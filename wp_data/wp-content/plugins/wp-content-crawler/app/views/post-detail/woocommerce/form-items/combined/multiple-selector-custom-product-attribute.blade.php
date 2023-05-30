{{--
    Required variables:
        String $title: Title of the form item. Label name.
        String $info: Information about the form item
        String $name: Name of the form item
        String $urlSelector: CSS selector for the URL input

    Optional variables:
        String $id: ID of the <tr> element surrounding the form items
        String $defaultAttr: Default attribute for the selector of the form item
        Other variables of label and multiple form item views.

--}}

<?php
$attr = isset($defaultAttr) && $defaultAttr ? $defaultAttr : 'text'
?>

<tr @if(isset($id)) id="{{ $id }}" @endif
@if(isset($class)) class="{{ $class }}" @endif
>
    <td>
        @include('form-items/label', [
            'for'   =>  $name,
            'title' =>  $title,
            'info'  =>  $info . ' ' . _wpcc_selector_attribute_info(),
        ])
    </td>
    <td>
        @include('form-items/multiple', [
            'include'       => 'post-detail.woocommerce.form-items.selector-custom-product-attribute',
            'name'          => $name,
            'addon'         =>  'dashicons dashicons-search',
            'data'          =>  [
                'urlSelector'            =>  $urlSelector,
                'testType'               =>  \WPCCrawler\Test\Test::$TEST_TYPE_SELECTOR_ATTRIBUTE,
                'attr'                   =>  $attr,
                'selectorFinderBehavior' =>  \WPCCrawler\Objects\Enums\SelectorFinderBehavior::SIMILAR,
            ],
            'test'          => true,
            'addKeys'       => true,
            'addonClasses'  => 'wcc-test-selector-attribute',
            'defaultAttr'   => $attr,
        ])
        @include('partials/test-result-container')
    </td>
</tr>