<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 06/12/2018
 * Time: 15:14
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\Base;


use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Crawling\Data\PostSaverData;

abstract class BasePostDetailDeleter {

    /**
     * Delete the information this post detail is interested in.
     *
     * @param SettingsImpl       $postSettings
     * @param BasePostDetailData $detailData
     * @param PostSaverData|null $saverData
     * @return mixed|void
     * @since 1.8.0
     */
    abstract public function delete(SettingsImpl $postSettings, BasePostDetailData $detailData, $saverData);

}