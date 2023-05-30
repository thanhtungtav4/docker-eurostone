<div class="input-group custom-post-taxonomy <?php echo e(isset($remove) ? 'remove' : ''); ?>"
     <?php if(isset($dataKey)): ?> data-key="<?php echo e($dataKey); ?>" <?php endif; ?>>
    <div class="input-container">
        <?php echo $__env->make('form-items.input-with-inner-key', [
            'type'          => 'checkbox',
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::APPEND,
            'titleAttr'     => _wpcc('Append?'),
            'showTooltip'   => true,
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::TAXONOMY,
            'placeholder'   => _wpcc('Taxonomy name...'),
            'classAttr'     => 'meta-key',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::VALUE,
            'placeholder'   => _wpcc('Taxonomy value...'),
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <?php if(isset($remove)): ?>
        <?php echo $__env->make('form-items/remove-button', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/custom-post-taxonomy.blade.php ENDPATH**/ ?>