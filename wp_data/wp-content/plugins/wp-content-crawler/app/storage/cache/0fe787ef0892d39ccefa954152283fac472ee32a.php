<div class="wcc-settings-title">
    <h3><?php echo e(_wpcc('Filters')); ?></h3>
    <span><?php echo e(_wpcc('Filters that will be applied in the post pages...') . ' ' . _wpcc_filter()); ?></span>
</div>

<table class="wcc-settings">

    <?php echo $__env->make('form-items.combined.filter-with-label', [
        'name'       =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_DATA_FILTERS,
        'title'      =>  _wpcc('Post data filters'),
        'info'       =>  _wpcc('Define filters that will be applied for the post data.') . _wpcc_filter(true),
        'class'      => 'site-settings-filters',
        'eventGroup' => \WPCCrawler\Objects\Events\Enums\EventGroupKey::POST_DATA
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</table><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/site-settings/tab-filters.blade.php ENDPATH**/ ?>