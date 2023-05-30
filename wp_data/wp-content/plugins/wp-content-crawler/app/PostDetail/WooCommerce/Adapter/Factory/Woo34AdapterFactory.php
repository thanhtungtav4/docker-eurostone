<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/12/2018
 * Time: 19:52
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\WooCommerce\Adapter\Factory;


use WC_Product_External;
use WC_Product_Simple;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Interfaces\ExternalProductAdapter;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Interfaces\SimpleProductAdapter;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Woo34\Woo34ExternalProductAdapter;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Woo34\Woo34SimpleProductAdapter;

class Woo34AdapterFactory extends BaseWooAdapterFactory {

    /**
     * Get minimum WooCommerce version that the adapters created by this factory apply.
     *
     * @return string Version, inclusive. E.g. if you write 3.5, this will be applicable for versions greater than or
     *                equal to 3.5
     * @since 1.8.0
     */
    public function getMinVersion(): string {
        return "3.4";
    }

    /**
     * Get maximum WooCommerce version that the adapters created by this factory apply.
     *
     * @return string|null Version, exclusive. E.g. if you write 3.5, this will be applicable for versions less than 3.5.
     *                     If this is null, it means this is applicable for all versions greater than min version defined
     *                     in {@link getMinVersion()}.
     * @since 1.8.0
     */
    public function getMaxVersion(): ?string {
        return "3.5";
    }

    /**
     * Create simple product adapter.
     *
     * @param WC_Product_Simple $simpleProduct
     * @return SimpleProductAdapter
     * @since 1.8.0
     */
    public function createSimpleProductAdapter($simpleProduct): SimpleProductAdapter {
        return new Woo34SimpleProductAdapter($simpleProduct);
    }

    /**
     * Create external product adapter.
     *
     * @param WC_Product_External $externalProduct
     * @return ExternalProductAdapter
     * @since 1.8.0
     */
    public function createExternalProductAdapter($externalProduct): ExternalProductAdapter {
        return new Woo34ExternalProductAdapter($externalProduct);
    }

}