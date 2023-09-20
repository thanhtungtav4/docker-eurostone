
<?php
    /** @var array $categories */
    $selectedTaxonomy = isset($categories[0]) && $categories[0]['taxonomy'] ? $categories[0]['taxonomy'] : 'category';
    $addTaxonomyInput = isset($addTaxonomyInput) && $addTaxonomyInput;
    $taxonomyInputName = isset($taxonomyInputName) && $taxonomyInputName ? $taxonomyInputName : null;
?>
<select name="<?php echo e($name); ?>" id="<?php echo e($name); ?>">
    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            /** @var array $categoryData */
            $categoryId         = $categoryData['id'];
            $categoryName       = $categoryData['name'];
            $categoryTaxonomy   = $categoryData['taxonomy'];
            $isSelected         = isset($selectedId) && $selectedId && $categoryId == $selectedId;
        ?>
        <option value="<?php echo e($categoryId); ?>" data-taxonomy="<?php echo e($categoryTaxonomy); ?>"
                <?php if($isSelected): ?> selected="selected" <?php endif; ?>><?php echo $categoryName; ?></option>

        
        <?php if($addTaxonomyInput && $isSelected): ?>
            <?php
                $selectedTaxonomy = $categoryTaxonomy;
            ?>
        <?php endif; ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>


<?php if($addTaxonomyInput && $taxonomyInputName): ?>
    <input type="hidden" class="category-taxonomy" name="<?php echo e($taxonomyInputName); ?>" value="<?php echo e($selectedTaxonomy); ?>">
<?php endif; ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/partials/categories.blade.php ENDPATH**/ ?>