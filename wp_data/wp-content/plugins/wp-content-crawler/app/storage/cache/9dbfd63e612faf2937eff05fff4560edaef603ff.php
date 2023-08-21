<?php

/** @var \WPCCrawler\Objects\Crawling\Data\Meta\PostMeta $item */
$content = $item->getData();

?>

<div class="post-meta-item">
    
    <div class="post-meta-key">
        <span class="name"><?php echo e(_wpcc("Meta key")); ?></span>
        <span><?php echo e($item->getKey()); ?></span>
    </div>

    
    <div class="post-meta-multiple">
        <span class="name"><?php echo e(_wpcc("Multiple")); ?></span>
        <span class="dashicons dashicons-<?php echo e($item->isMultiple() ? 'yes' : 'no'); ?>"></span>
    </div>

    
    <div class="post-meta-content">
        <span class="name"><?php echo e(_wpcc("Content")); ?></span>
        <?php if(is_array($content)): ?>
            <div>
                <ol>
                    <?php $__currentLoopData = $content; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($value); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ol>
            </div>
        <?php else: ?>
            <span><?php echo e($content); ?></span>
        <?php endif; ?>
    </div>
</div><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-tester/partial/custom-post-meta-item.blade.php ENDPATH**/ ?>