<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/11/2018
 * Time: 11:15
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post\Base;


use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Crawling\Preparers\Interfaces\Preparer;

abstract class AbstractPostBotPreparer implements Preparer {

    /** @var PostBot */
    protected $bot;

    /**
     * @param PostBot $postBot
     */
    public function __construct(PostBot $postBot) {
        $this->bot = $postBot;
    }

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public abstract function prepare();

    /**
     * Get values for a selector setting. This applies the options box configurations as well.
     *
     * @param string            $settingName  Name of the setting from which the selector data will be retrieved
     * @param string            $defaultAttr  Attribute value that will be used if the attribute is not found in the
     *                                        settings
     * @param false|null|string $contentType  See {@link AbstractBot::extractData}
     * @param bool              $singleResult See {@link AbstractBot::extractData}
     * @param bool              $trim         See {@link AbstractBot::extractData}
     * @return mixed|null If there are no results, returns null. If $singleResult is true, returns a single result.
     *                    Otherwise, returns an array.
     * @see AbstractBot::extractValuesForSelectorSetting()
     */
    protected function getValuesForSelectorSetting(string $settingName, string $defaultAttr, $contentType = false,
                                                   bool $singleResult = false, bool $trim = true) {

        return $this->bot->extractValuesForSelectorSetting($this->bot->getCrawler(), $settingName, $defaultAttr, $contentType, $singleResult, $trim);
    }

    /**
     * @return PostBot
     */
    public function getBot(): PostBot {
        return $this->bot;
    }
}