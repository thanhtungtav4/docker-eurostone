<table class="wcc-settings">

    {{-- WEIGHT SELECTOR --}}
    @include('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_WEIGHT_SELECTORS,
        'title'         => _wpcc('Weight Selectors'),
        'info'          => _wpcc('CSS selectors for weight.') . ' ' . _wpcc_trans_multiple_selectors_first_match(),
        'optionsBox'    => true,
    ])

    {{-- LENGTH SELECTOR --}}
    @include('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_LENGTH_SELECTORS,
        'title'         => _wpcc('Length Selectors'),
        'info'          => _wpcc('CSS selectors for length.') . ' ' . _wpcc_trans_multiple_selectors_first_match(),
        'optionsBox'    => true,
    ])

    {{-- WIDTH SELECTOR --}}
    @include('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_WIDTH_SELECTORS,
        'title'         => _wpcc('Width Selectors'),
        'info'          => _wpcc('CSS selectors for width.') . ' ' . _wpcc_trans_multiple_selectors_first_match(),
        'optionsBox'    => true,
    ])

    {{-- HEIGHT SELECTOR --}}
    @include('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_HEIGHT_SELECTORS,
        'title'         => _wpcc('Height Selectors'),
        'info'          => _wpcc('CSS selectors for height.') . ' ' . _wpcc_trans_multiple_selectors_first_match(),
        'optionsBox'    => true,
    ])

    {{-- SHIPPING CLASS --}}
    @include('form-items.combined.select-wp-dropdown-categories-with-label', [
        'name' => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_PRODUCT_SHIPPING_CLASS,
        'title' => _wpcc('Shipping Class'),
        'info' => _wpcc('Select the shipping class.'),
        'args' => [
            'taxonomy'          => 'product_shipping_class',
            'hide_empty'        => 0,
            'show_option_none'  => _wpcc('No shipping class'),
            'class'             => '',
        ]
    ])

</table>
