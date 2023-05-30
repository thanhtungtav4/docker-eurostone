<div class="description">
    <?php echo e(_wpcc("Find and replace in each item. These options will be applied before any changes are made to the current item.")); ?> <?php echo _wpcc_trans_regex(); ?>

</div>

<table class="wcc-settings">

    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_FIND_REPLACE,
        'data'      =>  [
            'extra' =>  $dataExtra
        ],
        'noLabel'   => true
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</table><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/options-box/tabs/tab-find-replace.blade.php ENDPATH**/ ?>