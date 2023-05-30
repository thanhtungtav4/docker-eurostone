<div class="input-group custom-product-attribute {{ isset($remove) ? 'remove' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    <div class="input-container">
        @include('form-items.input-with-inner-key', [
            'type'          => 'checkbox',
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::AS_TAXONOMY,
            'titleAttr'     => _wpcc('As taxonomy?'),
            'showTooltip'   => true,
        ])

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