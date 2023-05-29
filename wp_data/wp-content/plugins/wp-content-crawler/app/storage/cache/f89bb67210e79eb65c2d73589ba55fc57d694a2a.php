<?php if(\WPCCrawler\Objects\Informing\Informer::getInfos()): ?>
    <div class="info-list-container">
        <?php if(!isset($noTitle) || !$noTitle): ?>
            <span class="title"><?php echo e(_wpcc("Information")); ?></span>
        <?php endif; ?>
        <ul>
            <?php $__currentLoopData = \WPCCrawler\Objects\Informing\Informer::getInfos(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <?php /** @param WPCCrawler\Objects\Informing\Information $info */ ?>
                    <div class="message">
                        <span class="name"><?php echo e(_wpcc('Message')); ?>:</span>
                        <span class="description"><?php echo e($info->getMessage()); ?></span>
                    </div>

                    <?php if($info->getDetails()): ?>
                        <div class="details">
                            <span class="name"><?php echo e(_wpcc('Details')); ?>:</span>
                            <span class="description"><?php echo e($info->getDetails()); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="type">
                        <span class="name"><?php echo e(_wpcc('Type')); ?>:</span>
                        <span class="description"><?php echo e($info->getType()); ?></span>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/partials/info-list.blade.php ENDPATH**/ ?>