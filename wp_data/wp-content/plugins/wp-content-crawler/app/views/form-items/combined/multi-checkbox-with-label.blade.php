{{--
    Required variables:
        String $title: Title of the form item. Label name.
        String $info: Information about the form item
        String $name: Name of the form item
        array $options: See multi-select form item

    Optional variables:
        String $id: ID of the <tr> element surrounding the form items
        Other variables of label and mult-checkbox form item views.

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
        <div class="inputs">
            @include('form-items.multi-checkbox', [
                'name'      => $name,
                'options'   => $options,
            ])
        </div>
    </td>
</tr>