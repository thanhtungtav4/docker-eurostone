<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/12/2018
 * Time: 20:39
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\WooCommerce\Adapter\Woo35;


use WPCCrawler\PostDetail\WooCommerce\Adapter\BaseProductAdapter;

abstract class Woo35ProductAdapter extends BaseProductAdapter {

    /**
     * Set low stock amount.
     *
     * @param int|string $amount Empty string if value not set.
     * @since 1.8.0
     */
    public function set_low_stock_amount($amount): void {
        $this->getProduct()->set_low_stock_amount($amount);
    }

}