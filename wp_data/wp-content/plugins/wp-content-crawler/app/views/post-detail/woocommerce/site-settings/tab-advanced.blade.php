<table class="wcc-settings">

    {{-- PURCHASE NOTE SELECTORS --}}
    @include('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_PURCHASE_NOTE_SELECTORS,
        'title'         => _wpcc('Purchase Note Selectors'),
        'info'          => _wpcc("CSS selectors for purchase notes. This gets text of the found element by default. When
            there are multiple selectors, the first match will be used by default."),
        'optionsBox'    => true,
        'class'         => 'wc-purchase-note',
    ])

    {{-- ADD ALL FOUND PURCHASE NOTES --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_PURCHASE_NOTE_ADD_ALL_FOUND,
        'title' => _wpcc('Add all found purchase notes?'),
        'info'  => _wpcc("Check this if you want to add all purchase notes found by purchase note selectors. Otherwise,
            when there are multiple selectors, only the first match will be used."),
        'class' => 'wc-purchase-note',
    ])

    {{-- CUSTOM PURCHASE NOTE --}}
    @include('form-items.combined.multiple-textarea-with-label', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_CUSTOM_PURCHASE_NOTES,
        'title'         => _wpcc('Custom Purchase Note'),
        'info'          => _wpcc("Enter custom purchase notes for the product. If you enter more than one, a random
            purchase note will be selected when saving a product."),
        'optionsBox'    => true,
        'placeholder'   => _wpcc('Custom purchase note...'),
        'rows'          => 4,
        'class'         => 'wc-purchase-note',
    ])

    {{-- ALWAYS ADD CUSTOM PURCHASE NOTE --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_ALWAYS_ADD_CUSTOM_PURCHASE_NOTE,
        'title' => _wpcc('Always add custom purchase note?'),
        'info'  => _wpcc("Check this if you want to add the custom purchase note always. If you do not check this,
            custom purchase note will be added only if there is no purchase note found by selectors. If you check this,
            the purchase note of the product will be created by using both the purchase notes found by selectors and the
            custom purchase note. The purchase notes found by selectors will be added after the custom purchase note."),
        'class' => 'wc-purchase-note',
    ])

    {{-- ENABLE REVIEWS --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_ENABLE_REVIEWS,
        'title' => _wpcc('Enable reviews?'),
        'info'  => _wpcc("Check this if you want to enable reviews for the product."),
    ])

    {{-- MENU ORDER --}}
    @include('form-items.combined.input-with-label', [
        'name'  => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_MENU_ORDER,
        'title' => _wpcc('Menu Order'),
        'info'  => _wpcc("Enter the menu order for the product."),
        'type'  => 'number',
        'step'  => 1,
    ])

</table>
