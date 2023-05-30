<label for="<?php echo e(isset($for) ? $for : ''); ?>" <?php if(isset($class)): ?> class="<?php echo e($class); ?>" <?php endif; ?>>
    <?php echo e($title); ?>

</label>

<?php if(isset($markRequired) && $markRequired): ?>
    <span class="required" title="<?php echo e(_wpcc('Required')); ?>">*</span>
<?php endif; ?>

<?php if(isset($info)): ?>
    <div class="info-button"><span class="dashicons dashicons-info"></span></div>
    <div style="clear: both;"></div>
    <div class="info-text hidden"><?php echo $info; ?></div>
<?php endif; ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/label.blade.php ENDPATH**/ ?>