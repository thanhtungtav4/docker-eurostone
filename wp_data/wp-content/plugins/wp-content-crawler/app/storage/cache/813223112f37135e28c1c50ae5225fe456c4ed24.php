<button class="button wpcc-button <?php echo e(isset($buttonClass) && $buttonClass ? $buttonClass : ''); ?>"
        type="button"
        title="<?php echo e(isset($title) ? $title : ''); ?>"
        <?php if(isset($data)): ?> data-wcc="<?php echo e(json_encode($data)); ?>" <?php endif; ?>
>
    <span class="<?php echo e(isset($iconClass) && $iconClass ? $iconClass : ''); ?>"></span>
</button><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/partials/button-icon.blade.php ENDPATH**/ ?>