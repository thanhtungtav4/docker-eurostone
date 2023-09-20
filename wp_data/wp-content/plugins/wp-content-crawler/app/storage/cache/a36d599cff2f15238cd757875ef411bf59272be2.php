<?php
/** @var string $name */
$val = isset($value) ? $value : (isset($settings[$name]) ? (is_array($settings[$name]) ? $settings[$name][0] : $settings[$name]) : '');
$val = isset($inputKey) && $inputKey && isset($val[$inputKey]) ? $val[$inputKey] : $val;

$inputKeyVal = isset($inputKey) && $inputKey ? "[{$inputKey}]" : '';

?>

<?php if(!isset($showButtons) || $showButtons): ?>
    <?php echo $__env->make('form-items.partials.short-code-buttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<div class="input-group textarea <?php echo e(isset($addon) ? 'addon' : ''); ?> <?php echo e(isset($remove) ? 'remove' : ''); ?>"
<?php if(isset($dataKey)): ?> data-key="<?php echo e($dataKey); ?>" <?php endif; ?>>
    <?php if(isset($addon)): ?>
        <?php echo $__env->make('form-items.partials.button-addon-test', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    <div class="input-container">
        <textarea <?php if(!isset($noName) || !$noName): ?> name="<?php echo e($name); ?><?php echo e($inputKeyVal); ?>" <?php endif; ?> id="<?php echo e($name); ?><?php echo e($inputKeyVal); ?>"
                  <?php if(isset($cols)): ?> cols="<?php echo e($cols); ?>" <?php endif; ?>
                  rows="<?php echo e(isset($rows) ? $rows : '10'); ?>"
                  <?php if(isset($placeholder)): ?> placeholder="<?php echo e($placeholder); ?>" <?php endif; ?>
                  <?php if(isset($disabled)): ?> disabled <?php endif; ?>
                  <?php if(isset($readOnly)): ?> readonly="readonly" <?php endif; ?>
        ><?php echo $val; ?></textarea>
    </div>

    <?php if(isset($remove)): ?>
        <?php echo $__env->make('form-items/remove-button', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/textarea.blade.php ENDPATH**/ ?>