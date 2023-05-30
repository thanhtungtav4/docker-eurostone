

<?php
    $attr = isset($defaultAttr) && $defaultAttr ? $defaultAttr : 'text';

    $defaultData = [
        'testType'  =>  \WPCCrawler\Test\Test::$TEST_TYPE_FIND_REPLACE,
    ];

    if (isset($data) && $data && is_array($data)) {
        $defaultData = array_merge($defaultData, $data);
    }
?>

<tr <?php if(isset($id)): ?> id="<?php echo e($id); ?>" <?php endif; ?>
    <?php if(isset($class)): ?> class="<?php echo e($class); ?>" <?php endif; ?>
    aria-label="<?php echo e($name); ?>"
>
    <?php if(!isset($noLabel) || !$noLabel): ?>
        <td>
            <?php echo $__env->make('form-items/label', [
                'for'   =>  $name,
                'title' =>  $title,
                'info'  =>  $info . ' ' . _wpcc_trans_regex(),
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
    <?php endif; ?>

    <td>
        <?php echo $__env->make('form-items/multiple', [
            'include'       => 'form-items/find-replace',
            'name'          => $name,
            'addon'         => isset($addon) && $addon ? $addon : 'dashicons dashicons-search',
            'data'          => $defaultData,
            'test'          => true,
            'addKeys'       => true,
            'remove'        => true,
            'addonClasses'  => isset($addonClasses) && $addonClasses ? $addonClasses : 'wcc-test-find-replace',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
</tr><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/combined/multiple-find-replace-with-label.blade.php ENDPATH**/ ?>