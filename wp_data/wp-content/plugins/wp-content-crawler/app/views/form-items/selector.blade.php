<div class="input-group selector {{ isset($addon) ? 'addon dev-tools' : '' }} {{ isset($remove) ? 'remove' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    @if(isset($addon))
        @include('form-items.partials.button-addon-test')
        @include('form-items.dev-tools.button-dev-tools')

        @if(isset($optionsBox) && $optionsBox)
            @include('form-items.options-box.button-options-box')
        @endif
    @endif
    <div class="input-container">
        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::SELECTOR,
            'placeholder'   => _wpcc('Selector'),
            'classAttr'     => 'css-selector',
        ])
    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>