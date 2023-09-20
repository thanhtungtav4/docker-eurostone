<?php
/** @var string $name */
// If the options box is enabled and there is a configuration for this form item name
if (isset($optionsBox) && $optionsBox && isset($optionsBoxConfigs) && isset($optionsBoxConfigs[$name])) {
    // Change $optionsBox variable with the given configuration so that 'button-options-box' form item can use
    // the configuration.
    $optionsBox = $optionsBoxConfigs[$name];
}

$showExport = isset($hasExportButton) && $hasExportButton;
$showImport = isset($hasImportButton) && $hasImportButton;

?>

<div class="inputs" data-name="<?php echo e($name); ?>">
    <?php if(!isset($settings[$name]) || !$settings[$name] || !$settings[$name][0]): ?>
        <?php echo $__env->make($include, [
            'name'      => $name . '[' . (isset($addKeys) ? 0 : '') . ']',
            'remove'    => true,
            'value'     => '',
            'dataKey'   => isset($addKeys) ? 0 : ''
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php $__currentLoopData = unserialize($settings[$name][0]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make($include, [
                'name'      => $name . '[' . (isset($addKeys) ? $key : '') . ']',
                'remove'    => true,
                'value'     => $value,
                'dataKey'   => $key,
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>
<?php if(!isset($max) || $max != 1): ?>
    <div style="clear: both;"></div>
    <div class="actions">
        <button class="button wpcc-button wcc-add-new" data-max="<?php echo e(isset($max) ? $max : 0); ?>"><?php echo e(_wpcc('Add New')); ?></button>

        <?php if($showExport): ?>
            <button class="button wpcc-button setting-export"><?php echo e(_wpcc('Export')); ?></button>
        <?php endif; ?>

        <?php if($showImport): ?>
            <button class="button wpcc-button setting-import"><?php echo e(_wpcc('Import')); ?></button>
        <?php endif; ?>
    </div>

    <?php if($showExport || $showImport): ?>
        <div class="setting-import-export hidden"></div>
    <?php endif; ?>

<?php endif; ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/multiple.blade.php ENDPATH**/ ?>