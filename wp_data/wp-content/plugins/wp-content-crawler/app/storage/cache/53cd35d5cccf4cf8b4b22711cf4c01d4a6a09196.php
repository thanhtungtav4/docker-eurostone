<?php $__env->startSection('content-class'); ?> <?php $__env->stopSection(true); ?>

<?php $__env->startSection('header'); ?>
    <?php echo $__env->make('dashboard.partials.select-table-item-count', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(true); ?>

<?php $__env->startSection('title'); ?>
    <?php echo e($title); ?>

<?php $__env->stopSection(true); ?>

<?php $__env->startSection('content'); ?>
    <?php if(!empty($urls)): ?>
        <?php echo $__env->make('dashboard.partials.table-urls', [
            'urls'              => $urls,
            'fieldName'         => $fieldName,
            'dateColumnName'    => $dateColumnName,
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php else: ?>

        <?php echo e(_wpcc("No URLs.")); ?>


    <?php endif; ?>

<?php $__env->stopSection(true); ?>

<?php echo $__env->make('dashboard.partials.section', [
    'id' => isset($id) && $id ? $id : null
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/dashboard/section-last-urls.blade.php ENDPATH**/ ?>