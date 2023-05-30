<div class="description">
    <?php echo e(_wpcc('General options. Before applying the options here, the options defined in the find-replace tab will be applied.')); ?>

</div>

<table class="wcc-settings">
    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_TREAT_AS_JSON,
        'title' =>  _wpcc('Treat as JSON?'),
        'info'  =>  sprintf(_wpcc('If you check this, each item will be tried to be parsed to JSON. You can then
                use the values from the JSON using <b>[%1$s]</b> short code. When you check this, the item will be
                removed if it is not a valid JSON.'), \WPCCrawler\Objects\Enums\ShortCodeName::WCC_ITEM) . ' ' . _wpcc_wcc_item_short_code_dot_key_for_json()
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</table><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/options-box/tabs/tab-general.blade.php ENDPATH**/ ?>