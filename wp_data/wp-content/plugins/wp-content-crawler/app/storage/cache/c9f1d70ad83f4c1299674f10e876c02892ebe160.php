<?php
    /** @var string $name */
    /** @var string|array $optionData */
?>
<div class="input-group">
    <div class="input-container">
        <select name="<?php echo e($name); ?>" id="<?php echo e($name); ?>" <?php echo e(isset($disabled) && $disabled ? 'disabled' : ''); ?> tabindex="0">
            <?php $selectedKey = isset($settings[$name]) ? (isset($isOption) && $isOption ? $settings[$name] : $settings[$name][0]) : false; ?>
            <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $optionData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    // If the option data is an array
                    $isArr = is_array($optionData);
                    if ($isArr) {
                        // Get the option name and the dependants if there exists any
                        $optionName = \WPCCrawler\Utils::array_get($optionData, 'name');
                        $dependants = \WPCCrawler\Utils::array_get($optionData, 'dependants');
                    } else {
                        // Otherwise, option data is the name of the option and there is no dependant.
                        $optionName = $optionData;
                        $dependants = null;
                    }
                ?>

                <option value="<?php echo e($key); ?>"
                    <?php if($selectedKey && $key == $selectedKey): ?> selected="selected" <?php endif; ?>
                    <?php if($dependants): ?> data-dependants="<?php echo e($dependants); ?>" <?php endif; ?>
                ><?php echo e($optionName); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/select.blade.php ENDPATH**/ ?>