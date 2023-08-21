<button class="button wpcc-button button-primary button-large <?php echo e(isset($class) && $class ? $class : ''); ?>"
        data-wpcc-toggle="wpcc-tooltip" data-placement="right"
    <?php if(isset($title) && $title): ?> title="<?php echo e($title); ?>" <?php endif; ?>>
    <?php echo e(isset($text) ? $text : _wpcc('Submit')); ?>

</button><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/submit-button.blade.php ENDPATH**/ ?>