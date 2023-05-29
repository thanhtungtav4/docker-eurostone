<div class="wcc-settings-title">
    <h3><?php echo e(_wpcc('Scheduling')); ?></h3>
    <span><?php echo e(_wpcc('Set time intervals to crawl the sites')); ?></span>
</div>

<table class="wcc-settings">
    <?php if($isGeneralPage): ?>
        
        <?php echo $__env->make('form-items.combined.checkbox-with-label', [
            'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_IS_SCHEDULING_ACTIVE,
            'title'         =>  _wpcc('Scheduling is active?'),
            'info'          =>  _wpcc('If you want to activate automated checking and crawling for the
                active sites, check this.'),
            'dependants'    =>  '["#url-collection-interval", "#post-crawling-interval"]',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <?php echo $__env->make('form-items.combined.select-with-label', [
            'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_INTERVAL_URL_COLLECTION,
            'title'     =>  _wpcc('Post URL Collection Interval'),
            'info'      =>  _wpcc('Set interval for post URL collection.'),
            'options'   =>  $intervals,
            'isOption'  =>  $isOption,
            'id'        => 'url-collection-interval',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <?php echo $__env->make('form-items.combined.select-with-label', [
            'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_INTERVAL_POST_CRAWL,
            'title'     =>  _wpcc('Post Crawl Interval'),
            'info'      =>  _wpcc('Set interval for post crawling, i.e. saving posts by visiting collected URLs.'),
            'options'   =>  $intervals,
            'isOption'  =>  $isOption,
            'id'        =>  'post-crawling-interval',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_MAX_PAGE_COUNT_PER_CATEGORY,
        'title'     =>  _wpcc('Maximum number of pages to crawl per category'),
        'info'      =>  _wpcc('How many pages at maximum can be crawled for each category of each
            site? Set this <b>0</b> to get all available pages for each category.'),
        'isOption'  =>  $isOption,
        'type'      =>  'number',
        'min'       =>  0,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_NO_NEW_URL_PAGE_TRIAL_LIMIT,
        'title' =>  _wpcc('Maximum number of pages to check to find new post URLs'),
        'info'  =>  _wpcc('How many pages should be checked before going back checking the first
            page of the category, if there is no new URLs found? Default is <b>0</b>,
            meaning that all of the pages will be checked until the last page.
            <br /><br />
            For example, if you say 3 pages and there is no new posts found in 3 different
            pages of a category in a row, e.g. 4th, 5th, and 6th pages, then the crawler
            goes to the first page to check for new URLs, without trying 7th and pages
            coming after 7th page.'),
        'isOption'  =>  $isOption,
        'type'      =>  'number',
        'min'       =>  0,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_RUN_COUNT_URL_COLLECTION,
        'title' =>  _wpcc('Run count for URL collection event'),
        'info'  =>  _wpcc('For example, when you set this to 2, and interval for URL collection to 1 minute,
                URL collection event will be run 2 times every minute. Default value is 1.'),
        'isOption'  =>  $isOption,
        'type'      =>  'number',
        'min'       =>  1,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_RUN_COUNT_POST_CRAWL,
        'title' =>  _wpcc('Run count for post crawling event'),
        'info'  =>  _wpcc('For example, when you set this to 3, and interval for post crawl to 2 minutes,
                post-crawling event will be run 3 times every 2 minutes. Default value is 1.'),
        'isOption'  =>  $isOption,
        'type'      =>  'number',
        'min'       =>  1,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Recrawling")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php if($isGeneralPage): ?>
        
        <?php echo $__env->make('form-items.combined.checkbox-with-label', [
            'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_IS_RECRAWLING_ACTIVE,
            'class'         =>  'label-recrawl',
            'title'         =>  _wpcc('Recrawling is active?'),
            'info'          =>  _wpcc('If you want to activate post recrawling for active sites, check this. By this way,
                the posts will be recrawled (updated).'),
            'dependants'    =>  '["#post-recrawling-interval"]',
            'id'            =>  'is-post-recrawling-active',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <?php echo $__env->make('form-items.combined.select-with-label', [
            'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_INTERVAL_POST_RECRAWL,
            'class'     =>  'label-recrawl',
            'title'     =>  _wpcc('Post Recrawl Interval'),
            'info'      =>  _wpcc('Set interval for post crawling, i.e. saving posts by visiting collected URLs.'),
            'options'   =>  $intervals,
            'isOption'  =>  $isOption,
            'id'        =>  'post-recrawling-interval',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_RUN_COUNT_POST_RECRAWL,
        'class' =>  'label-recrawl',
        'title' =>  _wpcc('Run count for post recrawling event'),
        'info'  =>  _wpcc('For example, when you set this to 3, and interval for post recrawl to 2 minutes,
                post-recrawling event will be run 3 times every 2 minutes. Default value is 1.'),
        'isOption'  =>  $isOption,
        'type'      =>  'number',
        'min'       =>  1,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_MAX_RECRAWL_COUNT,
        'class' =>  'label-recrawl',
        'title' =>  _wpcc('Maximum recrawl count per post'),
        'info'  =>  _wpcc('How many times at maximum a post can be recrawled (updated). Set this to 0 if
            you do not want to limit. Default: 0'),
        'isOption'  =>  $isOption,
        'type'      =>  'number',
        'min'       =>  0,
        'id'        =>  'max-recrawl-count',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_MIN_TIME_BETWEEN_TWO_RECRAWLS_IN_MIN,
        'class' =>  'label-recrawl',
        'title' =>  _wpcc('Minimum time between two recrawls for a post (in minutes)'),
        'info'  =>  _wpcc('At least how many minutes should pass after the last recrawl so that a post can be
            suitable for recrawling again? E.g. if you set this to 60 minutes, for a post to become suitable for
            recrawling again, there should have passed at least 60 minutes. Set 0 to set no limit.
            Default: 1440 (1 day)'),
        'isOption'  =>  $isOption,
        'type'      =>  'number',
        'min'       =>  0,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_RECRAWL_POSTS_NEWER_THAN_IN_MIN,
        'class' =>  'label-recrawl',
        'title' =>  _wpcc('Recrawl posts newer than (in minutes)'),
        'info'  =>  _wpcc('E.g. if you set this to 1440 minutes, the posts older than 1 day will not be
            recrawled. Set 0 to set no limit. Default: 43200 (1 month)'),
        'isOption'  =>  $isOption,
        'type'      =>  'number',
        'min'       =>  0,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Deleting")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php if($isGeneralPage): ?>
        
        <?php echo $__env->make('form-items.combined.checkbox-with-label', [
            'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_IS_DELETING_POSTS_ACTIVE,
            'class'         =>  'label-post-deleting',
            'title'         =>  _wpcc('Deleting is active?'),
            'info'          =>  _wpcc('If you want to activate post deleting, check this. By this way, the old posts
                that were crawled previously will be deleted. The posts will be <b>deleted permanently</b>.
                <b>They will not be sent to the trash.</b> You cannot access or retrieve the posts after they
                are deleted.'),
            'dependants'    =>  '["#post-deleting-interval"]',
            'id'            =>  'is-post-deleting-active',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <?php echo $__env->make('form-items.combined.select-with-label', [
            'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_INTERVAL_POST_DELETE,
            'class'     =>  'label-post-deleting',
            'title'     =>  _wpcc('Post Delete Interval'),
            'info'      =>  _wpcc('Set interval for post deleting event.'),
            'options'   =>  $intervals,
            'isOption'  =>  $isOption,
            'id'        =>  'post-deleting-interval',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <?php echo $__env->make('form-items.combined.input-with-label', [
            'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_MAX_POST_COUNT_PER_POST_DELETE_EVENT,
            'class'     =>  'label-post-deleting',
            'title'     =>  _wpcc('Maximum number of posts to delete for each run'),
            'info'      =>  sprintf(_wpcc('Maximum number of posts that can be deleted for each run of post delete
                event. The posts will be deleted WordPress-way to allow other plugins to interrupt or listen to
                the process. So, a lot of work will be done each time a post is being deleted. Please keep this
                in mind. <b>Setting this option to a low value is highly recommended.</b> Default: %s, Min: %s'), 30, 1),
            'isOption'  =>  $isOption,
            'type'      =>  'number',
            'min'       =>  1,
            'id'        =>  'max-post-delete-count',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_DELETE_POSTS_OLDER_THAN_IN_MIN,
        'class' =>  'label-post-deleting',
        'title' =>  _wpcc('Delete posts older than (in minutes)'),
        'info'  =>  sprintf(_wpcc('E.g. if you set this to 1440 minutes, the posts older than 1 day will be
            deleted. Default: 43200 (1 month), Min: %1$s'), 1),
        'isOption'  =>  $isOption,
        'type'      =>  'number',
        'min'       =>  1,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_IS_DELETE_POST_ATTACHMENTS,
        'class' =>  'label-post-deleting',
        'title' =>  _wpcc('Delete post attachments?'),
        'info'  =>  _wpcc('If you want the attachments of the posts to be deleted along with the post, check this.'),
        'id'    =>  'delete-post-attachments',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php

    /**
     * Fires before closing table tag in scheduling tab of general settings page.
     *
     * @param array $settings       Existing settings and their values saved by user before
     * @param bool  $isGeneralPage  True if this is called from a general settings page.
     * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
     *                              an option is a WordPress option. This is true when this is fired from general
     *                              settings page.
     * @since 1.6.3
     */
    do_action('wpcc/view/general-settings/tab/scheduling', $settings, $isGeneralPage, $isOption);

    ?>

</table>
<?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/general-settings/tab-scheduling.blade.php ENDPATH**/ ?>