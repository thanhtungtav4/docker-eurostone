

<?php
    $isRecrawl = isset($type) && $type && $type != 'crawl';
    $now = strtotime(current_time('mysql'));
?>


<table class="section-table <?php echo e(isset($tableClass) && $tableClass ? $tableClass : ''); ?>" <?php if(isset($id) && $id): ?> id="<?php echo e($id); ?>" <?php endif; ?>>

    
    <thead>
        <tr>
            <th><?php echo e(_wpcc("Post")); ?></th>
            <th><?php echo e($isRecrawl ? _wpcc("Recrawled") : _wpcc("Saved")); ?></th>
            <?php if($isRecrawl): ?>
                <th class="col-update-count"><?php echo e(_wpcc("Update Count")); ?></th>
            <?php endif; ?>
        </tr>
    </thead>

    
    <tbody>
        <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dashboardPost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!($dashboardPost instanceof \WPCCrawler\Objects\Dashboard\DashboardPost) || !$dashboardPost->getUrlTuple()->getSite()): ?>
                <?php continue; ?>
            <?php endif; ?>

            <?php
                /** @var \WPCCrawler\Objects\Dashboard\DashboardPost $dashboardPost */
                $post = $dashboardPost->getPost();
                $urlTuple = $dashboardPost->getUrlTuple();
                $site = $urlTuple->getSite();
            ?>
            <tr>
                
                <td class="col-post">
                    
                    <div class="post-title">
                        <a href="<?php echo get_permalink($post->ID); ?>" target="_blank">
                            <?php echo e($post->post_title); ?>

                        </a>

                        
                        <span class="edit-link">
                            - <a href="<?php echo get_edit_post_link($post->ID); ?>" target="_blank">
                                <?php echo e(_wpcc("Edit")); ?>

                            </a>
                        </span>
                    </div>

                    
                    <div class="post-details">
                        
                        <?php echo $__env->make('dashboard.partials.site-link', ['site' => $site], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                        
                        <span class="post-type">
                            (<?php echo e($post->post_type); ?>)
                        </span>

                        
                        <span class="id">
                            <?php echo e(_wpcc("ID")); ?>: <?php echo e($post->ID); ?>

                        </span> -

                        
                        <span class="target-url">
                            <a href="<?php echo $urlTuple->getUrl(); ?>" target="_blank">
                                <?php echo mb_strlen($urlTuple->getUrl()) > 255 ? mb_substr($urlTuple->getUrl(), 0, 255) . "..." : $urlTuple->getUrl(); ?>

                            </a>
                        </span>
                    </div>

                </td>

                
                <td class="col-date">
                    
                    <span class="diff-for-humans">
                        <?php
                            $date = $isRecrawl ? $urlTuple->getRecrawledAt() : $urlTuple->getSavedAt();
                            $timestamp = $date ? $date->getTimestamp() : -1;
                        ?>
                        <?php if($timestamp === -1): ?>
                            <?php echo e('-'); ?>

                        <?php else: ?>
                            <?php echo e(\WPCCrawler\Utils::getDiffForHumans($timestamp)); ?>

                            <?php echo e($timestamp > $now ? _wpcc("later") : _wpcc("ago")); ?>

                        <?php endif; ?>
                    </span>

                    <span class="date">
                        (<?php echo e($timestamp === -1 ? '-' : \WPCCrawler\Utils::getDateFormatted($timestamp)); ?>)
                    </span>
                </td>

                
                <?php if($isRecrawl): ?>
                    <td class="col-update-count">
                        <?php echo e($urlTuple->getUpdateCount()); ?>

                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>

</table>
<?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/dashboard/partials/table-posts.blade.php ENDPATH**/ ?>