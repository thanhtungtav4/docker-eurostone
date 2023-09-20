<?php if(isset($urls) && $urls): ?>
    <h3><?php echo e(_wpcc('Post URLs')); ?></h3>
    <?php echo $__env->make('site-tester/urls-with-test', [
        'urls'      =>  $urls,
        'testType'  =>  \WPCCrawler\Test\Enums\TestType::POST
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<?php if(isset($nextPageUrl) && $nextPageUrl): ?>
    <h3><?php echo e(_wpcc('Next Page URL')); ?></h3>
    <div class="next-page-url">
        <?php echo $__env->make('site-tester/button-test-this', ['url' => $nextPageUrl, 'type' => 'test_category', 'class' => 'test-next-page'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <a target="_blank" href="<?php echo e($nextPageUrl); ?>"><?php echo e($nextPageUrl); ?></a>
    </div>
<?php endif; ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-tester/category-test.blade.php ENDPATH**/ ?>