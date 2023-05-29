<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/12/2018
 * Time: 20:43
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\WooCommerce\Adapter;


use WC_Data_Exception;
use WC_Product;
use WC_Product_Attribute;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Interfaces\ProductAdapter;

abstract class BaseProductAdapter implements ProductAdapter {

    /** @var WC_Product */
    private $product;

    /**
     * @param WC_Product $product
     */
    public function __construct($product) {
        $this->product = $product;
    }

    /*
     *
     */

    /**
     * @return int
     * @since 1.8.0
     */
    public function save() {
        return $this->getProduct()->save();
    }

    /**
     * @param WC_Product_Attribute[] $attributes
     * @since 1.8.0
     */
    public function set_attributes($attributes): void {
        $this->getProduct()->set_attributes($attributes);
    }

    /**
     * @return WC_Product_Attribute[]
     * @since 1.9.0
     */
    public function get_attributes() {
        return $this->getProduct()->get_attributes();
    }

    /**
     * @param bool|string $downloadable Whether product is downloadable or not.
     * @since 1.8.0
     */
    public function set_downloadable($downloadable): void {
        $this->getProduct()->set_downloadable($downloadable);
    }

    /**
     * Set download limit.
     *
     * @param int|string $download_limit Product download limit.
     * @since 1.8.0
     */
    public function set_download_limit($download_limit): void {
        $this->getProduct()->set_download_limit($download_limit);
    }

    /**
     * Set download expiry.
     *
     * @param int|string $download_expiry Product download expiry.
     * @since 1.8.0
     */
    public function set_download_expiry($download_expiry): void {
        $this->getProduct()->set_download_expiry($download_expiry);
    }

    /**
     * Set downloads.
     *
     * @param array $downloads_array Array of WC_Product_Download objects or arrays.
     * @since 1.8.0
     */
    public function set_downloads($downloads_array): void {
        $this->getProduct()->set_downloads($downloads_array);
    }

    /**
     * Set if reviews is allowed.
     *
     * @param bool $reviews_allowed Reviews allowed or not.
     * @since 1.8.0
     */
    public function set_reviews_allowed($reviews_allowed): void {
        $this->getProduct()->set_reviews_allowed($reviews_allowed);
    }

    /**
     * Set menu order.
     *
     * @param int $menu_order Menu order.
     * @since 1.8.0
     */
    public function set_menu_order($menu_order): void {
        $this->getProduct()->set_menu_order($menu_order);
    }

    /**
     * Set the product's active price.
     *
     * @param string $price Price.
     * @since 1.8.0
     */
    public function set_price($price): void {
        $this->getProduct()->set_price($price);
    }

    /**
     * Set the product's regular price.
     *
     * @param string $price Regular price.
     * @since 1.8.0
     */
    public function set_regular_price($price): void {
        $this->getProduct()->set_regular_price($price);
    }

    /**
     * Set the product's sale price.
     *
     * @param string $price sale price.
     * @since 1.8.0
     */
    public function set_sale_price($price): void {
        $this->getProduct()->set_sale_price($price);
    }

    /**
     * Set gallery attachment ids.
     *
     * @param array $image_ids List of image ids.
     * @since 1.8.0
     */
    public function set_gallery_image_ids($image_ids): void {
        $this->getProduct()->set_gallery_image_ids($image_ids);
    }

    /**
     * Returns the gallery attachment ids.
     *
     * @param string $context What the value is for. Valid values are view and edit.
     * @return array
     * @since 1.8.0
     */
    public function get_gallery_image_ids($context = 'view') {
        return $this->getProduct()->get_gallery_image_ids();
    }

    /**
     * Set purchase note.
     *
     * @param string $purchase_note Purchase note.
     * @since 1.8.0
     */
    public function set_purchase_note($purchase_note): void {
        $this->getProduct()->set_purchase_note($purchase_note);
    }

    /**
     * Set the product's weight.
     *
     * @param float|string $weight Total weight.
     * @since 1.8.0
     */
    public function set_weight($weight): void {
        $this->getProduct()->set_weight($weight);
    }

    /**
     * Set the product length.
     *
     * @param float|string $length Total length.
     * @since 1.8.0
     */
    public function set_length($length): void {
        $this->getProduct()->set_length($length);
    }

    /**
     * Set the product width.
     *
     * @param float|string $width Total width.
     * @since 1.8.0
     */
    public function set_width($width): void {
        $this->getProduct()->set_width($width);
    }

    /**
     * Set the product height.
     *
     * @param float|string $height Total height.
     * @since 1.8.0
     */
    public function set_height($height): void {
        $this->getProduct()->set_height($height);
    }

    /**
     * Set shipping class ID.
     *
     * @param int $id Product shipping class id.
     * @since 1.8.0
     */
    public function set_shipping_class_id($id): void {
        $this->getProduct()->set_shipping_class_id($id);
    }

    /**
     * Set if the product is virtual.
     *
     * @param bool|string $virtual Whether product is virtual or not.
     * @since 1.8.0
     */
    public function set_virtual($virtual): void {
        $this->getProduct()->set_virtual($virtual);
    }

    /**
     * Set SKU.
     *
     * @param string $sku Product SKU.
     * @throws WC_Data_Exception Throws exception when invalid data is found.
     * @since 1.8.0
     */
    public function set_sku($sku): void {
        $this->getProduct()->set_sku($sku);
    }

    /**
     * Set if should be sold individually.
     *
     * @param bool $sold_individually Whether or not product is sold individually.
     * @since 1.8.0
     */
    public function set_sold_individually($sold_individually): void {
        $this->getProduct()->set_sold_individually($sold_individually);
    }

    /**
     * Set if product manage stock.
     *
     * @param bool $manage_stock Whether or not manage stock is enabled.
     * @since 1.8.0
     */
    public function set_manage_stock($manage_stock): void {
        $this->getProduct()->set_manage_stock($manage_stock);
    }

    /**
     * Set number of items available for sale.
     *
     * @param float|null $quantity Stock quantity.
     * @since 1.8.0
     */
    public function set_stock_quantity($quantity): void {
        $this->getProduct()->set_stock_quantity($quantity);
    }

    /**
     * Set stock status.
     *
     * @param string $status New status.
     */
    public function set_stock_status($status = 'instock'): void {
        $this->getProduct()->set_stock_status($status);
    }

    /**
     * Set backorders.
     *
     * @param string $backorders Options: 'yes', 'no' or 'notify'.
     * @since 1.8.0
     */
    public function set_backorders($backorders): void {
        $this->getProduct()->set_backorders($backorders);
    }

    /**
     * Set the product tags.
     *
     * @param array $term_ids List of terms IDs.
     * @since 1.8.0
     */
    public function set_tag_ids($term_ids): void {
        $this->getProduct()->set_tag_ids($term_ids);
    }

    /**
     * Get the product tags.
     *
     * @return array List of terms IDs.
     * @since 1.8.0
     */
    public function get_tag_ids() {
        return $this->getProduct()->get_tag_ids();
    }

    /*
     *
     */

    /**
     * @return WC_Product
     * @since 1.8.0
     */
    public function getProduct() {
        return $this->product;
    }

}