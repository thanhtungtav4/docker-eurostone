<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/10/2018
 * Time: 08:47
 */

namespace WPCCrawler\Test\Tests;


use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Test\Base\AbstractTest;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Test\Test;
use WPCCrawler\Utils;

class SelectorTest extends AbstractTest {

    /** @var int */
    private $maxTestItem = 1000000;

    /** @var string|null */
    private $message;

    /** @var bool */
    private $isFromCache = false;

    /** @var string|null */
    private $url = null;

    /**
     * Conduct the test and return an array of results.
     *
     * @param TestData $data Information required for the test
     * @return array
     */
    protected function createResults($data): array {
        // Here, form item values must exist.
        $formItemValues = $data->getFormItemValues();
        if(!$formItemValues) return [];

        // If form item values is not an array, it means it is a string and that string is actually the selector.
        // So, prepare it that way.
        if(!is_array($formItemValues)) {
            $formItemValues = [SettingInnerKey::SELECTOR => $formItemValues];
        }

        $attr = Utils::array_get($formItemValues, SettingInnerKey::ATTRIBUTE);
        if(!$attr) $attr = $data->get(SettingInnerKey::ATTRIBUTE);

        /*
         *
         */

        $url        = $data->get("url");
        $selector   = Utils::array_get($formItemValues, SettingInnerKey::SELECTOR);
        $testType   = $data->getTestType();
        $content    = $data->get("content");

        /*
         *
         */

        // Create a dummy bot to get the client.
        $bot = new DummyBot($data->getPostSettings(), null, $data->getUseUtf8(), $data->getConvertEncodingToUtf8());
        $bot->setResponseCacheEnabled($data->isCacheTestUrlResponses());

        if(!$content) {
            $crawler = $bot->request($url, 'GET', $data->getRawHtmlFindReplaces());
            $this->isFromCache = $bot->isLatestResponseFromCache();

            // If the form item name contains 'unnecessary', it means the user is testing if the unnecessary element
            // selectors are working. So, in this case, do not remove unnecessary elements from the crawler so that the
            // user can see whether the selectors work or not.
            $lastStep = Str::contains($data->getFormItemName() ?: '', 'unnecessary') 
                ? AbstractTest::MANIPULATION_STEP_FIND_REPLACE_ELEMENT_HTML 
                : null;
            $this->applyHtmlManipulationOptions($bot, $crawler, $lastStep, $url);
        } else {
            $crawler = new Crawler($content);
        }

        if(!$testType) $testType = Test::$TEST_TYPE_HTML;

        $results = [];

        if ($crawler && $selector) {
            $abort = false;
            try {
                $crawler->filter($selector)->each(function ($node, $i) use ($testType, $attr, &$results, $crawler, &$abort) {
                    if ($abort) return;

                    /** @var Crawler $node */
                    if ($i >= $this->maxTestItem) return;

                    $result = false;
                    try {
                        switch ($testType) {
                            case Test::$TEST_TYPE_HREF:
                                $result = $node->attr("href");
                                break;

                            case Test::$TEST_TYPE_HTML:
                                $result = Utils::getNodeHTML($node);
                                break;

                            case Test::$TEST_TYPE_TEXT:
                                $result = $node->text();
                                break;

                            case Test::$TEST_TYPE_SRC:
                                $result = $node->attr("src");
                                break;

                            case Test::$TEST_TYPE_FIRST_POSITION:
                                $nodeHtml = Utils::getNodeHTML($node);
                                $result = $nodeHtml ? mb_strpos($crawler->html(), $nodeHtml) : false;
                                break;

                            case Test::$TEST_TYPE_SELECTOR_ATTRIBUTE:
                                if ($attr) {
                                    switch ($attr) {
                                        case "text":
                                            $result = $node->text();
                                            break;
                                        case "html":
                                            $result = Utils::getNodeHTML($node);
                                            break;
                                        default:
                                            $result = $node->attr($attr);
                                            break;
                                    }
                                }
                                break;
                        }

                    } catch (InvalidArgumentException $e) {
                        Informer::addError($e->getMessage())->setException($e)->addAsLog();
                    }

                    if ($result) {
                        if ($testType == Test::$TEST_TYPE_FIRST_POSITION) {
                            $results[] = Utils::getNodeHTML($node); // Add html of the node for a meaningful result
                            $results[] = $result;
                            $abort = true;
                        } else if ($result = trim((string) $result)) {
                            $results[] = $result;
                        }
                    }

                });

            } catch (Exception $e) {
                Informer::addError($selector . " - " . $e->getMessage())->setException($e)->addAsLog();
            }
        }

        $this->message = sprintf(
            _wpcc('Test results for %1$s%2$s on %3$s:'),
            "<span class='highlight selector'>" . $selector . "</span>",
            $attr   ? " <span class='highlight attribute'>" . $attr . "</span> "    : '',
            $url    ? "<span class='highlight url'>" . $url . "</span>"             : ''
        );

        $this->url = $url;

        return $results;
    }

    /**
     * Create the view of the response
     *
     * @return View|null
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