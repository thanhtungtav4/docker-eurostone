{{--
    Required variables:
        String $title: Title of the form item. Label name.
        String $info: Information about the form item
        array $buttons: Short code buttons

    Optional variables:
        String $id: ID of the <tr> element surrounding the form items
        String $class: Class of the <tr> element surrounding the form items.
        Other variables of label and short-code-buttons views.

--}}

<tr @if(isset($id)) id="{{ $id }}" @endif
@if(isset($class)) class="{{ $class }}" @endif
>
    <td>
        @include('form-items/label', [
            'title' =>  $title,
            'info'  =>  $info
        ])
    </td>
    <td>
        @include('form-items.partials.short-code-buttons', [
            'tooltipPos'    => 'top'
        ])
    </td>
</tr>