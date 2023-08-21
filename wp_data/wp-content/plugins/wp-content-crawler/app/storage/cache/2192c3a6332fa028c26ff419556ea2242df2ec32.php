

<?php
    /** @var int $siteId */
    /** @var string $siteName */
    /** @var string $testKey */
    /** @var string $testUrl */

    $nameExists = $siteName !== null && is_string($siteName);
    $testData = [
        'testKey' => $testKey,
        'siteId'  => $siteId,
        'testUrl' => $testUrl,
        'exists'  => $nameExists
    ];
?>

<tr class="test-history-item" data-test="<?php echo e(json_encode($testData)); ?>">
    <td class="controls-leading">
        <?php echo $__env->make('form-items.partials.button-addon-test', [
            'addon' =>  'dashicons dashicons-search',
            'test'  => true,
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
    <td class="item-number"><?php echo e($number); ?></td>
    <td class="site-name">
        <?php if($nameExists): ?>
            <a href="<?php echo get_edit_post_link($siteId); ?>" target="_blank">
                <?php echo e($siteName); ?>

            </a>
        <?php else: ?>
            <?php echo e(_wpcc('Not found')); ?>

        <?php endif; ?>
    </td>
    <td class="test-type">
        <?php echo e($testName); ?>

    </td>
    <td class="test-url">
        <a href="<?php echo e($testUrl); ?>" target="_blank"><?php echo e($testUrl); ?></a>
    </td>
    <td class="controls-trailing">
        <?php echo $__env->make('form-items.remove-button', [
            'disableSort' => true
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
</tr><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-tester/partial/test-history-item.blade.php ENDPATH**/ ?>