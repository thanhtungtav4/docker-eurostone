<div class="input-group post-and-image-url {{ isset($remove) ? 'remove' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>

    <div class="input-container">
        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::POST_URL,
            'placeholder'   => _wpcc('Post URL...'),
            'classAttr'     => 'post-url',
        ])

        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::IMAGE_URL,
            'placeholder'   => _wpcc('Featured image URL...'),
            'classAttr'     => 'image-url',
        ])
    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>