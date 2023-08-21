<?php $__env->startSection('content-class'); ?> whats-happening <?php $__env->stopSection(true); ?>
<?php $__env->startSection('header'); ?> <?php $__env->stopSection(true); ?>

<?php $__env->startSection('title'); ?>
    <?php echo e(_wpcc("What's happening")); ?>

<?php $__env->stopSection(true); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $now = strtotime(current_time('mysql'));
        $classUrlCollection = 'url-collection';
        $classPostCrawl     = 'post-crawl';
        $classPostRecrawl   = 'post-recrawl';
        $classPostDelete    = 'post-delete';

        $classCountQueue        = 'queue';
        $classCountSavedPosts   = 'saved-posts';
        $classCountUpdatedPosts = 'updated-posts';
        $classCountDeletedPosts = 'deleted-posts';
    ?>

    
    <h3 class="cron-events"><?php echo e(_wpcc("CRON Events")); ?> <span class="now">(<?php echo e(sprintf(_wpcc('Now: %1$s'), \WPCCrawler\Utils::getDateFormatted(current_time('mysql')))); ?>)</span></h3>
    <table class="detail-card orange" id="cron-events">
        <thead>
            <tr>
                <?php
                    $tableHeadValues = [
                        _wpcc("URL Collection") => [$classUrlCollection,    \WPCCrawler\Factory::schedulingService()->eventCollectUrls],
                        _wpcc("Post Crawl")     => [$classPostCrawl,        \WPCCrawler\Factory::schedulingService()->eventCrawlPost],
                        _wpcc("Post Recrawl")   => [$classPostRecrawl,      \WPCCrawler\Factory::schedulingService()->eventRecrawlPost],
                        _wpcc("Post Delete")    => [$classPostDelete,       \WPCCrawler\Factory::schedulingService()->eventDeletePosts],
                    ];
                ?>
                <th></th>
                <?php $__currentLoopData = $tableHeadValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        /** @var string[] $value */
                        $eventClass = $value[0];
                        $eventKey   = $value[1];
                    ?>
                    <th class="<?php echo e($eventClass); ?>">
                        <?php echo e($name); ?>

                        <div class="interval-description"><?php echo e($dashboard->getCronEventIntervalDescription($eventKey)); ?></div>
                    </th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        </thead>
        <tbody>
            <tr class="events-next">
                <?php
                    /** @var \WPCCrawler\Objects\Dashboard\Dashboard $dashboard */
                    $nextEventDates = [
                        [$classUrlCollection,   $dashboard->getNextUrlCollectionDate(),    $dashboard->getNextUrlCollectionSite()],
                        [$classPostCrawl,       $dashboard->getNextPostCrawlDate(),        $dashboard->getNextPostCrawlSite()],
                        [$classPostRecrawl,     $dashboard->getNextPostRecrawlDate(),      $dashboard->getNextPostRecrawlSite()],
                        [$classPostDelete,      $dashboard->getNextPostDeleteDate(),       $dashboard->getNextPostDeleteSite()],
                    ];
                ?>
                <td><?php echo e(_wpcc("Next")); ?></td>

                <?php $__currentLoopData = $nextEventDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php /** @var array $value */ ?>
                    <?php
                        $eventClass = $value[0];
                        $date       = $value[1];
                        $timestamp  = strtotime($date ?? '');
                        $site       = $value[2];
                    ?>
                    <td class="<?php echo e($eventClass); ?>">
                        <div class="diff-for-humans">
                            <?php echo e(\WPCCrawler\Utils::getDiffForHumans(strtotime($date ?? ''))); ?>

                            <?php echo e($timestamp > $now ? _wpcc("later") : _wpcc("ago")); ?>

                        </div>
                        <span class="date">(<?php echo e(\WPCCrawler\Utils::getDateFormatted($date)); ?>)</span>
                        <?php if($site): ?>
                            <div class="next-site">
                                <?php echo $__env->make('dashboard.partials.site-link', ['site' => $site], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
            <tr class="events-last">
                <td><?php echo e(_wpcc("Last")); ?></td>
                <?php
                    $lastEventDates = [
                        [$classUrlCollection,   $dashboard->getLastUrlCollectionDate()],
                        [$classPostCrawl,       $dashboard->getLastPostCrawlDate()],
                        [$classPostRecrawl,     $dashboard->getLastPostRecrawlDate()],
                        [$classPostDelete,      $dashboard->getLastPostDeleteDate()],
                    ];
                ?>
                <?php $__currentLoopData = $lastEventDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        /** @var string[] $value */
                        $eventClass = $value[0];
                        $eventDate  = $value[1];
                    ?>
                    <td class="<?php echo e($eventClass); ?>">
                        <div class="diff-for-humans">
                            <?php echo sprintf(_wpcc("%s ago"), \WPCCrawler\Utils::getDiffForHumans(strtotime($eventDate ?? ''))); ?>

                        </div>
                        <span class="date">(<?php echo e(\WPCCrawler\Utils::getDateFormatted($eventDate)); ?>)</span>
                    </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        </tbody>
    </table>

    
    <h3><?php echo e(_wpcc("Counts")); ?></h3>
    <table class="detail-card counts teal" id="counts">
        <thead>
            <tr>
                <th></th>
                <th class="<?php echo e($classCountQueue); ?>"><?php echo e(_wpcc("URLs in Queue")); ?></th>
                <th class="<?php echo e($classCountSavedPosts); ?>"><?php echo e(_wpcc("Saved Posts")); ?></th>
                <th class="<?php echo e($classCountUpdatedPosts); ?>"><?php echo e(_wpcc("Updated Posts")); ?></th>
                <th class="<?php echo e($classCountDeletedPosts); ?>"><?php echo e(_wpcc("Deleted Posts")); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="counts-today">
                <td><?php echo e(_wpcc("Today")); ?></td>
                <td class="<?php echo e($classCountQueue); ?>"><?php echo e($dashboard->getTotalUrlsInQueueAddedToday()); ?></td>
                <td class="<?php echo e($classCountSavedPosts); ?>"><?php echo e($dashboard->getTotalSavedPostsToday()); ?></td>
                <td class="<?php echo e($classCountUpdatedPosts); ?>"><?php echo e($dashboard->getTotalRecrawledPostsToday()); ?></td>
                <td class="<?php echo e($classCountDeletedPosts); ?>"><?php echo e($dashboard->getTotalDeletedPostsToday()); ?></td>
            </tr>
            <tr class="counts-all">
                <td><?php echo e(_wpcc("All")); ?></td>
                <td class="<?php echo e($classCountQueue); ?>"><?php echo e($dashboard->getTotalUrlsInQueue()); ?></td>
                <td class="<?php echo e($classCountSavedPosts); ?>"><?php echo e($dashboard->getTotalSavedPosts()); ?></td>
                <td class="<?php echo e($classCountUpdatedPosts); ?>"><?php echo e($dashboard->getTotalRecrawledPosts()); ?></td>
                <td class="<?php echo e($classCountDeletedPosts); ?>"><?php echo e($dashboard->getTotalDeletedPosts()); ?></td>
            </tr>
        </tbody>
    </table>

    
    <?php if($dashboard->getUrlsCurrentlyBeingCrawled()): ?>
        <h3><?php echo e(_wpcc("URLs being crawled right now")); ?></h3>
        <?php echo $__env->make('dashboard.partials.table-urls', [
            'id'                => 'urls-being-crawled',
            'urls'              => $dashboard->getUrlsCurrentlyBeingCrawled(),
            'tableClass'        => 'detail-card green currently-being-crawled',
            'dateColumnName'    => _wpcc('Created'),
            'fieldName'         => 'getCreatedAt',
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php endif; ?>

    
    <?php if($dashboard->getPostsCurrentlyBeingSaved()): ?>
        <h3><?php echo e(_wpcc("Posts being saved right now")); ?></h3>
        <?php echo $__env->make('dashboard.partials.table-posts', [
            'id'            => 'posts-being-saved',
            'posts'         => $dashboard->getPostsCurrentlyBeingSaved(),
            'tableClass'    => 'detail-card green currently-being-saved'
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

<?php $__env->stopSection(true); ?>

<?php echo $__env->make('dashboard.partials.section', [
    'id' => 'section-whats-happening'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/dashboard/section-whats-happening.blade.php ENDPATH**/ ?>