<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/11/2018
 * Time: 08:59
 */

namespace WPCCrawler\PostDetail\Base;


use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Crawling\Bot\PostBot;

abstract class BasePostDetailPreparer {

    /** @var PostBot */
    protected $bot;

    /** @var BasePostDetailData */
    protected $detailData;

    /**
     * @param PostBot            $postBot
     * @param BasePostDetailData $detailData
     */
    public function __construct(PostBot $postBot, BasePostDetailData $detailData) {
        $this->bot = $postBot;
        $this->detailData = $detailData;
    }

    /**
     * @return void
     */
    abstract public function prepare();

    /*
     * HELPERS
     */

    /**
     * Get values for a selector setting
     *
     * @param string            $settingName  Name of the setting from which the selector data will be retrieved
     * @param string            $defaultAttr  Attribute value that will be used if the attribute is not found in the
     *                                        settings
     * @param false|null|string $contentType  See {@link AbstractBot::extractData}
     * @param bool              $singleResult See {@link AbstractBot::extractData}
     * @param bool              $trim         See {@link AbstractBot::extractData}
     * @return array|mixed|null    Returns what {@link AbstractBot::extractValuesForSelectorSetting()} returns.
     * @see AbstractBot::extractValuesForSelectorSetting()
     */
    protected function getValuesForSelectorSetting($settingName, $defaultAttr, $contentType = false,
                                                   $singleResult = false, $trim = true) {

        return $this->bot->extractValuesForSelectorSetting($this->bot->getCrawler(), $settingName, $defaultAttr, 
            $contentType, $singleResult, $trim);
    }

    /*
     * GETTERS
     */

    /**
     * @return PostBot
     */
    public function getBot() {
        return $this->bot;
    }

    /**
     * @return BasePostDetailData
     */
    public function getDetailData() {
        return $this->detailData;
    }

}