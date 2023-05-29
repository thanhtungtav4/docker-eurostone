{{--
    Required variables:
        String $title: Title of the form item. Label name.
        String $info:  Information about the form item
        String $name:  Name of the form item

    Optional variables:
        String $id:             ID of the <tr> element surrounding the form items
        String $defaultAttr:    Default attribute for the selector of the form item
        bool   $noLabel:        If this is provided and true, then the label cell will not be added.
        Other variables of label and multiple form item views.

--}}

<?php
    $attr = isset($defaultAttr) && $defaultAttr ? $defaultAttr : 'text';

    $defaultData = [
        'testType'  =>  \WPCCrawler\Test\Test::$TEST_TYPE_FIND_REPLACE,
    ];

    if (isset($data) && $data && is_array($data)) {
        $defaultData = array_merge($defaultData, $data);
    }
?>

<tr @if(isset($id)) id="{{ $id }}" @endif
    @if(isset($class)) class="{{ $class }}" @endif
    aria-label="{{ $name }}"
>
    @if(!isset($noLabel) || !$noLabel)
        <td>
            @include('form-items/label', [
                'for'   =>  $name,
                'title' =>  $title,
                'info'  =>  $info . ' ' . _wpcc_trans_regex(),
            ])
        </td>
    @endif

    <td>
        @include('form-items/multiple', [
            'include'       => 'form-items/find-replace',
            'name'          => $name,
            'addon'         => isset($addon) && $addon ? $addon : 'dashicons dashicons-search',
            'data'          => $defaultData,
            'test'          => true,
            'addKeys'       => true,
            'remove'        => true,
            'addonClasses'  => isset($addonClasses) && $addonClasses ? $addonClasses : 'wcc-test-find-replace',
        ])
        @include('partials/test-result-container')
    </td>
</tr>