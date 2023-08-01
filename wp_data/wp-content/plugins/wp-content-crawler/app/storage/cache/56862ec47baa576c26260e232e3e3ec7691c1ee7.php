
<div class="content <?php echo $__env->yieldContent('content-class'); ?>" <?php if(isset($id) && $id): ?> id="<?php echo $id; ?>" <?php endif; ?>>
    <div class="details">
        <h2>
            <span>
                
                <?php echo $__env->yieldContent('title'); ?>
            </span>

            <div class="header">
                <?php echo $__env->yieldContent('header'); ?>
            </div>
        </h2>
        <div class="inside">
            <div class="panel-wrap wcc-settings-meta-box">
                
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/dashboard/partials/section.blade.php ENDPATH**/ ?>