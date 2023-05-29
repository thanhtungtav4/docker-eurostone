<div class="wcc-settings-title">
    <h3><?php echo e(_wpcc('Notes')); ?></h3>
    <span><?php echo e(_wpcc('You can write your notes about this site here. It is just for you to keep notes.')); ?></span>
</div>

<table class="wcc-settings">
    
    <?php echo $__env->make('form-items.combined.template-editor-with-label', [
        'name'      => \WPCCrawler\Objects\Settings\Enums\SettingKey::NOTES,
        'title'     => _wpcc('Notes'),
        'info'      => _wpcc('Write anything...'),
        'height'    => 450
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php

    /** @var int $postId */
    /**
     * Fires before closing table tag in notes tab of site settings page.
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/tab/notes', $settings, $postId);

    ?>
</table>
<?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/site-settings/tab-notes.blade.php ENDPATH**/ ?>