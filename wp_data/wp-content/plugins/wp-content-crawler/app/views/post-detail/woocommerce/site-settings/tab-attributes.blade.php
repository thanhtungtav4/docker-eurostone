<?php

$customAttrDescription = _wpcc("Custom product attributes will be added to each product. You can use short codes in the
    name and value of the product attribute.");

$asTaxonomyDescription = _wpcc("If you want to add the values to Attributes page of WooCommerce, check <b>'as taxonomy'</b>
    checkbox and write slug of the attribute instead of its name by retrieving it from Attributes page.");

?>

<table class="wcc-settings">

    {{-- ATTRIBUTE NAME SELECTORS --}}
    @include('post-detail.woocommerce.form-items.combined.multiple-product-name-selector-with-attribute', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_ATTRIBUTE_NAME_SELECTORS,
        'title'         => _wpcc('Attribute Name Selectors'),
        'info'          => _wpcc("Selectors that find names of the attributes.") . ' ' . $asTaxonomyDescription,
        'optionsBox'    => true,
    ])

    {{-- ATTRIBUTE VALUE SELECTORS --}}
    @include('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_ATTRIBUTE_VALUE_SELECTORS,
        'title'         => _wpcc('Attribute Value Selectors'),
        'info'          => _wpcc("Selectors that find values of the attributes whose names are found by using attribute name
            selectors."),
        'optionsBox'    => true,
        'class'         => 'attribute-value-selectors',
        'data'          => [
            'selectorFinderBehavior' => \WPCCrawler\Objects\Enums\SelectorFinderBehavior::SIMILAR,
        ]
    ])

    {{-- CUSTOM ATTRIBUTES WITH SELECTORS --}}
    @include('post-detail.woocommerce.form-items.combined.multiple-selector-custom-product-attribute', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_CUSTOM_ATTRIBUTES_WITH_SELECTORS,
        'title'         => _wpcc('Custom Attributes with Selectors'),
        'info'          => _wpcc("You can define your custom attributes and retrieve their values from the target
            site using CSS selectors. By default, this adds all values found by the selector. If you want to use only
            the first found value, check 'single' checkbox.") . ' ' . $customAttrDescription . ' ' . $asTaxonomyDescription,
        'optionsBox'    => true,
    ])

    {{-- ATTRIBUTE VALUE SEPARATORS --}}
    @include('form-items.combined.multiple-text-with-label', [
        'name'          => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_ATTRIBUTE_VALUE_SEPARATORS,
        'title'         => _wpcc('Attribute Value Separators'),
        'info'          => _wpcc("Set separators for attribute values found by selectors. For example, if an attribute
            value selector finds 'small, medium, large', when you add ',' as separator, there will be three values as
            'small', 'medium', and 'large'. Otherwise, the attribute value will be 'small, medium, large'. If you add
            more than one separator, all will be applied."),
        'placeholder'   => _wpcc('Separator...'),
    ])

    {{-- CUSTOM MANUAL ATTRIBUTES --}}
    @include('post-detail.woocommerce.form-items.combined.multiple-custom-product-attribute', [
        'name'              => \WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings::WC_CUSTOM_ATTRIBUTES,
        'title'             => _wpcc('Custom Attributes'),
        'info'              => _wpcc("You can define your custom attributes here. When writing attribute value, you can
            enter more than one value by separating them with commas.") . ' ' . $customAttrDescription . ' ' . $asTaxonomyDescription,
        'keyPlaceholder'    => _wpcc('Product attribute name/slug...'),
        'valuePlaceholder'  => _wpcc('Comma-separated attribute values...'),
    ])

</table>
