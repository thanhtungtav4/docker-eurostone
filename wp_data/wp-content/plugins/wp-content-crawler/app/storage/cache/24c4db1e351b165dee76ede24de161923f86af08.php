<div class="wrap container-general-settings">
    <h1><?php echo e(_wpcc('General Settings')); ?></h1>

    <?php echo $__env->make('partials.success-alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php if(isset($confirmationMessage) && $confirmationMessage): ?>
        <?php echo $__env->make('partials.alert', [
            'message' => $confirmationMessage
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    <form action="admin-post.php" method="post" id="post">
        

        
        <?php echo $__env->make('partials.form-nonce-and-action', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <?php echo $__env->make('form-items.partials.form-button-container', ['class' => 'top right', 'id' => 'submit-top'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="details">
            <div class="inside">
                <div class="panel-wrap wcc-settings-meta-box wcc-general-settings">

                    <?php echo $__env->make('partials/form-error-alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <?php echo $__env->make('general-settings/settings', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>
        </div>

        
        <?php echo $__env->make('form-items.partials.form-button-container', ['id' => 'submit-bottom'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <div class="reset-container">
            <input type="submit" name="reset" class="button right"
                   value="<?php echo e(_wpcc('Reset General Settings')); ?>"
                   title="<?php echo e(_wpcc('Reset the general settings to their defaults')); ?>"
            >
        </div>
    </form>

</div><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/general-settings/main.blade.php ENDPATH**/ ?>