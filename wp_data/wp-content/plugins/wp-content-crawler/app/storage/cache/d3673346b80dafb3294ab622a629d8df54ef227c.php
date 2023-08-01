
<?php if(!\WPCCrawler\Objects\Settings\SettingService::isSchedulingActive()): ?>
    <?php echo $__env->make('partials.alert', [
        'message' => _wpcc('Scheduling is not active. You should activate the scheduling in the general settings page'
                        . ' for the plugin to crawl posts automatically.'),
        'type' => 'warning',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/dashboard/partials/notice-passive-scheduling.blade.php ENDPATH**/ ?>