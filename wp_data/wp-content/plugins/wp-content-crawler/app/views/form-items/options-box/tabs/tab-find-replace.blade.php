<div class="description">
    {{ _wpcc("Find and replace in each item. These options will be applied before any changes are made to the current item.") }} {!! _wpcc_trans_regex() !!}
</div>

<table class="wcc-settings">

    @include('form-items.combined.multiple-find-replace-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_FIND_REPLACE,
        'data'      =>  [
            'extra' =>  $dataExtra
        ],
        'noLabel'   => true
    ])

</table>