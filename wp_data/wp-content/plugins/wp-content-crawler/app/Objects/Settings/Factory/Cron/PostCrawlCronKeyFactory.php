<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/10/19
 * Time: 20:00
 */

namespace WPCCrawler\Objects\Settings\Factory\Cron;


use WPCCrawler\Objects\Settings\Enums\SettingKey;

class PostCrawlCronKeyFactory extends AbstractCronKeyFactory {

    /** @var AbstractCronKeyFactory|null */
    private static $instance = null;

    public static function getInstance(): AbstractCronKeyFactory {
        if (static::$instance === null) {
            static::$instance = new PostCrawlCronKeyFactory();
        }
        
        return static::$instance;
    }

    /**
     * @return string The setting key storing last crawled URL ID
     */
    public function getLastCrawledUrlIdKey(): string {
        return SettingKey::CRON_LAST_CRAWLED_URL_ID;
    }

    /**
     * @return string The setting key storing next page URL of the post
     */
    public function getPostNextPageUrlKey(): string {
        return SettingKey::CRON_POST_NEXT_PAGE_URL;
    }

    /**
     * @return string The setting key storing the next page URLs of the post
     */
    public function getPostNextPageUrlsKey(): string {
        return SettingKey::CRON_POST_NEXT_PAGE_URLS;
    }

    /**
     * @return string The setting key storing the draft post ID
     */
    public function getPostDraftIdKey(): string {
        return SettingKey::CRON_POST_DRAFT_ID;
    }

    /**
     * @return string The setting key storing the last time at which the post was crawled
     */
    public function getLastCrawledAtKey(): string {
        return SettingKey::CRON_LAST_CRAWLED_AT;
    }
}