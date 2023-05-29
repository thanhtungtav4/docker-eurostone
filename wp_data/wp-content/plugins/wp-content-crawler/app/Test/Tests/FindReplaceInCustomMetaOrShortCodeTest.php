<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/10/2018
 * Time: 07:54
 */

namespace WPCCrawler\Test\Tests;


use Exception;
use Illuminate\Contracts\View\View;
use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\OptionsBox\OptionsBoxService;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Traits\FindAndReplaceTrait;
use WPCCrawler\Test\Base\AbstractTest;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Utils;

class FindReplaceInCustomMetaOrShortCodeTest extends AbstractTest {

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
        $formItemValues = $data->getFormItemValues();
        if(!$formItemValues || !is_array($formItemValues)) return null;

        $key      = Utils::array_get($formItemValues, 'meta_key', Utils::array_get($formItemValues, 'short_code'));
        $url      = $this->getData()->get("url");
        $content  = $this->getData()->get("subject");
        $selector = $this->getData()->get("valueSelector");
        $attr     = $this->getData()->get("valueSelectorAttr", $this->getData()->get(SettingInnerKey::ATTRIBUTE));
        $find     = Utils::array_get($formItemValues, SettingInnerKey::FIND);
        $replace  = Utils::array_get($formItemValues, SettingInnerKey::REPLACE);
        $regex    = isset($formItemValues[SettingInnerKey::REGEX]);
        $optionsBoxApplier = OptionsBoxService::getInstance()->createApplierFromRawData($this->getData()->get("valueOptionsBoxData"));

        $results = [];

        // If there are a URL and a selector, get the content from that URL.
        if($url && $selector) {
            $this->url = $url;
            $bot = new PostBot($data->getPostSettings(), null, $data->getUseUtf8(), $data->getConvertEncodingToUtf8());
            $bot->setResponseCacheEnabled($data->isCacheTestUrlResponses());

            if($crawler = $bot->request($url, 'GET', $data->getRawHtmlFindReplaces())) {
                $this->isFromCache = $bot->isLatestResponseFromCache();

                $contents = $bot->extractData($crawler, [$selector], $attr ? $attr : 'text', null, false, true);
                if(is_array($contents) && $contents) {
                    // Apply options box settings if there are any
                    if ($optionsBoxApplier) $contents = $optionsBoxApplier->applyToArray($contents);

                    foreach($contents as $c) {
                        $results[] = $this->findAndReplaceSingle($find, $replace, $c, $regex);
                    }
                }

            }
        }

        // If there is a content, use it as well.
        if($content) {
            // Apply options box settings if there are any
            if ($optionsBoxApplier) $content = $optionsBoxApplier->apply($content);

            $results[] = $this->findAndReplaceSingle($find, $replace, $content, $regex);
        }

        $this->message = sprintf(_wpcc('Test results for %1$s %2$s %3$s %4$s %5$s %6$s %7$s'),
            sprintf('%1$s %2$s %3$s',
                $url && $selector   ? "<span class='highlight url'>" . $url . "</span>" : '',
                $url && $selector && $content ? _wpcc("and") : '',
                $content ? _wpcc("test code") : ''
            ),
            $key                ? "<span class='highlight key'>" . $key . "</span>"                             : '',
            $selector           ? "<span class='highlight selector'>" . $selector . "</span>"                   : '',
            $attr               ? "<span class='highlight attribute'>" . $attr . "</span>"                      : '',
            $find               ? "<span class='highlight find'>" . htmlspecialchars($find) . "</span>"         : '',
            $replace            ? "<span class='highlight replace'>" . htmlspecialchars($replace) . "</span>"   : '',
            $regex              ? _wpcc("(as regex)") : ''
        );

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