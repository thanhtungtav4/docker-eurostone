<a role="button" class="toggle"><?php echo e($toggleText); ?></a>

<div class="toggleable <?php if(isset($hidden) && $hidden): ?> hidden <?php endif; ?>" id="<?php echo e($id); ?>">
    <div class="section-title">
        <?php echo e($title); ?>

    </div>

    <textarea class="data" rows="16"><?php echo e($content); ?></textarea>
</div><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-tester/partial/toggleable-textarea.blade.php ENDPATH**/ ?>