<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/10/2018
 * Time: 17:45
 */

namespace WPCCrawler\Test\Tests\HtmlManipulation;


use Exception;
use Illuminate\Contracts\View\View;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Traits\FindAndReplaceTrait;
use WPCCrawler\Test\Base\AbstractTest;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Utils;

class FindReplaceInRawHtmlTest extends AbstractTest {

    use FindAndReplaceTrait;

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
     * @return array|null
     */
    protected function createResults($data): ?array {
        // Here, form item values must be an array.
        $formItemValues = $data->getFormItemValues();
        if(!$formItemValues || !is_array($formItemValues)) return null;

        $url        = $data->get("url");
        $content    = $data->get("subject");
        $find       = Utils::array_get($formItemValues, SettingInnerKey::FIND);
        $replace    = Utils::array_get($formItemValues, SettingInnerKey::REPLACE);
        $regex      = isset($formItemValues[SettingInnerKey::REGEX]);

        // Create the message
        $message = sprintf(_wpcc('Test result for find %1$s and replace with %2$s'),
            "<span class='highlight find'>" . htmlspecialchars($find) . "</span>",
            "<span class='highlight replace'>" . htmlspecialchars($replace) . "</span>");
        if($regex) $message .= " " . _wpcc("(as regex)");
        $message .= ':';
        if($url) $message .= "<span class='highlight url'>{$url}</span>" . ($content ? ' & ' : '');
        if($content) {
            $escapedContent = htmlspecialchars($content);
            $message .= '"' . (mb_strlen($escapedContent) > 50 ? mb_substr($escapedContent, 0, 49) . '...' : $escapedContent) . '"';
        }

        $bot = new PostBot($data->getPostSettings(), null, $data->getUseUtf8(), $data->getConvertEncodingToUtf8());
        $bot->setResponseCacheEnabled($data->isCacheTestUrlResponses());

        $results = [];
        $addResults = function($title, $content, $crawlerBefore, $crawlerAfter, $crawlerWithAllChangesApplied) use (&$results) {
            $results[$title] = [
                _wpcc('Crawler HTML with <span>current</span> find-replace applied') => Utils::getNodeHTML($crawlerAfter),
                _wpcc('<span>Raw</span> content') => $content,
                _wpcc('Crawler HTML with <span>no</span> find-replace applied') => Utils::getNodeHTML($crawlerBefore),
                _wpcc('Crawler HTML with <span>all</span> raw HTML find-replaces applied') => Utils::getNodeHTML($crawlerWithAllChangesApplied),
            ];
        };

        // Handle the URL response
        if ($url) {
            $this->url = $url;

            // Try to get the crawler before making any changes to the raw response
            $crawlerBefore = $bot->request($url, 'GET');
            $this->isFromCache = $bot->isLatestResponseFromCache();

            // Get the response content
            $responseContent = $bot->getLatestResponseContent();

            // Find and replace in the response content
            $modifiedResponseContent = $responseContent !== null 
                ? $this->findAndReplaceSingle($find, $replace, $responseContent, $regex) 
                : null;

            // Try to create the crawler with the modified response content
            try {
                $crawlerAfter = new Crawler($modifiedResponseContent);
            } catch(Exception $e) {
                $crawlerAfter = null;
            }

            // Try to create a crawler by applying all find-replace options for raw HTML
            $responseContentWithAllChangesApplied = $responseContent !== null 
                ? $this->findAndReplace($data->getRawHtmlFindReplaces() ?: [], $responseContent) 
                : null;
            try {
                $crawlerWithAllChangesApplied = new Crawler($responseContentWithAllChangesApplied);
            } catch(Exception $e) {
                $crawlerWithAllChangesApplied = null;
            }

            call_user_func($addResults, _wpcc('For the URL'), $responseContent, $crawlerBefore, $crawlerAfter, $crawlerWithAllChangesApplied);
        }

        // Handle the content
        if ($content) {
            // Get if the content contains HTML tag
            $containsHtmlTag = strpos($content, '<html') !== false;

            try {
                // If the content has HTML tag in it, try to create a crawler directly
                // Otherwise, create a crawler by adding necessary HTML tags
                $crawlerBefore = $containsHtmlTag ? new Crawler($content) : $bot->createDummyCrawler($content);
            } catch(Exception $e) {
                $crawlerBefore = null;
            }

            // Apply current find-replace options
            $modifiedContent = $this->findAndReplaceSingle($find, $replace, $content, $regex);

            // Try to create the crawler with the modified content
            try {
                $crawlerAfter = $containsHtmlTag ? new Crawler($modifiedContent) : $bot->createDummyCrawler($modifiedContent);
            } catch(Exception $e) {
                $crawlerAfter = null;
            }

            // Try to create a crawler by applying all find-replace options for raw HTML
            $contentWithAllChangesApplied = $this->findAndReplace($data->getRawHtmlFindReplaces(), $content);
            try {
                $crawlerWithAllChangesApplied = $containsHtmlTag ? new Crawler($contentWithAllChangesApplied) : $bot->createDummyCrawler($contentWithAllChangesApplied);
            } catch(Exception $e) {
                $crawlerWithAllChangesApplied = null;
            }

            call_user_func($addResults, _wpcc('For the test code'), $content, $crawlerBefore, $crawlerAfter, $crawlerWithAllChangesApplied);
        }

        $this->message = $message;

        return $results;
    }

    /**
     * Create the view of the response
     *
     * @return View|null
     * @throws Exception
     */
    protected function createView() {
        return Utils::view('partials.test-result-find-replace-raw-html')
            ->with('results', $this->getResults())
            ->with('message', $this->message ?: '')
            ->with("isResponseFromCache", $this->isFromCache)
            ->with("testUrl", $this->url ?: '');
    }
}