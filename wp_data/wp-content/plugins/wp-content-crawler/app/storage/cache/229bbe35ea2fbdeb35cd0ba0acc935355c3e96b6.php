

<div class="input-group">
    <div class="input-container">
        <?php echo $__env->make('form-items.partials.categories', [
            'name'          => $name,
            'categories'    => $categories,
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/category-select.blade.php ENDPATH**/ ?>