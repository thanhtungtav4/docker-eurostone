<div class="details" <?php if(isset($id) && $id): ?> id="<?php echo e($id); ?>" <?php endif; ?>>
    <h2>
        <span><?php echo $__env->yieldContent('title'); ?></span>

        <?php if(!isset($noToggleButton) || !$noToggleButton): ?>
            <?php echo $__env->make('partials.button-toggle-info-texts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    </h2>
    <div class="inside">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/tools/base/tool-container.blade.php ENDPATH**/ ?>