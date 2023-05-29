

<?php
    /** @var string $name */
    // Get the values from settings, if $value is not supplied.
    if(!isset($value) || !is_array($value) || !$value) {
        $value = isset($settings[$name]) && isset($settings[$name][0]) ? unserialize($settings[$name][0])[0] : [];

        // Make sure each key has a non-empty value. Checkboxes have weird behaviors. I am not sure if they always
        // have a value. Sometimes they are just keys. Sometimes they are key-value pairs.
        $valuesPrepared = [];
        foreach($value as $k => $v) $valuesPrepared[$k] = 1;
        $value = $valuesPrepared;
    }
?>

<div class="input-group multi-checkbox">
    <div class="input-container">
        <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $title): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                /** @var string $key */
                // Store all settings under 0 key. This is important because the backend function that saves the settings
                // automatically gets the array values. So, in order not to lose the keys, we should store it under a
                // number-type index. At the end, this index will end up being 0, due to getting array values in backend.
                // So, we store the settings under 0 key.
                $preparedName = "{$name}[0][{$key}]"
            ?>

            <label for="<?php echo e($preparedName); ?>">
                <input type="checkbox"
                       id="<?php echo e($preparedName); ?>"
                       name="<?php echo e($preparedName); ?>"
                       <?php if(isset($value[$key]) && $value[$key]): ?> checked="checked" <?php endif; ?> />

                <?php echo e($title); ?>

            </label>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/multi-checkbox.blade.php ENDPATH**/ ?>