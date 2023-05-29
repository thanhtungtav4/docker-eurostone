<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/10/2018
 * Time: 17:55
 */

namespace WPCCrawler\Test\Base;


use Exception;
use Illuminate\Contracts\View\View;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Traits\FindAndReplaceTrait;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Utils;

abstract class AbstractHtmlManipulationTest extends AbstractTest {

    use FindAndReplaceTrait;

    /** @var string|null */
    private $message;

    /** @var bool */
    private $isFromCache = false;

    /** @var string|null */
    private $url = null;

    /**
     * Get the last HTML manipulation step. See {@link applyHtmlManipulationOptions}
     *
     * @return null|int
     */
    protected abstract function getLastHtmlManipulationStep(): ?int;

    /**
     * Define instance variables.
     * @return void
     */
    protected abstract function defineVariables();

    /**
     * @return string
     */
    protected abstract function getMessageLastPart(): string;

    /**
     * Returns a manipulated {@link Crawler}. {@link AbstractBot} is the bot that is used to get the data from the
     * target URL and it can be used to manipulate the content.
     *
     * @param Crawler $crawler
     * @param AbstractBot $bot
     * @return Crawler
     */
    protected abstract function manipulate($crawler, $bot);

    /**
     * @param string|null $url
     * @param string|null $content
     * @param string|null $selector
     * @param string|null $messageLastPart
     * @param string|null $attr
     * @return array
     */
    protected function createHtmlManipulationResults($url, $content, $selector, $messageLastPart, $attr = null): array {
        $results = [];
        if($selector) {
            if($url || $content) {
                // Create a dummy bot to get the client.
                $bot = new DummyBot(
                    $this->getData()->getPostSettings(),
                    null,
                    $this->getData()->getUseUtf8(),
                    $this->getData()->getConvertEncodingToUtf8()
                );

                $bot->setResponseCacheEnabled($this->getData()->isCacheTestUrlResponses());

                if($url) {
                    $this->url = $url;
                    $crawler = $bot->request($url, 'GET', $this->getData()->getRawHtmlFindReplaces());
                    $this->isFromCache = $bot->isLatestResponseFromCache();

                    // Apply the manipulation options
                    $this->applyHtmlManipulationOptions($bot, $crawler, $this->getLastHtmlManipulationStep(), $url);

                    if($crawler) $this->manipulateAndAddResults($crawler, $bot, $results, $selector,$attr);
                }

                if($content) {
                    // Remove html, body and head tags
                    $content = str_replace(['</html>', '</body>', '</head>'], '', $content);
                    $regexFormat = '<%1$s>|<%1$s\s[^>]+>';
                    $content = $this->findAndReplaceSingle(sprintf($regexFormat, 'html'), '', $content, true);
                    $content = $this->findAndReplaceSingle(sprintf($regexFormat, 'body'), '', $content, true);
                    $content = $this->findAndReplaceSingle(sprintf($regexFormat, 'head'), '', $content, true);

                    // Apply raw find-replaces. It is safe to directly apply here, without checking the last manipulation
                    // step, because FindReplaceInRawHtmlTest does not extend to this class. So, the raw HTML find-replaces
                    // must be applied for every AbstractHtmlManipulationTest when there is a test code
                    $content = $bot->findAndReplace($this->getData()->getRawHtmlFindReplaces(), $content, false);

                    // Create a dummy crawler
                    $dummyCrawler = $bot->createDummyCrawler($content);

                    // Apply the manipulation options
                    $this->applyHtmlManipulationOptions($bot, $dummyCrawler, $this->getLastHtmlManipulationStep(), "http://site.com/");

                    if($dummyCrawler) $this->manipulateAndAddResults($dummyCrawler, $bot, $results, $selector,$attr);
                }

                $message = sprintf(
                    _wpcc('Test results for %1$s with %2$s'),
                    sprintf('%1$s %2$s %3$s',
                        $url ? "<span class='highlight url'>" . $url . "</span>" : '',
                        $url && $content ? _wpcc("and") : '',
                        $content ? _wpcc("test code") : ''
                    ),
                    $messageLastPart ?: ''
                );

                // Remove unnecessary spaces
                $message = $this->findAndReplaceSingle('\s{2,}', ' ', $message, true);

            } else {
                $message = _wpcc("URL and/or content must exist to conduct the test.");
            }

        } else {
            $message = _wpcc("You must provide a valid CSS selector.");
        }

        $this->message = $message;

        return $results;
    }

    /**
     * @param Crawler     $crawler
     * @param AbstractBot $bot
     * @param array       $results
     * @param string      $selector
     * @param string      $attr
     */
    private function manipulateAndAddResults($crawler, $bot, &$results, &$selector, &$attr): void {
        $crawler = $this->manipulate($crawler, $bot);
        $this->addResults($crawler, $results, $selector, $attr);
    }

    /**
     * @param Crawler     $crawler
     * @param array       $results
     * @param string      $selector
     * @param string|null $attr
     */
    protected function addResults($crawler, &$results, &$selector, &$attr): void {
        try {
            $crawler->filter($selector)->each(function ($node) use (&$results, &$attr) {
                /** @var Crawler $node */
                $result = $attr ? $node->attr($attr) : Utils::getNodeHTML($node);
                if($result) $results[] = $result;
            });

        } catch(Exception $e) {
            Informer::addError($selector . " - " . $e->getMessage())->setException($e)->addAsLog();
        }
    }

    /**
     * @param TestData $data
     * @return array|null
     */
    protected function createResults($data): ?array {
        if(!$this->getData()->getFormItemValues() || !is_array($this->getData()->getFormItemValues())) return null;
        $this->defineVariables();
        return $this->onCreateResults($data);
    }

    /**
     * @param TestData $data
     * @return array|null The manipulation results
     * @since 1.11.1
     */
    abstract protected function onCreateResults(TestData $data): ?array;
    
    /**
     * @return View
     * @throws Exception
     */
    protected function createView() {
        return Utils::view('partials/test-result')
            ->with("results", $this->getResults())
            ->with("message", $this->message ?: '')
            ->with("isResponseFromCache", $this->isFromCache)
            ->with("testUrl", $this->url ?: '');
    }


}