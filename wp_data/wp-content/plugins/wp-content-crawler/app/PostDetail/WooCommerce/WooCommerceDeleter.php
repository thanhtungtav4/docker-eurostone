<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 06/12/2018
 * Time: 15:16
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\WooCommerce;


use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\PostDetail\Base\BasePostDetailData;
use WPCCrawler\PostDetail\Base\BasePostDetailDeleter;
use WPCCrawler\Objects\Crawling\Data\PostSaverData;

class WooCommerceDeleter extends BasePostDetailDeleter {

    /**
     * Delete the information this post detail is interested in.
     *
     * @param SettingsImpl       $postSettings
     * @param BasePostDetailData $detailData
     * @param PostSaverData|null $saverData
     * @return mixed|void
     * @since 1.8.0
     */
    public function delete(SettingsImpl $postSettings, BasePostDetailData $detailData, $saverData) {
        /** @var WooCommerceData $detailData */

    }
}