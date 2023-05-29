

<tr <?php if(isset($id)): ?> id="<?php echo e($id); ?>" <?php endif; ?>
    <?php if(isset($class)): ?> class="<?php echo e($class); ?>" <?php endif; ?>
    aria-label="<?php echo e($name); ?>"
>
    <td>
        <?php echo $__env->make('form-items/label', [
            'for'   =>  $name,
            'title' =>  $title,
            'info'  =>  $info
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
    <td>
        <div class="inputs">
            <?php echo $__env->make('form-items.multi-checkbox', [
                'name'      => $name,
                'options'   => $options,
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </td>
</tr><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/combined/multi-checkbox-with-label.blade.php ENDPATH**/ ?>