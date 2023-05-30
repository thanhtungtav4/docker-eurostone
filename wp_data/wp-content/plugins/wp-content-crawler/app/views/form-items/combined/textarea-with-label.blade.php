{{--
    Required variables:
        String $title: Title of the form item. Label name.
        String $info:  Information about the form item
        String $name:  Name of the form item

    Optional variables:
        String $id:         ID of the <tr> element surrounding the form items
        String $class:      Class of the <tr> element surrounding the form items.
        bool   $noLabel:    If this is provided and true, then the label cell will not be added.
        Other variables of label and textarea form item views.

--}}

<tr @if(isset($id)) id="{{ $id }}" @endif
@if(isset($class)) class="{{ $class }}" @endif
aria-label="{{ $name }}"
>
    @if(!isset($noLabel) || !$noLabel)
        <td>
            @include('form-items/label', [
                'for'   => $name,
                'title' => $title,
                'info'  => $info,
            ])
        </td>
    @endif
    <td>
        @include('form-items/textarea', [
            'name' => $name
        ])
    </td>
</tr>