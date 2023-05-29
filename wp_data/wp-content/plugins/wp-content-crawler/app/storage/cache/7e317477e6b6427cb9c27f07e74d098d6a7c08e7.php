<div class="input-group find-replace-in-custom-meta <?php echo e(isset($addon) ? 'addon' : ''); ?> <?php echo e(isset($remove) ? 'remove' : ''); ?>"
     <?php if(isset($dataKey)): ?> data-key="<?php echo e($dataKey); ?>" <?php endif; ?>>
    <?php if(isset($addon)): ?>
        <?php echo $__env->make('form-items.partials.button-addon-test', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <div class="input-container">
        <?php echo $__env->make('form-items.input-with-inner-key', [
            'type'          => 'checkbox',
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::REGEX,
            'titleAttr'     => _wpcc('Regex?'),
            'showTooltip'   => true,
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::META_KEY,
            'placeholder'   => _wpcc('Meta key'),
            'classAttr'     => 'meta-key',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::FIND,
            'placeholder'   => _wpcc('Find'),
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::REPLACE,
            'placeholder'   => _wpcc('Replace'),
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <?php if(isset($remove)): ?>
        <?php echo $__env->make('form-items/remove-button', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/find-replace-in-custom-meta.blade.php ENDPATH**/ ?>