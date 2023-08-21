<?php

/** @var \WPCCrawler\Objects\Crawling\Data\Taxonomy\TaxonomyItem $item */
$content = $item->getData();

?>

<div class="post-taxonomy-item">
    
    <div class="post-taxonomy-name">
        <span class="name"><?php echo e(_wpcc("Taxonomy")); ?></span>
        <span><?php echo e($item->getTaxonomy()); ?></span>
    </div>

    
    <div class="post-meta-append">
        <span class="name"><?php echo e(_wpcc("Append")); ?></span>
        <span class="dashicons dashicons-<?php echo e($item->isAppend() ? 'yes' : 'no'); ?>"></span>
    </div>

    
    <div class="post-taxonomy-value">
        <span class="name"><?php echo e(_wpcc("Value")); ?></span>
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
</div><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-tester/partial/custom-post-taxonomy-item.blade.php ENDPATH**/ ?>