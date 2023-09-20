

<?php
    $attr = isset($defaultAttr) && $defaultAttr ? $defaultAttr : 'text';

    $defaultData = [
        'urlSelector'                                                 => $urlSelector,
        'testType'                                                    => \WPCCrawler\Test\Test::$TEST_TYPE_SELECTOR_ATTRIBUTE,
        \WPCCrawler\Objects\Settings\Enums\SettingInnerKey::ATTRIBUTE => $attr
    ];

    if (isset($data) && $data && is_array($data)) {
        $defaultData = array_merge($defaultData, $data);
    }
?>

<tr <?php if(isset($id)): ?> id="<?php echo e($id); ?>" <?php endif; ?>
    <?php if(isset($class)): ?> class="<?php echo e($class); ?>" <?php endif; ?>
    aria-label="<?php echo e($name); ?>"
>
    <td>
        <?php echo $__env->make('form-items/label', [
            'for'   =>  $name,
            'title' =>  $title,
            'info'  =>  $info . ' ' . _wpcc_selector_attribute_info(),
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
    <td>
        <?php echo $__env->make('form-items/multiple', [
            'include'       => 'form-items/selector-with-attribute',
            'name'          => $name,
            'addon'         => isset($addon) && $addon ? $addon : 'dashicons dashicons-search',
            'data'          => $defaultData,
            'test'          => true,
            'addKeys'       => true,
            'addonClasses'  => isset($addonClasses) && $addonClasses ? $addonClasses : 'wcc-test-selector-attribute',
            'defaultAttr'   => $attr,
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
</tr><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/combined/multiple-selector-with-attribute.blade.php ENDPATH**/ ?>