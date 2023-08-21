<?php $__env->startSection('content-class'); ?> <?php $__env->stopSection(true); ?>
<?php $__env->startSection('header'); ?> <?php $__env->stopSection(true); ?>

<?php $__env->startSection('title'); ?>
    <?php echo e(_wpcc("Active sites")); ?> (<?php echo e(sizeof($activeSites)); ?>)
<?php $__env->stopSection(true); ?>

<?php $__env->startSection('content'); ?>
    <?php if(!empty($activeSites)): ?>
        <table class="section-table detail-card white">
            <thead>
                <tr>
                    <th></th>
                    <th><?php echo e(_wpcc("Last")); ?></th>
                    <th><?php echo e(_wpcc("Active")); ?></th>
                    <th><?php echo e(_wpcc("Today")); ?></th>
                    <th><?php echo e(_wpcc("All")); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $activeSites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activeSite): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php /** @var \WPCCrawler\Objects\Dashboard\DashboardSite $activeSite */ ?>
                    <tr>
                        <td class="site-name">
                            <a href="<?php echo get_edit_post_link($activeSite->getSite()->ID); ?>" target="_blank">
                                <?php echo e($activeSite->getSite()->post_title); ?>

                            </a>
                        </td>
                        <td class="last-cron-dates">
                            <?php
                                $lastEventDates = [
                                    _wpcc("URL Collection") => ['date-url-collection',  $activeSite->getLastCheckedAt()],
                                    _wpcc("Post Crawl")     => ['date-post-crawl',      $activeSite->getLastCrawledAt()],
                                    _wpcc("Post Recrawl")   => ['date-post-recrawl',    $activeSite->getLastRecrawledAt()],
                                    _wpcc("Post Delete")    => ['date-post-delete',     $activeSite->getLastDeletedAt()],
                                ];
                            ?>

                            <?php $__currentLoopData = $lastEventDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventName => $mValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="<?php echo e($mValue[0]); ?>"><span><?php echo e($eventName); ?>:</span> <span class="diff-for-humans"><?php echo sprintf(_wpcc('%1$s ago'), \WPCCrawler\Utils::getDiffForHumans(strtotime($mValue[1]))); ?></span> <span class="date">(<?php echo e(\WPCCrawler\Utils::getDateFormatted($mValue[1])); ?>)</span> </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td class="active-event-types">
                            <?php
                                $activeStatuses = [
                                    _wpcc("Scheduling") => ['event-scheduling', $activeSite->isActiveScheduling()],
                                    _wpcc("Recrawling") => ['event-recrawling', $activeSite->isActiveRecrawling()],
                                    _wpcc("Deleting")   => ['event-deleting',   $activeSite->isActiveDeleting()],
                                ];
                            ?>

                            <?php $__currentLoopData = $activeStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventName => $mValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="<?php echo e($mValue[0]); ?>"><span><?php echo e($eventName); ?></span>: <span class="dashicons dashicons-<?php echo e($mValue[1] ? 'yes' : 'no'); ?>"></span></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td class="counts-today">
                            <?php
                                $countsToday = [
                                    _wpcc("Queue")   => ['count-queue',     $activeSite->getCountQueueToday()],
                                    _wpcc("Saved")   => ['count-saved',     $activeSite->getCountSavedToday()],
                                    _wpcc("Updated") => ['count-updated',   $activeSite->getCountRecrawledToday()],
                                    _wpcc("Deleted") => ['count-deleted',   $activeSite->getCountDeletedToday()],
                                ];
                            ?>

                            <?php $__currentLoopData = $countsToday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mName => $mValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="<?php echo e($mValue[0]); ?>"><span class="name"><?php echo e($mName); ?>:</span> <span class="number"><?php echo e($mValue[1]); ?></span></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td class="counts-all">
                            <?php
                                $countsAll = [
                                    _wpcc("Queue")   => ['count-queue',     $activeSite->getCountQueue()],
                                    _wpcc("Saved")   => ['count-saved',     $activeSite->getCountSaved()],
                                    _wpcc("Updated") => ['count-updated',   $activeSite->getCountRecrawled()],
                                    _wpcc("Deleted") => ['count-deleted',   $activeSite->getCountDeleted()],
                                ];
                            ?>

                            <?php $__currentLoopData = $countsAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mName => $mValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="<?php echo e($mValue[0]); ?>"><span class="name"><?php echo e($mName); ?>:</span> <span class="number"><?php echo e($mValue[1]); ?></span></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

    <?php else: ?>

        <?php echo e(_wpcc("No active sites.")); ?>


    <?php endif; ?>

<?php $__env->stopSection(true); ?>

<?php echo $__env->make('dashboard.partials.section', [
    'id' => 'section-active-sites'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/dashboard/section-active-sites.blade.php ENDPATH**/ ?>