<div class="input-group key-value {{ isset($remove) ? 'remove' : '' }} {{ isset($class) ? $class : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    <div class="input-container">
        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::KEY,
            'placeholder'   => $keyPlaceholder,
        ])

        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::VALUE,
            'placeholder'   => $valuePlaceholder,
        ])
    </div>
    @if(isset($remove) && $remove)
        @include('form-items/remove-button')
    @endif
</div>