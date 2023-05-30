<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/11/2018
 * Time: 11:13
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post;


use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostBotPreparer;
use WPCCrawler\Objects\Settings\Enums\SettingKey;

class PostSlugPreparer extends AbstractPostBotPreparer {

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public function prepare() {
        $slug = $this->getValuesForSelectorSetting(SettingKey::POST_SLUG_SELECTORS, 'text', false, true, true);
        if (!$slug) return;

        $this->bot->getPostData()->setSlug($slug);
    }
}
