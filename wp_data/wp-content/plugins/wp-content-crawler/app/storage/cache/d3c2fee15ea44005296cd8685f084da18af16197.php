<div class="wcc-settings-title">
    <h3><?php echo e(_wpcc('SEO')); ?></h3>
    <span><?php echo e(_wpcc('Set meta keywords and description keys')); ?></span>
</div>

<table class="wcc-settings">
    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_META_KEYWORDS_META_KEY,
        'title'     =>  _wpcc('Meta Keywords Key'),
        'info'      =>  _wpcc('Set the key under which the meta keywords are saved. This key depends
            on your SEO plugin. If you do not set a key, meta keywords will not be saved. If you use
            Yoast SEO, this key is <span class="highlight variable">_yoast_wpseo_metakeys</span>'),
        'isOption'  =>  $isOption,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_META_DESCRIPTION_META_KEY,
        'title'     =>  _wpcc('Meta Description Key'),
        'info'      =>  _wpcc('Set the key under which the meta description is saved. This key depends
            on your SEO plugin. If you do not set a key, meta description will not be saved. If you use
            Yoast SEO, this key is <span class="highlight variable">_yoast_wpseo_metadesc</span>'),
        'isOption'  =>  $isOption,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.textarea-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TEST_FIND_REPLACE,
        'title'         =>  _wpcc('Find and Replace Test Code'),
        'info'          =>  _wpcc('A piece of code to be used when testing find-and-replace settings below.'),
        'placeholder'   =>  _wpcc('The code which will be used to test find-and-replace settings'),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_FIND_REPLACE,
        'title' => _wpcc("Find and replace in all post pages"),
        'info'  => _wpcc('If you want some things to be replaced with some other things in <b>HTML of
            the post page</b>, this is the place. The replacements will be done just after the HTML of target
            page is retrieved and raw HTML find-replace options are applied. <b>These replacements will be
            applied to all of the active sites.</b>'),
        'data'  =>  [
            'subjectSelector' => sprintf('#%s', \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TEST_FIND_REPLACE),
        ],
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php

    /**
     * Fires before closing table tag in SEO tab of general settings page.
     *
     * @param array $settings       Existing settings and their values saved by user before
     * @param bool  $isGeneralPage  True if this is called from a general settings page.
     * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
     *                              an option is a WordPress option. This is true when this is fired from general
     *                              settings page.
     * @since 1.6.3
     */
    do_action('wpcc/view/general-settings/tab/seo', $settings, $isGeneralPage, $isOption);

    ?>

</table>
<?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/general-settings/tab-seo.blade.php ENDPATH**/ ?>