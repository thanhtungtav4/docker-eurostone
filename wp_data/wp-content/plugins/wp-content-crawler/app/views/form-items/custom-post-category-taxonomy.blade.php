<div class="input-group category-taxonomy {{ isset($remove) ? 'remove' : '' }} {{ isset($class) ? $class : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>

    <div class="input-container">
        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::TAXONOMY,
            'placeholder'   => _wpcc("Taxonomy...")
        ])

        @include('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::DESCRIPTION,
            'placeholder'   => _wpcc("Name/description...")
        ])
    </div>

    @if(isset($remove) && $remove)
        @include('form-items/remove-button')
    @endif
</div>