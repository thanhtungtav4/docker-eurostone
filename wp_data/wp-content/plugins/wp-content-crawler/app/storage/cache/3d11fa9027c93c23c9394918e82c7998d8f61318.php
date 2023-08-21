

<tr>
    <td class="detail-name">
        <span class="detail-name"><?php echo e($name); ?></span>
    </td>
    <td class="detail-value">
        
        <?php if(is_bool($content)): ?>
            <span class="dashicons dashicons-<?php echo e($content ? 'yes' : 'no'); ?>"></span>

        <?php elseif(is_array($content)): ?>
            <?php echo $__env->make('site-tester.partial.list', [
                'content' => $content
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php elseif(!$content): ?>
            <span class="no-result">-</span>

        <?php else: ?>
            <?php echo $content; ?>

        <?php endif; ?>
    </td>
</tr><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-tester/partial/single-detail.blade.php ENDPATH**/ ?>