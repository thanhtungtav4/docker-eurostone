{{--
    Required variables:
        String $title: Title of the form item. Label name.
        String $info: Information about the form item
        String $name: Name of the form item

    Optional variables:
        String $id: ID of the <tr> element surrounding the form items
        Other variables of label and multiple form item views.

--}}

<tr @if(isset($id)) id="{{ $id }}" @endif
@if(isset($class)) class="{{ $class }}" @endif
aria-label="{{ $name }}"
>
    <td>
        @include('form-items/label', [
            'for'   =>  $name,
            'title' =>  $title,
            'info'  =>  $info
        ])
    </td>
    <td>
        @include('form-items/multiple', [
            'include'       => 'form-items/media',
            'name'          => $name,
            'addKeys'       => true,
        ])
        @include('partials/test-result-container')
    </td>
</tr>