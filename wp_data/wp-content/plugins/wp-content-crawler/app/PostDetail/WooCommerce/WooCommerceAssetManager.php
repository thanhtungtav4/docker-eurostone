<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/12/2018
 * Time: 12:57
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\WooCommerce;


use WPCCrawler\Objects\AssetManager\BaseAssetManager;

/**
 * @since 1.8.0
 */
class WooCommerceAssetManager extends BaseAssetManager {

    /** @var WooCommerceAssetManager|null */
    private static $instance;

    /** @var string */
    private $styleSiteTester = 'wpcc_wc_site_tester_css';

    /**
     * Get the instance
     *
     * @return WooCommerceAssetManager
     * @since 1.11.1
     */
    public static function getInstance(): WooCommerceAssetManager {
        if (static::$instance === null) {
            static::$instance = new WooCommerceAssetManager();
        }

        return static::$instance;
    }

    /**
     * Add site tester assets.
     * @since 1.8.0
     */
    public function addTester(): void {
        $this->addStyle($this->styleSiteTester, $this->stylePath('post-detail/woocommerce/wc-site-tester.css'));
    }
}