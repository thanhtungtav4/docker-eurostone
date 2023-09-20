<button type="button" class="button wpcc-button wcc-options-box" title="<?php echo e(_wpcc("Options")); ?>"
        data-settings="<?php echo e(isset($optionsBox) && is_array($optionsBox) ? json_encode($optionsBox) : '{}'); ?>">
    <span class="dashicons dashicons-admin-settings"></span>
    <input type="hidden" name="<?php echo e($name . '[options_box]'); ?>" value="<?php echo e(isset($value['options_box']) ? $value['options_box'] : '{}'); ?>">
    <div class="summary-colors"></div>
</button><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/options-box/button-options-box.blade.php ENDPATH**/ ?>