<div class="wcc-settings-title">
    <h3><?php echo e(_wpcc('Notifications')); ?></h3>
    <span><?php echo e(_wpcc('Set notification email addresses...')); ?></span>
</div>

<table class="wcc-settings">
    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_IS_NOTIFICATION_ACTIVE,
        'title' =>  _wpcc('Notifications are active?'),
        'info'  =>  _wpcc('If you want to activate notification emails, check this.')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_NOTIFICATION_EMAIL_INTERVAL_FOR_SITE,
        'title'     =>  _wpcc('Email interval'),
        'info'      =>  _wpcc("Set how many minutes should pass before sending another similar notification about
            the same site. Default: 30"),
        'isOption'  =>  $isOption,
        'type'      =>  'number',
        'min'       =>  1,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-text-with-label', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_NOTIFICATION_EMAILS,
        'title'         => _wpcc("Email addresses"),
        'info'          => _wpcc('Write email addresses to which notifications can be sent.'),
        'type'          =>  'email',
        'remove'        =>  true,
        'placeholder'   =>  _wpcc('Email address...'),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php

    /**
     * Fires before closing table tag in notifications tab of general settings page.
     *
     * @param array $settings       Existing settings and their values saved by user before
     * @param bool  $isGeneralPage  True if this is called from a general settings page.
     * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
     *                              an option is a WordPress option. This is true when this is fired from general
     *                              settings page.
     * @since 1.6.3
     */
    do_action('wpcc/view/general-settings/tab/notifications', $settings, $isGeneralPage, $isOption);

    ?>

</table>
<?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/general-settings/tab-notifications.blade.php ENDPATH**/ ?>