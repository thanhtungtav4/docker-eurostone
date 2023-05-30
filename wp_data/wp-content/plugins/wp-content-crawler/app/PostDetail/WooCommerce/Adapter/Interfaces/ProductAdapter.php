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


use WC_Data_Exception;
use WC_Product;
use WC_Product_Attribute;

interface ProductAdapter {

    /**
     * @return int
     * @since 1.8.0
     */
    public function save();

    /**
     * @param WC_Product_Attribute[] $attributes
     * @since 1.8.0
     */
    public function set_attributes($attributes): void;

    /**
     * @return WC_Product_Attribute[]
     * @since 1.8.0
     */
    public function get_attributes();

    /**
     * @param bool|string $downloadable Whether product is downloadable or not.
     * @since 1.8.0
     */
    public function set_downloadable($downloadable): void;

    /**
     * Set download limit.
     *
     * @param int|string $download_limit Product download limit.
     * @since 1.8.0
     */
    public function set_download_limit($download_limit): void;

    /**
     * Set download expiry.
     *
     * @param int|string $download_expiry Product download expiry.
     * @since 1.8.0
     */
    public function set_download_expiry($download_expiry): void;

    /**
     * Set downloads.
     *
     * @param array $downloads_array Array of WC_Product_Download objects or arrays.
     * @since 1.8.0
     */
    public function set_downloads($downloads_array): void;

    /**
     * Set if reviews is allowed.
     *
     * @param bool $reviews_allowed Reviews allowed or not.
     * @since 1.8.0
     */
    public function set_reviews_allowed($reviews_allowed): void;

    /**
     * Set menu order.
     *
     * @param int $menu_order Menu order.
     * @since 1.8.0
     */
    public function set_menu_order($menu_order): void;

    /**
     * Set the product's active price.
     *
     * @param string $price Price.
     * @since 1.8.0
     */
    public function set_price($price): void;

    /**
     * Set the product's regular price.
     *
     * @param string $price Regular price.
     * @since 1.8.0
     */
    public function set_regular_price($price): void;

    /**
     * Set the product's sale price.
     *
     * @param string $price sale price.
     * @since 1.8.0
     */
    public function set_sale_price($price): void;

    /**
     * Set gallery attachment ids.
     *
     * @param array $image_ids List of image ids.
     * @since 1.8.0
     */
    public function set_gallery_image_ids($image_ids): void;

    /**
     * Returns the gallery attachment ids.
     *
     * @param string $context What the value is for. Valid values are view and edit.
     * @return array
     * @since 1.8.0
     */
    public function get_gallery_image_ids($context = 'view');

    /**
     * Set purchase note.
     *
     * @param string $purchase_note Purchase note.
     * @since 1.8.0
     */
    public function set_purchase_note($purchase_note): void;

    /**
     * Set the product's weight.
     *
     * @param float|string $weight Total weight.
     * @since 1.8.0
     */
    public function set_weight($weight): void;

    /**
     * Set the product length.
     *
     * @param float|string $length Total length.
     * @since 1.8.0
     */
    public function set_length($length): void;

    /**
     * Set the product width.
     *
     * @param float|string $width Total width.
     * @since 1.8.0
     */
    public function set_width($width): void;

    /**
     * Set the product height.
     *
     * @param float|string $height Total height.
     * @since 1.8.0
     */
    public function set_height($height): void;

    /**
     * Set shipping class ID.
     *
     * @param int $id Product shipping class id.
     * @since 1.8.0
     */
    public function set_shipping_class_id($id): void;

    /**
     * Set if the product is virtual.
     *
     * @param bool|string $virtual Whether product is virtual or not.
     * @since 1.8.0
     */
    public function set_virtual($virtual): void;

    /**
     * Set SKU.
     *
     * @param string $sku Product SKU.
     * @throws WC_Data_Exception Throws exception when invalid data is found.
     * @since 1.8.0
     */
    public function set_sku($sku): void;

    /**
     * Set if should be sold individually.
     *
     * @param bool $sold_individually Whether or not product is sold individually.
     * @since 1.8.0
     */
    public function set_sold_individually($sold_individually): void;

    /**
     * Set if product manage stock.
     *
     * @param bool $manage_stock Whether or not manage stock is enabled.
     * @since 1.8.0
     */
    public function set_manage_stock($manage_stock): void;

    /**
     * Set number of items available for sale.
     *
     * @param float|null $quantity Stock quantity.
     * @since 1.8.0
     */
    public function set_stock_quantity($quantity): void;

    /**
     * Set stock status.
     *
     * @param string $status New status.
     */
    public function set_stock_status($status = 'instock'): void;

    /**
     * Set backorders.
     *
     * @param string $backorders Options: 'yes', 'no' or 'notify'.
     * @since 1.8.0
     */
    public function set_backorders($backorders): void;

    /**
     * Set low stock amount.
     *
     * @param int|string $amount Empty string if value not set.
     * @since 1.8.0
     */
    public function set_low_stock_amount($amount): void;

    /**
     * Set the product tags.
     *
     * @param array $term_ids List of terms IDs.
     * @since 1.8.0
     */
    public function set_tag_ids($term_ids): void;

    /**
     * Get the product tags.
     *
     * @return array List of terms IDs.
     * @since 1.8.0
     */
    public function get_tag_ids();

    /**
     * @return WC_Product
     * @since 1.8.0
     */
    public function getProduct();
}