<ul class="post-url-list">
    <?php $__currentLoopData = $urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li>
            <div class="number"><span><?php echo e($i + 1); ?>.</span></div>
            <div class="controls">
                <?php echo $__env->make('site-tester/button-test-this', ['url' => $url["data"], 'type' => $testType], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="thumbnail-container <?php if(isset($hideThumbnails) && $hideThumbnails): ?> <?php echo e('hidden'); ?> <?php endif; ?>">
                <?php if(isset($url["thumbnail"])): ?>
                    <a href="<?php echo e($url["thumbnail"]); ?>"
                       target="_blank"
                       data-html="true"
                       data-wpcc-toggle="wpcc-tooltip"
                       data-placement="right"
                       title="<img src='<?php echo e($url["thumbnail"]); ?>' />"
                    >
                        <img class="small" src="<?php echo e($url["thumbnail"]); ?>" width="30" height="30" alt="">
                    </a>
                <?php endif; ?>
            </div>
            <div class="post-url">
                <a target="_blank" href="<?php echo e($url["data"]); ?>"><?php echo e($url["data"]); ?></a>
            </div>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-tester/urls-with-test.blade.php ENDPATH**/ ?>