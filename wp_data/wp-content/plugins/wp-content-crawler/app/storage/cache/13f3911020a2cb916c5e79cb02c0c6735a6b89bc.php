<?php /** @var array $testHistory */ ?>
<div class="details test-history" id="test-history">

    
    <h2>
        <span><?php echo e(_wpcc('Recent Tests')); ?></span>
        <div class="toggle-indicator">
            <span class="dashicons toggle dashicons-arrow-up"></span>
        </div>
    </h2>

    
    <div class="inside">
        <?php if($testHistory): ?>
        <table>
            
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th><?php echo e(_wpcc("Site")); ?></th>
                    <th><?php echo e(_wpcc("Test Type")); ?></th>
                    <th><?php echo e(_wpcc("Test URL")); ?></th>
                    <th class="delete-all-container"><a href="#" role="button" class="delete-all"><?php echo e(_wpcc('Delete All')); ?></a></th>
                </tr>
            </thead>

            
            <tbody>
                <?php $i = sizeof($testHistory); ?>
                <?php $__currentLoopData = $testHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('site-tester.partial.test-history-item', [
                        'number'    => $i--,
                        'siteId'    => $history['siteId'],
                        'siteName'  => $history['siteName'],
                        'testName'  => $history['testName'],
                        'testKey'   => $history['testKey'],
                        'testUrl'   => $history['testUrl']
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php else: ?>
            <span>
                <?php echo e(_wpcc("No previous tests.")); ?>

            </span>
        <?php endif; ?>
    </div>

</div><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-tester/test-history.blade.php ENDPATH**/ ?>