<div class="input-group media {{ isset($remove) ? ' remove ' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif
>
    @include('form-items.partials.button-media-viewer')

    {{-- Preview of the selected image --}}
    <div class="media-preview">
        <a href="#">
            <div class="image"></div>
        </a>
    </div>

    <div class="input-container media-container">
        @include('form-items.input-with-inner-key', [
            'innerKey'    => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::ITEM_ID,
            'type'        => 'number',
            'placeholder' => _wpcc('Image ID'),
        ])
    </div>

    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>