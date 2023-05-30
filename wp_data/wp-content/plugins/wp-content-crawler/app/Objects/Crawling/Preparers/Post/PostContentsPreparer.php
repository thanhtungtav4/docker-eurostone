<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/11/2018
 * Time: 11:31
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post;


use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostBotPreparer;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Utils;

class PostContentsPreparer extends AbstractPostBotPreparer {

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public function prepare() {
        $postContentSelectors = $this->bot->getSetting(SettingKey::POST_CONTENT_SELECTORS, []);

        $allContents = [];
        foreach($postContentSelectors as $selectorData) {
            $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
            if (!$selector) continue;

            $attr = Utils::array_get($selectorData, SettingInnerKey::ATTRIBUTE);
            if (!$attr) $attr = 'html';

            $contents = $this->bot->extractData($this->bot->getCrawler(), $selector, $attr, "content", false, true);
            if(!$contents || !is_array($contents)) continue;

            $contents = Utils::array_msort($contents, ['start' => SORT_ASC]);

            $allContents = array_merge($allContents, $contents);
        }

        $this->bot->getPostData()->setContents($allContents);
    }

}
