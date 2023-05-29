<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/11/2018
 * Time: 11:28
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post;


use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostBotPreparer;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Utils;

class PostExcerptPreparer extends AbstractPostBotPreparer {

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public function prepare() {
        $postExcerptSelectors      = $this->bot->getSetting(SettingKey::POST_EXCERPT_SELECTORS, []);
        $findAndReplacesForExcerpt = $this->bot->prepareFindAndReplaces($this->bot->getSetting(SettingKey::POST_FIND_REPLACE_EXCERPT, null));

        foreach($postExcerptSelectors as $selectorData) {
            $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
            if (!$selector) continue;

            $attr = Utils::array_get($selectorData, SettingInnerKey::ATTRIBUTE);
            if (!$attr) $attr = 'html';

            $excerpt = $this->bot->extractData($this->bot->getCrawler(), $selector, $attr, "excerpt", true, true);
            if(!$excerpt || !is_array($excerpt)) continue;

            $excerpt["data"] = trim($this->bot->findAndReplace($findAndReplacesForExcerpt, $excerpt["data"]));
            $this->bot->getPostData()->setExcerpt($excerpt);

            break;
        }

    }
}
