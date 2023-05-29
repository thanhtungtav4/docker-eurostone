<button class="button wpcc-button test-this {{ isset($class) && $class ? $class : '' }}"
        data-url="{{ $url }}"
        data-type="{{ $type }}"
        title="{{ _wpcc('Test this URL') }}">
    <span class="dashicons dashicons-search"></span>
</button>