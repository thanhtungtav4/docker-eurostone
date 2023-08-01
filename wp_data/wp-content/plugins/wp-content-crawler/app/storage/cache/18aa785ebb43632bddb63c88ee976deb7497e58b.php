<?php
$values = [5, 10, 15, 20, 25, 30, 40, 50, 75, 100];
$options = [];
foreach($values as $value) $options[$value] = $value;
?>

<form action="admin-post.php" method="post">
    
    <?php echo $__env->make('partials.form-nonce-and-action', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('form-items.select', [
        'name' => $countOptionName,
        'options' => $options,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</form><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/dashboard/partials/select-table-item-count.blade.php ENDPATH**/ ?>