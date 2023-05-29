<div class="input-group custom-post-taxonomy {{ isset($remove) ? 'remove' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    <div class="input-container">
        @include('form-items.input-with-inner-key', [
            'type'          => 'checkbox',
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::APPEND,
            'titleAttr'     => _wpcc('Append?'),
            'showTooltip'   => true,
        ])

        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::TAXONOMY,
            'placeholder'   => _wpcc('Taxonomy name...'),
            'classAttr'     => 'meta-key',
        ])

        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::VALUE,
            'placeholder'   => _wpcc('Taxonomy value...'),
        ])
    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>