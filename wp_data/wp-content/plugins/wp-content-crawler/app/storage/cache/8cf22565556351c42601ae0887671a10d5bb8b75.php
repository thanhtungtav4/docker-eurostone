<div class="description">
    <?php echo e(_wpcc('You can take notes about the options you configured, or anything. This tab is just for taking notes.')); ?>

</div>

<table class="wcc-settings">

    <?php echo $__env->make('form-items.combined.textarea-with-label', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_NOTE,
        'rows'          => 6,
        'showButtons'   => false,
        'placeholder'   =>  _wpcc('Notes...'),
        'noLabel'       => true,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</table><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/options-box/tabs/tab-notes.blade.php ENDPATH**/ ?>