<div class="input-group exchange-element-attributes <?php echo e(isset($addon) ? 'addon dev-tools' : ''); ?> <?php echo e(isset($remove) ? 'remove' : ''); ?>"
     <?php if(isset($dataKey)): ?> data-key="<?php echo e($dataKey); ?>" <?php endif; ?>>
    <?php if(isset($addon)): ?>
        <?php echo $__env->make('form-items.partials.button-addon-test', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('form-items.dev-tools.button-dev-tools', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <div class="input-container">
        <?php echo $__env->make('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::SELECTOR,
            'placeholder'   => _wpcc('Selector'),
            'classAttr'     => 'css-selector',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::ATTRIBUTE_1,
            'placeholder'   => _wpcc('Attribute 1'),
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('form-items.input-with-inner-key', [
            'innerKey'      => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::ATTRIBUTE_2,
            'placeholder'   => _wpcc('Attribute 2'),
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <?php if(isset($remove)): ?>
        <?php echo $__env->make('form-items/remove-button', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/exchange-element-attributes.blade.php ENDPATH**/ ?>