<div class="sidebar-section expanded <?php echo e(isset($class) ? $class : ''); ?>">
    
    <div class="section-title">
        
        <span><?php echo e($title); ?></span>

        
        <div class="section-controls">
            <?php if(isset($buttons) && is_array($buttons)): ?>
                <?php $__currentLoopData = $buttons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="section-title-button <?php echo e($class); ?>"></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <span class="section-title-button dashicons dashicons-arrow-up toggleExpand"></span>
        </div>
    </div>

    
    <div class="section-content"></div>
</div><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/dev-tools/sidebar-section.blade.php ENDPATH**/ ?>