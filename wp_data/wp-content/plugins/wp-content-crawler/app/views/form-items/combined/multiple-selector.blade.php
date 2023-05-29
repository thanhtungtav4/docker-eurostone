{{--
    Required variables:
        String $title: Title of the form item. Label name.
        String $info: Information about the form item
        String $name: Name of the form item
        String $urlSelector: CSS selector for the URL input

    Optional variables:
        String $id: ID of the <tr> element surrounding the form items
        Other variables of label and multiple form item views.

--}}

<?php
    $defaultData = [
        'urlSelector'   =>  $urlSelector,
        'testType'      =>  \WPCCrawler\Test\Test::$TEST_TYPE_HTML,
    ];

    if (isset($data) && $data && is_array($data)) {
        $defaultData = array_merge($defaultData, $data);
    }
?>

<tr @if(isset($id)) id="{{ $id }}" @endif
    @if(isset($class)) class="{{ $class }}" @endif
    aria-label="{{ $name }}"
>
    <td>
        @include('form-items/label', [
            'for'   =>  $name,
            'title' =>  $title,
            'info'  =>  $info,
        ])
    </td>
    <td>
        @include('form-items/multiple', [
            'include'       => 'form-items/selector',
            'name'          => $name,
            'addon'         => isset($addon) && $addon ? $addon : 'dashicons dashicons-search',
            'data'          => $defaultData,
            'test'          => true,
            'addKeys'       => true,
        ])
        @include('partials/test-result-container')
    </td>
</tr>