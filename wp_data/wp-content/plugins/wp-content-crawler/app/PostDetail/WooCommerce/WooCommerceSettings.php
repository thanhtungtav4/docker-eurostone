<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 30/11/2018
 * Time: 09:11
 */

namespace WPCCrawler\PostDetail\WooCommerce;


use Illuminate\Contracts\View\View;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Settings\SettingData;
use WPCCrawler\Objects\Settings\SettingRegistry;
use WPCCrawler\PostDetail\Base\BasePostDetailSettings;
use WPCCrawler\Utils;

class WooCommerceSettings extends BasePostDetailSettings {

    const WC_PRODUCT_TYPE                       = '_wc_product_type';                             // string   Type of the product
    const WC_VIRTUAL                            = '_wc_virtual';                                  // bool     True if the product is a virtual product
    const WC_DOWNLOADABLE                       = '_wc_downloadable';                             // bool     True if the product is a downloadable product

    // General
    const WC_PRODUCT_URL                        = '_wc_product_url';                              // string   Stores URL for the external product
    const WC_BUTTON_TEXT                        = '_wc_button_text';                              // string   Stores button text for the external product
    const WC_REGULAR_PRICE_SELECTORS            = '_wc_regular_price_selectors';                  // array    CSS selectors with attributes that find regular price
    const WC_SALE_PRICE_SELECTORS               = '_wc_sale_price_selectors';                     // array    CSS selectors with attributes that find sale price
    const WC_FILE_URL_SELECTORS                 = '_wc_file_url_selectors';                       // array    CSS selectors with attributes that find file URL
    const WC_DOWNLOAD_LIMIT                     = '_wc_download_limit';                           // int      Download limit for file downloads
    const WC_DOWNLOAD_EXPIRY                    = '_wc_download_expiry';                          // int      Number of days before a download link expires

    // Inventory
    const WC_SKU_SELECTORS                      = '_wc_sku_selectors';                            // array    CSS selectors with attributes that find SKU of the product
    const WC_MANAGE_STOCK                       = '_wc_manage_stock';                             // bool     True if the stock should be managed.
    const WC_STOCK_QUANTITY_SELECTORS           = '_wc_stock_quantity_selectors';                 // array    CSS selectors with attributes that find stock quantity of the product
    const WC_BACKORDERS                         = '_wc_backorders';                               // string   Backorder type of the product
    const WC_LOW_STOCK_AMOUNT                   = '_wc_low_stock_amount';                         // int      Low stock threshold
    const WC_STOCK_STATUS                       = '_wc_stock_status';                             // string   Stock status, e.g. 'instock', 'outofstock', ...
    const WC_SOLD_INDIVIDUALLY                  = '_wc_sold_individually';                        // bool     True if the product is sold individually

    // Shipping
    const WC_WEIGHT_SELECTORS                   = '_wc_weight_selectors';                         // array    CSS selectors with attributes that find weight of the product
    const WC_LENGTH_SELECTORS                   = '_wc_length_selectors';                         // array    CSS selectors with attributes that find length of the product
    const WC_WIDTH_SELECTORS                    = '_wc_width_selectors';                          // array    CSS selectors with attributes that find width of the product
    const WC_HEIGHT_SELECTORS                   = '_wc_height_selectors';                         // array    CSS selectors with attributes that find height of the product
    const WC_PRODUCT_SHIPPING_CLASS             = '_wc_product_shipping_class';                   // int      ID of shipping class of the product

    // Attributes
    const WC_ATTRIBUTE_NAME_SELECTORS           = '_wc_attribute_name_selectors';                 // array    CSS selectors with attributes that find attribute names
    const WC_ATTRIBUTE_VALUE_SELECTORS          = '_wc_attribute_value_selectors';                // array    CSS selectors with attributes that find attribute values
    const WC_CUSTOM_ATTRIBUTES_WITH_SELECTORS   = '_wc_custom_attributes_with_selectors';         // array    CSS selectors with attributes and custom product attribute name/slug
    const WC_ATTRIBUTE_VALUE_SEPARATORS         = '_wc_attribute_value_separators';               // array    Separators that will be used to separate attribute values in a single string
    const WC_CUSTOM_ATTRIBUTES                  = '_wc_custom_attributes';                        // array    Custom attributes with attribute name and attribute values

    // Advanced
    const WC_PURCHASE_NOTE_SELECTORS            = '_wc_purchase_note_selectors';                  // array    CSS selectors with attributes that find purchase notes
    const WC_PURCHASE_NOTE_ADD_ALL_FOUND        = '_wc_purchase_note_add_all_found';              // bool     When checked, purchase notes found by all CSS selectors will be added
    const WC_CUSTOM_PURCHASE_NOTES              = '_wc_custom_purchase_notes';                    // array    An array of custom purchase notes.
    const WC_ALWAYS_ADD_CUSTOM_PURCHASE_NOTE    = '_wc_always_add_custom_purchase_note';          // bool     When checked, custom purchase note will be prepended to purchase notes found by CSS selectors.
    const WC_ENABLE_REVIEWS                     = '_wc_enable_reviews';                           // bool     True if the reviews for the product should be enabled
    const WC_MENU_ORDER                         = '_wc_menu_order';                               // int      Menu order of the product

