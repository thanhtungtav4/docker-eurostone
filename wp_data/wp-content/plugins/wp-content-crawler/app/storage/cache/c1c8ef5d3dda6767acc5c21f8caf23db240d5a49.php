<div class="description">
    <?php echo _wpcc("Find and replace in <b>each saved file's name</b>. These options will be applied after the file is saved
    and before any changes are made to the current item."); ?>

    <?php echo _wpcc_trans_regex(); ?>

    <?php echo _wpcc_file_options_box_tests_note(); ?>

</div>

<table class="wcc-settings">

    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_FILE_FIND_REPLACE,
        'addon'         =>  'dashicons dashicons-search',
        'data'          =>  [
            'testType'  =>  \WPCCrawler\Test\Test::$TEST_TYPE_FILE_FIND_REPLACE,
            'extra'     =>  $dataExtra
        ],
        'test'          => true,
        'addonClasses'  => 'wcc-test-find-replace',
        'noLabel'       => true,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</table><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/options-box/tabs/file/tab-file-find-replace.blade.php ENDPATH**/ ?>