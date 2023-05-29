<?php /** @var array $value */ ?>
<div class="input-group category-map <?php echo e(isset($addon) ? 'addon' : ''); ?> <?php echo e(isset($remove) ? 'remove' : ''); ?>" <?php if(isset($dataKey)): ?> data-key="<?php echo e($dataKey); ?>" <?php endif; ?>>
    <?php if(isset($addon)): ?>
        <?php echo $__env->make('form-items.partials.button-addon-test', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <div class="input-container">
        <?php $selectedId = isset($value['cat_id']) ? $value['cat_id'] : null; ?>

        <?php echo $__env->make('form-items.partials.categories', [
            'selectedId'        => $selectedId,
            'name'              => $name . '[cat_id]',
            'categories'        => $categories,
            'taxonomyInputName' => $name . '[taxonomy]',
            'addTaxonomyInput'  => true
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('form-items.input-with-inner-key', [
            'innerKey' => \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::URL
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </div>
    <?php if(isset($remove)): ?>
        <?php echo $__env->make('form-items/remove-button', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/category-map.blade.php ENDPATH**/ ?>