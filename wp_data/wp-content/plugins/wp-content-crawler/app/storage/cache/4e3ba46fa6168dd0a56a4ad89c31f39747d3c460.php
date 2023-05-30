<div class="description">
    <?php echo e(_wpcc('You can create templates for the current item. You can use the short codes below. In addition, you can
        use custom short codes you defined in the settings. When there are more than one template, a random one
        will be selected for each found item. When testing, find-replace, general, and calculation options will be
        applied first.')); ?>

</div>

<?php echo $__env->make('form-items.partials.short-code-buttons', [
    'buttons' => $buttonsOptionsBoxTemplates,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<table class="wcc-settings">

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_REMOVE_IF_EMPTY,
        'title' =>  _wpcc('Remove item if it is empty?'),
        'info'  =>  _wpcc('When you check this, if the item is found to be empty, it will be removed from the results.
            In other words, it will be treated as it was not found. It will not be included in the results.
            The templates will not be applied.')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-textarea-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_TEMPLATES,
        'title'         =>  _wpcc('Templates'),
        'info'          =>  _wpcc('Define your templates here. If there are more than one, a random template will be
                selected.'),
        'inputKey'      =>  'template',
        'placeholder'   =>  _wpcc('Template'),
        'addon'         =>  'dashicons dashicons-search',
        'data'          =>  [
            'testType'  =>  \WPCCrawler\Test\Test::$TEST_TYPE_TEMPLATE,
            'extra'     =>  $dataExtra
        ],
        'test'          =>  true,
        'addonClasses'  => 'wcc-test-template',
        'showButtons'   => false,
        'rows'          => 4
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</table><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/options-box/tabs/tab-templates.blade.php ENDPATH**/ ?>