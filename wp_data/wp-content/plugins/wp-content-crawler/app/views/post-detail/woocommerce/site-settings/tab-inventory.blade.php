<table class="wcc-settings">

    {{-- SKU SELECTOR --}}
    @include('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_SKU_SELECTORS,
        'title'         => _wpcc('SKU Selectors'),
        'info'          => _wpcc('CSS selectors for SKU.') . ' ' . _wpcc_trans_multiple_selectors_first_match(),
        'optionsBox'    => true,
    ])

    {{-- MANAGE STOCK --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_MANAGE_STOCK,
        'title'         => _wpcc('Manage stock?'),
        'info'          => _wpcc('Select if you want to manage stock or not.'),
        'id'            => 'wc-manage-stock',
        'dependants'    => '["#wc-stock-quantity-selectors", "#wc-backorders", "#wc-low-stock-amount", "!#wc-stock-status"]',
    ])

    {{-- STOCK QUANTITY SELECTOR --}}
    @include('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_STOCK_QUANTITY_SELECTORS,
        'title'         => _wpcc('Stock Quantity Selectors'),
        'info'          => _wpcc('CSS selectors for stock quantity.') . ' ' . _wpcc_trans_multiple_selectors_first_match(),
        'id'            => 'wc-stock-quantity-selectors',
        'optionsBox'    => true,
    ])

    {{-- ALLOW BACKORDERS --}}
    @include('form-items.combined.select-with-label', [
        'name'      => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_BACKORDERS,
        'title'     => _wpcc('Allow backorders?'),
        'info'      => _wpcc('Select if backorders are allowed or not.'),
        'options'   => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::getBackorderOptionsForSelect(),
        'id'        => 'wc-backorders',
    ])

    {{-- LOW STOCK THRESHOLD --}}
    @include('form-items.combined.input-with-label', [
        'name'  => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_LOW_STOCK_AMOUNT,
        'title' => _wpcc('Low Stock Threshold'),
        'info'  => _wpcc('When product stock reaches this amount you will be notified by email by WooCommerce.'),
        'type'  => 'number',
        'id'    => 'wc-low-stock-amount',
    ])

    {{-- STOCK STATUS --}}
    @include('form-items.combined.select-with-label', [
        'name'      => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_STOCK_STATUS,
        'title'     => _wpcc('Stock Status'),
        'info'      => _wpcc('Select stock status.'),
        'options'   => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::getStockStatusOptionsForSelect(),
        'id'        => 'wc-stock-status',
    ])

    {{-- SOLD INDIVIDUALLY --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_SOLD_INDIVIDUALLY,
        'title'         => _wpcc('Sold individually?'),
        'info'          => _wpcc('Enable this to only allow one of this item to be bought in a single order.'),
        'id'            => 'wc-sold-individually'
    ])

</table>
