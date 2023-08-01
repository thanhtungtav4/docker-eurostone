


<table class="section-table <?php echo e(isset($tableClass) && $tableClass ? $tableClass : ''); ?>" <?php if(isset($id) && $id): ?> id="<?php echo $id; ?>" <?php endif; ?>>

    
    <thead>
        <tr>
            <th><?php echo e(_wpcc("URL")); ?></th>
            <th><?php echo e($dateColumnName); ?></th>
        </tr>
    </thead>

    
    <tbody>
        <?php $__currentLoopData = $urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php /** @var \WPCCrawler\Objects\Dashboard\DashboardUrlTuple $url */ ?>
            <tr>
                
                <td class="col-post">
                    
                    <div class="post-title">
                        <a href="<?php echo $url->getUrl(); ?>" target="_blank">
                            <?php echo mb_strlen($url->getUrl()) > 255 ? mb_substr($url->getUrl(), 0, 255) . "..." : $url->getUrl(); ?>

                        </a>
                    </div>

                    
                    <div class="post-details">
                        
                        <?php if($url->getSite()): ?>
                            <?php echo $__env->make('dashboard.partials.site-link', ['site' => $url->getSite()], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endif; ?>

                        
                        <span class="id">
                            <?php echo e(_wpcc("ID")); ?>: <?php echo e($url->getId()); ?>

                        </span>
                    </div>

                </td>

                
                <td class="col-date">
                    <?php
                    /** @var string $fieldName */
                    /** @var DateTime|null $date */
                    $date = method_exists($url, $fieldName) ? $url->$fieldName() : null;
                    $date = $date instanceof DateTime ? $date : null;
                    ?>
                    
                    <span class="diff-for-humans">
                        <?php echo e($date ? \WPCCrawler\Utils::getDiffForHumans($date->getTimestamp()) : "-"); ?>

                        <?php echo e(_wpcc("ago")); ?>

                    </span>

                    <span class="date">
                        (<?php echo e($date ? \WPCCrawler\Utils::getDateFormatted($date->getTimestamp()) : "-"); ?>)
                    </span>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>

</table>
<?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/dashboard/partials/table-urls.blade.php ENDPATH**/ ?>