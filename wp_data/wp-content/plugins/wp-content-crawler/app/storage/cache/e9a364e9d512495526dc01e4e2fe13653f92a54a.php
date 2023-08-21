<?php
    /** @var array $settings */

    // Define a variable to understand if this is the general page. If not, this settings is in post settings page.
    // Take some actions according to this.
    $isGeneralPage = !isset($isPostPage) || !$isPostPage;
    $isOption = !isset($isOption) ? ($isGeneralPage ? true : false) : $isOption;

    $keyIsSchedulingActive = \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_IS_SCHEDULING_ACTIVE;
?>


<?php if($isGeneralPage): ?>
    <h2 class="nav-tab-wrapper">
        <a href="#" data-tab="#tab-gs-scheduling" class="
            nav-tab nav-tab-active
            <?php echo e(isset($settings[$keyIsSchedulingActive]) && !empty($settings[$keyIsSchedulingActive]) && $settings[$keyIsSchedulingActive][0] ? 'nav-tab-highlight-on' : 'nav-tab-highlight-off'); ?>

        ">
            <?php echo e(_wpcc('Scheduling')); ?>

        </a>
        <a href="#" data-tab="#tab-gs-post" class="nav-tab"><?php echo e(_wpcc('Post')); ?></a>
        <a href="#" data-tab="#tab-gs-translation" class="nav-tab"><?php echo e(_wpcc('Translation')); ?></a>
        <a href="#" data-tab="#tab-gs-spinning" class="nav-tab"><?php echo e(_wpcc('Spinning')); ?></a>
        <a href="#" data-tab="#tab-gs-seo" class="nav-tab"><?php echo e(_wpcc('SEO')); ?></a>
        <a href="#" data-tab="#tab-gs-notifications" class="nav-tab"><?php echo e(_wpcc('Notifications')); ?></a>
        <a href="#" data-tab="#tab-gs-advanced" class="nav-tab"><?php echo e(_wpcc('Advanced')); ?></a>

        <?php

        /**
         * Fires before advanced tab in tab title area of general settings page
         *
         * @param array $settings       Existing settings and their values saved by user before
         * @param bool  $isGeneralPage  True if this is called from a general settings page.
         * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
         *                              an option is a WordPress option. This is true when this is fired from general
         *                              settings page.
         * @since 1.6.3
         */
        do_action('wpcc/view/general-settings/add_tab_title', $settings, $isGeneralPage, $isOption);

        ?>

        <?php echo $__env->make('partials.input-url-hash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('partials/button-toggle-info-texts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </h2>


<?php else: ?>
    <div class="section-header-button-container">
        <button class="button wpcc-button" id="btn-load-general-settings"><?php echo e(_wpcc("Load General Settings")); ?></button>
        <button class="button wpcc-button" id="btn-clear-general-settings"><?php echo e(_wpcc("Clear General Settings")); ?></button>
    </div>
<?php endif; ?>


<div id="tab-gs-scheduling" class="tab<?php echo e($isGeneralPage ? '' : '-inside'); ?>">
    <?php echo $__env->make('general-settings.tab-scheduling', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>


<div id="tab-gs-post" class="tab<?php echo e($isGeneralPage ? '' : '-inside'); ?> <?php echo e($isGeneralPage ? 'hidden' : ''); ?>">
    <?php echo $__env->make('general-settings.tab-post', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>


<div id="tab-gs-translation" class="tab<?php echo e($isGeneralPage ? '' : '-inside'); ?> <?php echo e($isGeneralPage ? 'hidden' : ''); ?>">
    <?php echo $__env->make('general-settings.tab-translation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>


<div id="tab-gs-spinning" class="tab<?php echo e($isGeneralPage ? '' : '-inside'); ?> <?php echo e($isGeneralPage ? 'hidden' : ''); ?>">
    <?php echo $__env->make('general-settings.tab-spinning', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>

<?php if($isGeneralPage): ?>
    
    <div id="tab-gs-seo" class="tab hidden">
        <?php echo $__env->make('general-settings.tab-seo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    
    <div id="tab-gs-notifications" class="tab hidden">
        <?php echo $__env->make('general-settings.tab-notifications', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
<?php endif; ?>

<?php

/**
 * Fires before advanced tab content in tab content area of general settings page
 *
 * @param array $settings       Existing settings and their values saved by user before
 * @param bool  $isGeneralPage  True if this is called from a general settings page.
 * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
 *                              an option is a WordPress option. This is true when this is fired from general
 *                              settings page.
 * @since 1.6.3
 */
do_action('wpcc/view/general-settings/add_tab_content', $settings, $isGeneralPage, $isOption);

?>


<div id="tab-gs-advanced" class="tab<?php echo e($isGeneralPage ? '' : '-inside'); ?> <?php echo e($isGeneralPage ? 'hidden' : ''); ?>">
    <?php echo $__env->make('general-settings.tab-advanced', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
<?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/general-settings/settings.blade.php ENDPATH**/ ?>