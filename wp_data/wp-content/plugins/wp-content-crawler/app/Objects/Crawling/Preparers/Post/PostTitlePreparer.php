<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/11/2018
 * Time: 11:14
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post;


use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostBotPreparer;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Utils;

class PostTitlePreparer extends AbstractPostBotPreparer {

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public function prepare() {
        $postTitleSelectors      = $this->bot->getSetting(SettingKey::POST_TITLE_SELECTORS, []);
        $findAndReplacesForTitle = $this->bot->prepareFindAndReplaces($this->bot->getSetting(SettingKey::POST_FIND_REPLACE_TITLE, null));

        foreach($postTitleSelectors as $selectorData) {
            $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
            if (!$selector) continue;

            $attr = Utils::array_get($selectorData, SettingInnerKey::ATTRIBUTE);
            if (!$attr) $attr = 'text';

            $title = $this->bot->extractData($this->bot->getCrawler(), $selector, $attr, false, true, true);
            if($title && is_string($title)) {
                $title = $this->bot->findAndReplace($findAndReplacesForTitle, $title);

                $this->bot->getPostData()->setTitle($title);
                break;
            }
        }

    }

}
