{{--
    Required variables:
        String $title: Title of the form item. Label name.
        String $info: Information about the form item
        String $name: Name of the form item
        String $text: Text that will be shown on the button

    Optional variables:
        String $id: ID of the <tr> element surrounding the form items
        String $class: Class of the <tr> element surrounding the form items.
        String $btnTitle: Explanation that will shown when the button is hovered
        Other variables of label and button form item views.

--}}

<tr @if(isset($id)) id="{{ $id }}" @endif
@if(isset($class)) class="{{ $class }}" @endif
>
    <td>
        @include('form-items/label', [
            'for'   => $name,
            'title' => $title,
            'info'  => $info,
        ])
    </td>
    <td>
        @include('form-items/button', [
            'text'  => $text,
            'title' => $btnTitle ?? ''
        ])
        @include('partials/test-result-container')
    </td>
</tr>