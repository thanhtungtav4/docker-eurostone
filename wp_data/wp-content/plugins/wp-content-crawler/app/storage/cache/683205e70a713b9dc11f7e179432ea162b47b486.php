<div class="input-group media <?php echo e(isset($remove) ? ' remove ' : ''); ?>"
     <?php if(isset($dataKey)): ?> data-key="<?php echo e($dataKey); ?>" <?php endif; ?>
>
    <?php echo $__env->make('form-items.partials.button-media-viewer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <div class="media-preview">
        <a href="#">
            <div class="image"></div>
        </a>
    </div>

    <div class="input-container media-container">
        <?php echo $__env->make('form-items.input-with-inner-key', [
            'innerKey'    => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::ITEM_ID,
            'type'        => 'number',
            'placeholder' => _wpcc('Image ID'),
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <?php if(isset($remove)): ?>
        <?php echo $__env->make('form-items/remove-button', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/media.blade.php ENDPATH**/ ?>