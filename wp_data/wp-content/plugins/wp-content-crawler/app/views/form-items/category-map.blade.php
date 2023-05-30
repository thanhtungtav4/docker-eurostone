<?php /** @var array $value */ ?>
<div class="input-group category-map {{ isset($addon) ? 'addon' : '' }} {{ isset($remove) ? 'remove' : '' }}" @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    @if(isset($addon))
        @include('form-items.partials.button-addon-test')
    @endif
    <div class="input-container">
        <?php $selectedId = isset($value['cat_id']) ? $value['cat_id'] : null; ?>

        @include('form-items.partials.categories', [
            'selectedId'        => $selectedId,
            'name'              => $name . '[cat_id]',
            'categories'        => $categories,
            'taxonomyInputName' => $name . '[taxonomy]',
            'addTaxonomyInput'  => true
        ])

        @include('form-items.input-with-inner-key', [
            'innerKey' => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::URL
        ])

    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>