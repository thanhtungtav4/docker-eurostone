

<?php
/** @var string $name */
/** @var string $eventGroup */
$val = isset($value) ? $value : (isset($settings[$name]) ? (isset($isOption) && $isOption ? $settings[$name] : $settings[$name][0]) : '');
$config = [
    'eventGroup' => $eventGroup
];

$additionalClasses = isset($filterClass) ? $filterClass : '';
?>

<div class="input-group filter-setting <?php echo e($additionalClasses); ?>" data-config="<?php echo e(json_encode($config)); ?>">
    <div class="input-container">
        <input type="hidden"
               class="filter-serialized-input"
               id="<?php echo e(isset($name) ? $name : ''); ?>"
               name="<?php echo e(isset($name) ? $name : ''); ?>"
               value="<?php echo e($val); ?>" />
        <div class="filters-loading">
            <span><?php echo e(_wpcc('Loading...')); ?></span>
        </div>
    </div>
</div><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/filter.blade.php ENDPATH**/ ?>