<div class="wcc-settings-title">
    <h3><?php echo e(_wpcc('Import/Export Settings')); ?></h3>
    <span><?php echo e(_wpcc('Import settings from another site or copy the settings to import for another site')); ?></span>
</div>

<table class="wcc-settings">

    
    <?php echo $__env->make('form-items.combined.textarea-with-label', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_IMPORT_SETTINGS,
        'title'         => _wpcc('Import Settings'),
        'info'          => _wpcc('Paste the settings exported from another site to import. <b>Current settings
            will be overridden.</b>'),
        'placeholder'   =>  _wpcc('Paste settings and update. Note: This will override all settings.')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.textarea-with-label', [
        'name'      => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_EXPORT_SETTINGS,
        'title'     => _wpcc('Export Settings'),
        'info'      => _wpcc('You can copy the settings here and use the copied code to export settings to
            another site.'),
        'value'     =>  $settingsForExport,
        'readOnly'  =>  true,
        'noName'    =>  true,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</table>
<?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/site-settings/tab-import-export.blade.php ENDPATH**/ ?>