
<?php /** @var string $info */ ?>
<?php if($info): ?>
    <div <?php if(isset($class) && $class): ?> class="<?php echo e($class); ?>" <?php endif; ?>>
        <span class="name"><?php echo e($name); ?></span>
        <span class="info-value"><?php echo e($info); ?></span>
    </div>
<?php endif; ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-tester/partial/attachment-item-info.blade.php ENDPATH**/ ?>