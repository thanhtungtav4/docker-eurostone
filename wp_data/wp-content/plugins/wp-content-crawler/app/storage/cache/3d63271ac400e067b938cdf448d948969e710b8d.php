<?php if(isset($isResponseFromCache) && $isResponseFromCache): ?>
    <div class="from-cache">
        <span><?php echo e(_wpcc("Response retrieved from cache.")); ?></span>

        
        <?php if(isset($testUrl) && $testUrl): ?>
            <span>
                <a role="button" class="invalidate-cache-for-this-url" data-url="<?php echo e($testUrl); ?>" title="<?php echo e(_wpcc("Invalidate cache for this URL")); ?>"><?php echo e(_wpcc("Invalidate")); ?></a>
            </span>
            <span>
                <a role="button" class="invalidate-all-test-url-caches" data-url="<?php echo e($testUrl); ?>" title="<?php echo e(_wpcc("Invalidate all test URL caches")); ?>"><?php echo e(_wpcc("Invalidate all")); ?></a>
            </span>
        <?php endif; ?>
    </div>
<?php endif; ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/partials/notification-for-url-cache.blade.php ENDPATH**/ ?>