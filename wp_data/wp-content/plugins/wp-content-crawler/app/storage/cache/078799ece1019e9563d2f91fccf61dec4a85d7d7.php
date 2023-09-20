<button class="button wpcc-button <?php echo e(isset($buttonClass) && $buttonClass ? $buttonClass : ''); ?>"
        type="button"
        title="<?php echo e(isset($title) ? $title : ''); ?>"
        <?php if(isset($id)): ?> id="<?php echo e($id); ?>" <?php endif; ?>
        <?php if(isset($data)): ?> data-wcc="<?php echo e(json_encode($data)); ?>" <?php endif; ?>
        <?php if(isset($name)): ?> name="<?php echo e($name); ?>" <?php endif; ?>
>
    <?php if(isset($iconClass) && $iconClass): ?>
        <span class="<?php echo e($iconClass); ?>"></span>
    <?php endif; ?>
    <?php echo e($text); ?>

</button><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/partials/single-button.blade.php ENDPATH**/ ?>