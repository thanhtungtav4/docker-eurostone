{{--
    Required variables:
        string $title       Title of the form item. Label name.
        string $info        Information about the form item
        string $name        Name of the form item
        string $eventGroup  See "filter" view

    Optional variables:
        string $id      ID of the <tr> element surrounding the form items
        string $class   Class of the <tr> element surrounding the form items.

        Other variables of label and filter form item views.

--}}

<tr @if(isset($id)) id="{{ $id }}" @endif
@if(isset($class)) class="{{ $class }}" @endif
    aria-label="{{ $name }}"
>
    <td>
        @include('form-items.label', [
            'for'   => $name,
            'title' => $title,
            'info'  => $info,
        ])
    </td>
    <td>
        @include('form-items.filter', [
            'name'       => $name,
            'eventGroup' => $eventGroup,
        ])
    </td>
</tr>