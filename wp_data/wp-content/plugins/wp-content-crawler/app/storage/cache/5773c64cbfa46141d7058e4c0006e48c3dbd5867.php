

<?php

$singleResultView = isset($singleResultView) && $singleResultView ? $singleResultView : null;

?>

<?php if(isset($message)): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>


<ul data-results="<?php echo e(json_encode($results)); ?>">
    <?php
        $actualResults = isset($modifiedResults) ? $modifiedResults : $results;
        if (!$actualResults) $actualResults = [];
    ?>
    <?php $__currentLoopData = $actualResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php if($singleResultView): ?> <?php echo $__env->make($singleResultView, ['result' => $result], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> <?php else: ?> <code><?php echo e($result); ?></code> <?php endif; ?></li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>


<?php if(empty($actualResults)): ?>
    <span class="no-result"><?php echo e(_wpcc('No result')); ?></span>
<?php endif; ?>


<?php if(isset($modifiedResults) && $modifiedResults !== $results): ?>
    <div class="original-results">
        <a role="button" class="see-unmodified-results"><?php echo e(_wpcc("See unmodified results")); ?></a>
        <ul class="hidden">
            <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php if($singleResultView): ?> <?php echo $__env->make($singleResultView, ['result' => $result], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> <?php else: ?> <code><?php echo e($result); ?></code> <?php endif; ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>


<?php if(isset($memoryUsage) && isset($elapsedTime)): ?>
    <div class="usage">
        <span title="<?php echo e(_wpcc("Used memory")); ?>"><?php echo e($memoryUsage); ?> MB</span>
        /
        <span title="<?php echo e(_wpcc("Elapsed time")); ?>"><?php echo e($elapsedTime); ?> ms</span>
    </div>
<?php endif; ?>


<?php echo $__env->make('partials.notification-for-url-cache', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php echo $__env->make('partials.info-list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/partials/test-result.blade.php ENDPATH**/ ?>