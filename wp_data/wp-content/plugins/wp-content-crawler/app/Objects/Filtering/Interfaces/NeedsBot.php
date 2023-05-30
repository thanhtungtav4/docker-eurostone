<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/04/2020
 * Time: 12:07
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Interfaces;


use WPCCrawler\Objects\Crawling\Bot\AbstractBot;

interface NeedsBot {

    /**
     * @param AbstractBot|null $bot
     * @since 1.11.0
     */
    public function setBot(?AbstractBot $bot): void;

    /**
     * @return AbstractBot|null
     * @since 1.11.0
     */
    public function getBot(): ?AbstractBot;

}