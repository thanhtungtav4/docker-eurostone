<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/12/2018
 * Time: 20:40
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\WooCommerce\Adapter\Woo35;


use WC_Product_External;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Interfaces\ExternalProductAdapter;

class Woo35ExternalProductAdapter extends Woo35ProductAdapter implements ExternalProductAdapter {

    /**
     * Set product URL.
     *
     * @param string $product_url Product URL.
     * @since 1.8.0
     */
    public function set_product_url($product_url): void {
        $product = $this->getProduct();
        if (!($product instanceof WC_Product_External)) return;

        $product->set_product_url($product_url);
    }

    /**
     * Set button text.
     *
     * @param string $button_text Button text.
     * @since 1.8.0
     */
    public function set_button_text($button_text): void {
        $product = $this->getProduct();
        if (!($product instanceof WC_Product_External)) return;

        $product->set_button_text($button_text);
    }

}