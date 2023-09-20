<?php /** @var array $content */ ?>

<ol>
    <?php $__currentLoopData = $content; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(!is_array($v)): ?>
            <li><?php echo $v; ?></li>
        <?php else: ?>
            <?php echo $__env->make('site-tester.partial.list', [
                'content' => $v
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ol><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-tester/partial/list.blade.php ENDPATH**/ ?>