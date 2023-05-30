<div class="input-group exchange-attrs {{ isset($remove) ? 'remove' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    <div class="input-container">
        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::ATTRIBUTE_1,
            'placeholder'   => _wpcc('Attribute 1'),
        ])

        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::ATTRIBUTE_2,
            'placeholder'   => _wpcc('Attribute 2'),
        ])
    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>