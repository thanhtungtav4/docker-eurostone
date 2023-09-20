<button
        class="button wpcc-button wcc-addon <?php echo e(isset($test) ? 'wcc-test' : ''); ?> <?php echo e(isset($addonClasses) ? $addonClasses : ''); ?>"
        title="<?php echo e(isset($addonTitle) ? $addonTitle : _wpcc('Test')); ?>"
        <?php if(isset($data)): ?> data-wcc="<?php echo e(json_encode($data)); ?>" <?php endif; ?>
>
    <span class="<?php echo e($addon); ?>"></span>
</button><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/partials/button-addon-test.blade.php ENDPATH**/ ?>