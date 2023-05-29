<div class="wcc-settings-title">
    <h3>{{ _wpcc('Import/Export Settings') }}</h3>
    <span>{{ _wpcc('Import settings from another site or copy the settings to import for another site') }}</span>
</div>

<table class="wcc-settings">

    {{-- IMPORT SETTINGS --}}
    @include('form-items.combined.textarea-with-label', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_IMPORT_SETTINGS,
        'title'         => _wpcc('Import Settings'),
        'info'          => _wpcc('Paste the settings exported from another site to import. <b>Current settings
            will be overridden.</b>'),
        'placeholder'   =>  _wpcc('Paste settings and update. Note: This will override all settings.')
    ])

    {{-- EXPORT SETTINGS --}}
    @include('form-items.combined.textarea-with-label', [
        'name'      => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_EXPORT_SETTINGS,
        'title'     => _wpcc('Export Settings'),
        'info'      => _wpcc('You can copy the settings here and use the copied code to export settings to
            another site.'),
        'value'     =>  $settingsForExport,
        'readOnly'  =>  true,
        'noName'    =>  true,
    ])

</table>
