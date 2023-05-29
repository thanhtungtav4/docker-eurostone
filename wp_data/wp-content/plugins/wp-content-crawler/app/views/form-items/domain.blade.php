<div class="input-group domains {{ isset($remove) ? 'remove' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>

    <div class="input-container">
        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::DOMAIN,
            'placeholder'   => _wpcc('Domain...'),
            'classAttr'     => 'post-url',
        ])
    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>