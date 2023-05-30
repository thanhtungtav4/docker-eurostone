<div class="wcc-settings-title">
    <h3>{{ _wpcc('Filters') }}</h3>
    <span>{{ _wpcc('Filters that will be applied in the post pages...') . ' ' . _wpcc_filter() }}</span>
</div>

<table class="wcc-settings">

    @include('form-items.combined.filter-with-label', [
        'name'       =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_DATA_FILTERS,
        'title'      =>  _wpcc('Post data filters'),
        'info'       =>  _wpcc('Define filters that will be applied for the post data.') . _wpcc_filter(true),
        'class'      => 'site-settings-filters',
        'eventGroup' => \WPCCrawler\Objects\Events\Enums\EventGroupKey::POST_DATA
    ])

</table>