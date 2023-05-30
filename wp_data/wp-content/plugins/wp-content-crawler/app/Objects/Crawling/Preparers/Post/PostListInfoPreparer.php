<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/11/2018
 * Time: 12:32
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post;


use Exception;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostBotPreparer;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Utils;

class PostListInfoPreparer extends AbstractPostBotPreparer {

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public function prepare() {
        $postIsListType = $this->bot->getSetting(SettingKey::POST_IS_LIST_TYPE);
        if(!$postIsListType) return;

        $crawler  = $this->bot->getCrawler();
        $postData = $this->bot->getPostData();
        if (!$crawler) return;

        $postListItemsStartAfterSelectors   = $this->bot->getSetting(SettingKey::POST_LIST_ITEM_STARTS_AFTER_SELECTORS, []);
        $postListNumberSelectors            = $this->bot->getSetting(SettingKey::POST_LIST_ITEM_NUMBER_SELECTORS, []);
        $postListTitleSelectors             = $this->bot->getSetting(SettingKey::POST_LIST_TITLE_SELECTORS, []);
        $postListContentSelectors           = $this->bot->getSetting(SettingKey::POST_LIST_CONTENT_SELECTORS, []);
        $findAndReplaces                    = $this->bot->prepareFindAndReplaces([]);

        $listNumbers = $listTitles = $listContents = [];
        $listStartPos = 0;

        // Get the position after which the list items start
        if(!empty($postListItemsStartAfterSelectors)) {
            foreach($postListItemsStartAfterSelectors as $selectorData) {
                $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
                if (!$selector) continue;

                /** @var Crawler $node */
                try {
                    $node = $crawler->filter($selector)->first();

                    try {
                        $nodeHtml = Utils::getNodeHTML($node);
                        $pos = $nodeHtml ? mb_strpos($crawler->html(), $nodeHtml) : 0;
                        if ($pos > $listStartPos) $listStartPos = $pos;
                    } catch(InvalidArgumentException $e) {}

                } catch(Exception $e) {
                    Informer::addError("{$selector} - " . $e->getMessage())->setException($e)->addAsLog();
                }
            }

            $postData->setListStartPos($listStartPos);
        }

        // Get item numbers
        foreach($postListNumberSelectors as $selectorData) {
            $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
            if (!$selector) continue;

            $attr = Utils::array_get($selectorData, SettingInnerKey::ATTRIBUTE);
            if (!$attr) $attr = 'text';

            if ($listNumbers = $this->bot->extractData($crawler, $selector, $attr, "list_number", false, true)) {
                if ($listStartPos) $this->bot->removeItemsBeforePos($listNumbers, $listStartPos);

                $postData->setListNumbers($listNumbers);
                break;
            }
        }

        // Get titles
        foreach($postListTitleSelectors as $selectorData) {
            $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
            if (!$selector) continue;

            $attr = Utils::array_get($selectorData, SettingInnerKey::ATTRIBUTE);
            if (!$attr) $attr = 'text';

            if ($listTitles = $this->bot->extractData($crawler, $selector, $attr, "list_title", false, true)) {
                if ($listStartPos) $this->bot->removeItemsBeforePos($listTitles, $listStartPos);

                $postData->setListTitles($listTitles);
                break;
            }
        }

        // Get contents
        $allListContents = [];
        foreach($postListContentSelectors as $selectorData) {
            $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
            if (!$selector) continue;

            $attr = Utils::array_get($selectorData, SettingInnerKey::ATTRIBUTE);
            if (!$attr) $attr = 'html';

            if ($listContents = $this->bot->extractData($crawler, $selector, $attr, "list_content", false, true)) {
                if ($listStartPos) $this->bot->removeItemsBeforePos($listContents, $listStartPos);

                // Apply find-and-replaces
                $listContents = $this->modifyArrayValue($listContents, 'data', function ($val) use (&$findAndReplaces) {
                    return $this->bot->findAndReplace($findAndReplaces, $val);
                });

                $allListContents = array_merge($allListContents, $listContents);
            }
        }
        $listContents = $allListContents;
        $postData->setListContents($listContents);

        // Remove the list content from main content
        if($listStartPos > 0 && $contents = $postData->getContents()) {
            // Find start and end pos of the list
            $this->bot->combinedListData = Utils::combineArrays($this->bot->combinedListData, $listNumbers, $listTitles, $listContents);

            $startPos = $endPos = 0;
            foreach($this->bot->combinedListData as $listData) {
                if(!$startPos || (isset($listData["start"]) && $listData["start"] < $startPos)) {
                    $startPos = $listData["start"];
                }

                if(!$endPos || (isset($listData["end"]) && $listData["end"] > $endPos)) {
                    $endPos = $listData["end"];
                }
            }

            // If start and end positions are valid, remove the content between these positions
            if($startPos && $endPos) {
                foreach($contents as $key => $mContent) {
                    if(isset($mContent["start"]) && $mContent["start"] > $startPos &&
                        isset($mContent["end"]) && $mContent["end"] < $endPos) {
                        unset($contents[$key]);
                    }
                }
            }

            $postData->setContents($contents);
        }

    }

    /**
     * Modify a value in each inner array of an array.
     *
     * @param array    $array    An array of arrays. E.g. <i>[ ['data' => 'a'], ['data' => 'b'] ]</i>
     * @param string   $key      Inner array key whose value should be modified. E.g. <i>'data'</i>
     * @param callable $callback Called for each inner array. func($val) {return $modifiedVal; }. $val is, e.g., the
     *                           value of 'data'. This should return the modified value.
     * @return array             Modified array
     */
    private function modifyArrayValue($array, $key, $callback) {
        if(!is_callable($callback)) return $array;

        $preparedArray = [];
        foreach($array as $data) {
            if(isset($data[$key])) {
                $data[$key] = $callback($data[$key]);
            }

            $preparedArray[] = $data;
        }

        return $preparedArray;
    }
}
