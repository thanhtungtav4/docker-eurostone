<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/10/19
 * Time: 20:00
 */

namespace WPCCrawler\Objects\Settings\Factory\Cron;


abstract class AbstractCronKeyFactory {

    /**
     * Get the instance of this class
     *
     * @return AbstractCronKeyFactory
     */
    abstract public static function getInstance(): AbstractCronKeyFactory;

    /**
     * This is a singleton
     */
    protected function __construct() { }

    /**
     * @return string The setting key storing last crawled URL ID
     */
    abstract public function getLastCrawledUrlIdKey(): string;

    /**
     * @return string The setting key storing next page URL of the post
     */
    abstract public function getPostNextPageUrlKey(): string;

    /**
     * @return string The setting key storing the next page URLs of the post
     */
    abstract public function getPostNextPageUrlsKey(): string;

    /**
     * @return string The setting key storing the draft post ID
     */
    abstract public function getPostDraftIdKey(): string;

    /**
     * @return string The setting key storing the last time at which the post was crawled
     */
    abstract public function getLastCrawledAtKey(): string;


}