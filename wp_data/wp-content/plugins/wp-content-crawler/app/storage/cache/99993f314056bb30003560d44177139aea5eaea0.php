

<?php
/** @var string $name */
$optionsValues = array_values($options);
$hasOptGroups = isset($optionsValues[0]) && is_array($optionsValues[0]);

$selected = isset($selected) ? $selected : (isset($settings[$name]) && $settings[$name][0] ? unserialize($settings[$name][0]) : []);

?>

<div class="input-group multi-select">
    <div class="input-container">
        <select name="<?php echo e($name); ?>[]" id="<?php echo e($name); ?>[]" multiple="multiple">
            
            <?php if($hasOptGroups): ?>
                
                <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optGroupName => $optGroupValues): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <optgroup label="<?php echo e($optGroupName); ?>">
                        
                        <?php echo $__env->make('form-items.partials.select-options', [
                            'options'   => $optGroupValues,
                            'selected'  => $selected,
                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </optgroup>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php else: ?>
                
                <?php echo $__env->make('form-items.partials.select-options', [
                    'options'   => $options,
                    'selected'  => $selected,
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
        </select>
    </div>
</div><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/multi-select.blade.php ENDPATH**/ ?>