<div class="input-group custom-post-meta {{ isset($remove) ? 'remove' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    <div class="input-container">
        @include('form-items.input-with-inner-key', [
            'type'          => 'checkbox',
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::MULTIPLE,
            'titleAttr'     => _wpcc('Multiple?'),
            'showTooltip'   => true,
        ])

        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::KEY,
            'placeholder'   => _wpcc('Post meta key'),
            'classAttr'     => 'meta-key',
        ])

        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::VALUE,
            'placeholder'   => _wpcc('Meta value'),
        ])
    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>