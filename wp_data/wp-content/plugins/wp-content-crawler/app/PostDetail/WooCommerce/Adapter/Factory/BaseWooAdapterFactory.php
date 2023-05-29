<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/12/2018
 * Time: 19:51
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\WooCommerce\Adapter\Factory;


use Exception;
use WC_Product;
use WC_Product_External;
use WC_Product_Simple;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Interfaces\ExternalProductAdapter;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Interfaces\ProductAdapter;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Interfaces\SimpleProductAdapter;

abstract class BaseWooAdapterFactory {

    /** @var BaseWooAdapterFactory[] */
    protected static $instances = [];

    /**
     * Get the instance.
     *
     * @return BaseWooAdapterFactory
     * @since 1.8.0
     */
    public static function getInstance(): BaseWooAdapterFactory {
        $clz = get_called_class();
        if (!isset(static::$instances[$clz])) {
            static::$instances[$clz] = new static();
        }

        return static::$instances[$clz];
    }

    /** This is a singleton. */
    final protected function __construct() {}

    /**
     * Get minimum WooCommerce version that the adapters created by this factory apply.
     *
     * @return string Version, inclusive. E.g. if you write 3.5, this will be applicable for versions greater than or
     *                equal to 3.5
     * @since 1.8.0
     */
    public abstract function getMinVersion(): string;

    /**
     * Get maximum WooCommerce version that the adapters created by this factory apply.
     *
     * @return string|null Version, exclusive. E.g. if you write 3.5, this will be applicable for versions less than 3.5.
     *                     If this is null, it means this is applicable for all versions greater than min version defined
     *                     in {@link getMinVersion()}.
     * @since 1.8.0
     */
    public abstract function getMaxVersion(): ?string;

    /**
     * Create simple product adapter.
     *
     * @param WC_Product_Simple $simpleProduct
     * @return SimpleProductAdapter
     * @since 1.8.0
     */
    public abstract function createSimpleProductAdapter($simpleProduct): SimpleProductAdapter;

    /**
     * Create external product adapter.
     *
     * @param WC_Product_External $externalProduct
     * @return ExternalProductAdapter
     * @since 1.8.0
     */
    public abstract function createExternalProductAdapter($externalProduct): ExternalProductAdapter;

    /**
     * Create a product adapter.
     *
     * @param WC_Product $product The product for which an adapter will be created.
     * @return ExternalProductAdapter|SimpleProductAdapter
     * @throws Exception If there is no adapter for the class of the supplied product.
     * @since 1.8.0
     */
    public function createAdapterForProduct($product): ProductAdapter {
        switch (get_class($product)) {
            case WC_Product_Simple::class:
                /** @var WC_Product_Simple $product */
                return $this->createSimpleProductAdapter($product);

            case WC_Product_External::class:
                /** @var WC_Product_External $product */
                return $this->createExternalProductAdapter($product);

            default:
                throw new Exception(sprintf(_wpcc('An adapter for product class %1$s does not exist.'), get_class($product)));
        }
    }

}