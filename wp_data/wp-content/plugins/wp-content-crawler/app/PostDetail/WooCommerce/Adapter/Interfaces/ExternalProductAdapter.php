<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/12/2018
 * Time: 20:38
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\WooCommerce\Adapter\Interfaces;


interface ExternalProductAdapter extends ProductAdapter {

    /**
     * Set product URL.
     *
     * @param string $product_url Product URL.
     * @since 1.8.0
     */
    public function set_product_url($product_url): void;

    /**
     * Set button text.
     *
     * @param string $button_text Button text.
     * @since 1.8.0
     */
    public function set_button_text($button_text): void;

}