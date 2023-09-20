<div class="description">
    <?php echo e(_wpcc('Import or export options box settings.')); ?>

</div>

<table class="wcc-settings">

    
    <?php $keyImportSettings = \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_IMPORT_SETTINGS; ?>
    <tr aria-label="<?php echo e($keyImportSettings); ?>">
        <td>
            <?php echo $__env->make('form-items/label', [
                'for'   => $keyImportSettings,
                'title' => _wpcc('Import Settings'),
                'info'  => _wpcc('Paste the settings exported from another options box to import. <b>Current settings
                    will be overridden.</b>')
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
        <td>
            <?php echo $__env->make('form-items/textarea', [
                'name'          =>  $keyImportSettings,
                'placeholder'   =>  _wpcc('Paste settings and click the import button. Note: This will override all settings.')
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('form-items.button', [
                'buttonClass' => 'options-box-import',
                'text' => _wpcc("Import")
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
    </tr>

    
    <?php echo $__env->make('form-items.combined.textarea-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_EXPORT_SETTINGS,
        'title'     => _wpcc('Export Settings'),
        'info'      => _wpcc('You can copy the settings here and use the copied code to export settings to
            another options box.'),
        'readOnly'  =>  true,
        'noName'    =>  true,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</table><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/options-box/tabs/tab-import-export.blade.php ENDPATH**/ ?>