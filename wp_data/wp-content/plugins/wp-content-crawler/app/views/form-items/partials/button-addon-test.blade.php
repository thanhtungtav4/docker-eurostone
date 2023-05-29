<button
        class="button wpcc-button wcc-addon {{ isset($test) ? 'wcc-test' : '' }} {{ isset($addonClasses) ? $addonClasses : '' }}"
        title="{{ isset($addonTitle) ? $addonTitle : _wpcc('Test') }}"
        @if(isset($data)) data-wcc="{{ json_encode($data) }}" @endif
>
    <span class="{{$addon}}"></span>
</button>