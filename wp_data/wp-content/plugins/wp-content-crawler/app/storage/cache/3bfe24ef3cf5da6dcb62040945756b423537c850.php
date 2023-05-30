<?php

/**
 * @var array $defaultTemplateOptions Default options for the template form items
 */
$defaultTemplateOptions = [
    'inputKey'      =>  'template',
    'placeholder'   =>  _wpcc('Template...'),
    'addKeys'       =>  true,
    'remove'        =>  true,
    'addon'         =>  'dashicons dashicons-search',
    'data'          =>  [
        'testType'  =>  \WPCCrawler\Test\Test::$TEST_TYPE_FILE_TEMPLATE,
        'extra'     =>  $dataExtra
    ],
    'test'          =>  true,
    'addonClasses'  => 'wcc-test-template',
    'showButtons'   => false,
    'rows'          => 4
];

?>

<div class="description">
    <?php echo e(_wpcc('You can create templates for the current file. You can use the short codes below. In addition, you can
        use custom short codes you defined in the settings. When there are more than one template, a random one
        will be selected for each found item. Before applying the options in this tab, find-replace and file operations
        options will be applied, respectively.')); ?>

    <?php echo _wpcc_file_options_box_tests_note(); ?>

    <?php echo _wpcc('Values of the short codes about the files might change when they are applied to saved files.'); ?>

</div>


<?php echo $__env->make('form-items.partials.short-code-buttons', [
    'buttons' => array_merge($buttonsOptionsBoxTemplates, $buttonsFileOptionsBoxTemplates),
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<table class="wcc-settings">

    
    <?php echo $__env->make('form-items.combined.multiple-textarea-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_TEMPLATES_FILE_NAME,
        'title' => _wpcc('File name templates'),
        'info'  => sprintf(
                _wpcc('Define templates for the name of the file. File extension will be added to the name automatically.
                    Short codes in file names will be treated differently. Opening and closing brackets for the short codes
                    will be replaced with <b>%1$s</b> and <b>%2$s</b>, respectively. You can write short codes regularly.
                    This is just to inform you so that you do not get surprised when you see opening and closing brackets
                    of the short codes are changed in the test results.'),
                \WPCCrawler\Objects\File\FileService::SC_OPENING_BRACKETS,
                \WPCCrawler\Objects\File\FileService::SC_CLOSING_BRACKETS
            ) . ' ' . _wpcc_trans_more_than_one_random_one(),
        'class' => 'file-template',
        'id'    => 'file-name-templates',
    ] + $defaultTemplateOptions, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-textarea-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_TEMPLATES_MEDIA_TITLE,
        'title' => _wpcc('Media title templates'),
        'info'  => _wpcc('Define templates for the title of the media that will be created for the file.') . ' ' . _wpcc_trans_more_than_one_random_one(),
        'class' => 'file-template file-media-template',
        'id'    => 'media-title-templates',
    ] + $defaultTemplateOptions, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-textarea-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_TEMPLATES_MEDIA_DESC,
        'title' => _wpcc('Media description templates'),
        'info'  => _wpcc('Define templates for the description of the media that will be created for the file.') . ' ' . _wpcc_trans_more_than_one_random_one(),
        'class' => 'file-template file-media-template',
        'id'    => 'media-description-templates',
    ] + $defaultTemplateOptions, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-textarea-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_TEMPLATES_MEDIA_CAPTION,
        'title' => _wpcc('Media caption templates'),
        'info'  => _wpcc('Define templates for the caption of the media that will be created for the file.') . ' ' . _wpcc_trans_more_than_one_random_one(),
        'class' => 'file-template file-media-template',
        'id'    => 'media-caption-templates',
    ] + $defaultTemplateOptions, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-textarea-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_TEMPLATES_MEDIA_ALT_TEXT,
        'title' => _wpcc('Media alternate text templates'),
        'info'  => _wpcc('Define templates for the alt text of the media that will be created for the file.') . ' ' . _wpcc_trans_more_than_one_random_one(),
        'class' => 'file-template file-media-template',
        'id'    => 'media-alt-templates',
    ] + $defaultTemplateOptions, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</table><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/options-box/tabs/file/tab-file-templates.blade.php ENDPATH**/ ?>