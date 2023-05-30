<div class="description">
    {{ _wpcc('Import or export options box settings.') }}
</div>

<table class="wcc-settings">

    {{-- IMPORT SETTINGS --}}
    <?php $keyImportSettings = \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_IMPORT_SETTINGS; ?>
    <tr aria-label="{{ $keyImportSettings }}">
        <td>
            @include('form-items/label', [
                'for'   => $keyImportSettings,
                'title' => _wpcc('Import Settings'),
                'info'  => _wpcc('Paste the settings exported from another options box to import. <b>Current settings
                    will be overridden.</b>')
            ])
        </td>
        <td>
            @include('form-items/textarea', [
                'name'          =>  $keyImportSettings,
                'placeholder'   =>  _wpcc('Paste settings and click the import button. Note: This will override all settings.')
            ])
            @include('form-items.button', [
                'buttonClass' => 'options-box-import',
                'text' => _wpcc("Import")
            ])
        </td>
    </tr>

    {{-- EXPORT SETTINGS --}}
    @include('form-items.combined.textarea-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_EXPORT_SETTINGS,
        'title'     => _wpcc('Export Settings'),
        'info'      => _wpcc('You can copy the settings here and use the copied code to export settings to
            another options box.'),
        'readOnly'  =>  true,
        'noName'    =>  true,
    ])

</table>