    /**
     * @return SettingRegistry A {@link SettingRegistry} instance that contains the settings of this post detail.
     * @since 1.9.0
     */
    protected function createSettingRegistry(): SettingRegistry {
        return new SettingRegistry([
            new SettingData(WooCommerceSettings::WC_PRODUCT_TYPE,   ValueType::T_STRING),
            new SettingData(WooCommerceSettings::WC_VIRTUAL,        ValueType::T_BOOLEAN),
            new SettingData(WooCommerceSettings::WC_DOWNLOADABLE,   ValueType::T_BOOLEAN),

            // General
            new SettingData(WooCommerceSettings::WC_PRODUCT_URL,                ValueType::T_STRING),
            new SettingData(WooCommerceSettings::WC_BUTTON_TEXT,                ValueType::T_STRING),
            new SettingData(WooCommerceSettings::WC_REGULAR_PRICE_SELECTORS,    ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_SALE_PRICE_SELECTORS,       ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_FILE_URL_SELECTORS,         ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_DOWNLOAD_LIMIT,             ValueType::T_INTEGER),
            new SettingData(WooCommerceSettings::WC_DOWNLOAD_EXPIRY,            ValueType::T_INTEGER),

            // Inventory
            new SettingData(WooCommerceSettings::WC_SKU_SELECTORS,              ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_MANAGE_STOCK,               ValueType::T_BOOLEAN),
            new SettingData(WooCommerceSettings::WC_STOCK_QUANTITY_SELECTORS,   ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_BACKORDERS,                 ValueType::T_STRING),
            new SettingData(WooCommerceSettings::WC_LOW_STOCK_AMOUNT,           ValueType::T_INTEGER),
            new SettingData(WooCommerceSettings::WC_STOCK_STATUS,               ValueType::T_STRING),
            new SettingData(WooCommerceSettings::WC_SOLD_INDIVIDUALLY,          ValueType::T_BOOLEAN),

            // Shipping
            new SettingData(WooCommerceSettings::WC_WEIGHT_SELECTORS,       ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_LENGTH_SELECTORS,       ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_WIDTH_SELECTORS,        ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_HEIGHT_SELECTORS,       ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_PRODUCT_SHIPPING_CLASS, ValueType::T_INTEGER),

            // Attributes
            new SettingData(WooCommerceSettings::WC_ATTRIBUTE_NAME_SELECTORS,           ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_ATTRIBUTE_VALUE_SELECTORS,          ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_CUSTOM_ATTRIBUTES_WITH_SELECTORS,   ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_ATTRIBUTE_VALUE_SEPARATORS,         ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_CUSTOM_ATTRIBUTES,                  ValueType::T_ARRAY),

            // Advanced
            new SettingData(WooCommerceSettings::WC_PURCHASE_NOTE_SELECTORS,            ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_PURCHASE_NOTE_ADD_ALL_FOUND,        ValueType::T_BOOLEAN),
            new SettingData(WooCommerceSettings::WC_CUSTOM_PURCHASE_NOTES,              ValueType::T_ARRAY),
            new SettingData(WooCommerceSettings::WC_ALWAYS_ADD_CUSTOM_PURCHASE_NOTE,    ValueType::T_BOOLEAN),
            new SettingData(WooCommerceSettings::WC_ENABLE_REVIEWS,                     ValueType::T_BOOLEAN),
            new SettingData(WooCommerceSettings::WC_MENU_ORDER,                         ValueType::T_INTEGER),
        ]);
    }

    /**
     * Create settings view. This view will be shown in the site settings page. The view can be created by using
     * {@link Utils::view()} method. If the view is outside of the plugin, it can be created using a custom implementation
     * of {@link Utils::view()}. In that case, check the source code of the method.
     *
     * @return null|View Not-rendered blade view
     */
    protected function createSettingsView() {
        return Utils::view('post-detail.woocommerce.site-settings.main');
    }

    /*
     * STATIC METHODS
     */

    /**
     * Get backorder options.
     *
     * @return array A key-value pair where keys are option keys and values are option values that can be shown in a
     *               select HTML element.
     * @since 1.8.0
     */
    public static function getBackorderOptionsForSelect() {
        return [
            'no'        => _wpcc('Do not allow'),
            'notify'    => _wpcc('Allow, but notify customer'),
            'yes'       => _wpcc('Allow'),
        ];
    }

    /**
     * Get stock status options.
     *
     * @return array A key-value pair where keys are option keys and values are option values that can be shown in a
     *               select HTML element.
     * @since 1.8.0
     */
    public static function getStockStatusOptionsForSelect() {
        return [
            'instock'       => _wpcc('In stock'),
            'outofstock'    => _wpcc('Out of stock'),
            'onbackorder'   => _wpcc('On backorder'),
        ];
    }

    /**
     * Get product type options.
     *
     * @return array A key-value pair where keys are option keys and values are option values that can be shown in a
     *               select HTML element.
     * @since 1.8.0
     */
    public static function getProductTypeOptionsForSelect() {
        return [
            'simple'   => [
                'name'       => _wpcc("Simple Product"),
                'dependants' => '["!.wc-external-product"]'
            ],
            'external' => [
                'name'       => _wpcc("External/Affiliate Product"),
                'dependants' => sprintf('[
                    "!label[for=\"%1$s\"]", 
                    "!label[for=\"%2$s\"]", 
                    "!.wc-purchase-note", 
                    "!.wc-tab-shipping", 
                    "!#wc-manage-stock", 
                    "!#wc-stock-quantity-selectors", 
                    "!#wc-backorders", 
                    "!#wc-low-stock-amount", 
                    "!#wc-stock-status", 
                    "!#wc-sold-individually"
                ]',
                static::WC_VIRTUAL,
                static::WC_DOWNLOADABLE
                ),
            ],
        ];
    }

}
