<button class="button wpcc-button button-primary button-large {{ isset($class) && $class ? $class : '' }}"
        data-wpcc-toggle="wpcc-tooltip" data-placement="right"
    @if(isset($title) && $title) title="{{ $title }}" @endif>
    {{ isset($text) ? $text : _wpcc('Submit') }}
</button>