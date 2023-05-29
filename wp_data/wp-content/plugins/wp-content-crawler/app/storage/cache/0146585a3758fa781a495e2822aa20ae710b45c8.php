<div class="panel-wrap wcc-settings-meta-box" data-post-id="<?php echo e($postId); ?>">
    <?php wp_nonce_field('wcc-settings-metabox', \WPCCrawler\Environment::nonceName()); ?>

    <?php echo $__env->make('partials.form-error-alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php
    /** @var int $postId */
    /**
     * Fires after opening div tag of the meta box that contains the site settings
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/main/above-meta-box', $settings, $postId);

    ?>

    
    <h2 class="nav-tab-wrapper fixable">
        <a href="#" data-tab="#tab-main"        class="nav-tab nav-tab-active"><?php echo e(_wpcc('Main')); ?></a>
        <a href="#" data-tab="#tab-category"    class="nav-tab"><?php echo e(_wpcc('Category')); ?></a>
        <a href="#" data-tab="#tab-post"        class="nav-tab"><?php echo e(_wpcc('Post')); ?></a>
        <a href="#" data-tab="#tab-templates"   class="nav-tab"><?php echo e(_wpcc('Templates')); ?></a>
        <a href="#" data-tab="#tab-filters"     class="nav-tab"><?php echo e(_wpcc('Filters')); ?></a>
        <a href="#" data-tab="#tab-notes"       class="nav-tab"><?php echo e(_wpcc('Notes')); ?></a>

        <?php

        /**
         * Fires before import export tab in tab title area of site settings page
         *
         * @param array $settings   Existing settings and their values saved by user before
         * @param int $postId       ID of the site
         * @since 1.6.3
         */
        do_action('wpcc/view/site-settings/add_tab_title', $settings, $postId);

        ?>

        <a href="#" data-tab="#tab-general-settings"
           class="nav-tab <?php echo e(isset($settings[\WPCCrawler\Objects\Settings\Enums\SettingKey::DO_NOT_USE_GENERAL_SETTINGS]) && $settings[\WPCCrawler\Objects\Settings\Enums\SettingKey::DO_NOT_USE_GENERAL_SETTINGS][0] ? '' : 'hidden'); ?> nav-tab-highlight-on"
        >
            <?php echo e(_wpcc('Settings')); ?>

        </a>

        <a href="#" data-tab="#tab-import-export-settings" class="nav-tab">
            <span class="dashicons dashicons-upload"></span>
            <span class="dashicons dashicons-download"></span>
        </a>

        <?php echo $__env->make('partials.input-url-hash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('partials.button-toggle-info-texts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </h2>

    
    <div id="tab-main" class="tab">
        <?php echo $__env->make('site-settings.tab-main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    
    <div id="tab-category" class="tab hidden">
        <?php echo $__env->make('site-settings.tab-category', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    
    <div id="tab-post" class="tab hidden">
        <?php echo $__env->make('site-settings.tab-post', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    
    <div id="tab-templates" class="tab hidden">
        <?php echo $__env->make('site-settings.tab-templates', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    
    <div id="tab-filters" class="tab hidden">
        <?php echo $__env->make('site-settings.tab-filters', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    
    <div id="tab-notes" class="tab hidden">
        <?php echo $__env->make('site-settings.tab-notes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <?php

    /**
     * Fires before import export tab in tab content area of site settings page
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/add_tab_content', $settings, $postId);

    ?>

    
    <div id="tab-import-export-settings" class="tab hidden">
        <?php echo $__env->make('site-settings.tab-import-export', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    
    <div id="tab-general-settings" class="tab hidden">
        <?php echo $__env->make('general-settings.settings', ['isPostPage' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <?php

    /**
     * Fires before closing div tag of the meta box that contains the site settings
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/main/below-meta-box', $settings, $postId);

    ?>

    
    <?php echo $__env->make('form-items.options-box.options-box-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.quick-save-settings', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</div>


<?php echo $__env->make('form-items.dev-tools.dev-tools-content-container', [
    'data' => [
        'postId' => $postId
    ]
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/site-settings/main.blade.php ENDPATH**/ ?>