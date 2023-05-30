<div class="input-group selector <?php echo e(isset($addon) ? 'addon dev-tools' : ''); ?> <?php echo e(isset($remove) ? 'remove' : ''); ?>"
     <?php if(isset($dataKey)): ?> data-key="<?php echo e($dataKey); ?>" <?php endif; ?>>
    <?php if(isset($addon)): ?>
        <?php echo $__env->make('form-items.partials.button-addon-test', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('form-items.dev-tools.button-dev-tools', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php if(isset($optionsBox) && $optionsBox): ?>
            <?php echo $__env->make('form-items.options-box.button-options-box', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    <?php endif; ?>
    <div class="input-container">
        <?php echo $__env->make('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::SELECTOR,
            'placeholder'   => _wpcc('Selector'),
            'classAttr'     => 'css-selector',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <?php if(isset($remove)): ?>
        <?php echo $__env->make('form-items/remove-button', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/selector.blade.php ENDPATH**/ ?>