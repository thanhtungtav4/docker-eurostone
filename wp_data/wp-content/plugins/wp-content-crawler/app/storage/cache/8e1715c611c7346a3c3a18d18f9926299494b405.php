<?php $__env->startSection('title'); ?>
    <?php echo e(_wpcc('URLs for Manual Crawling')); ?>

<?php $__env->stopSection(true); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="table-container hidden">

        
        <div class="control-buttons">
            
            <button class="button wpcc-button pause" type="button" title="<?php echo e(_wpcc('Pause crawling')); ?>">
                <span class="dashicons dashicons-controls-pause"></span>
                <?php echo e(_wpcc('Pause')); ?>

            </button>

            
            <button class="button wpcc-button continue hidden" type="button" title="<?php echo e(_wpcc('Continue crawling')); ?>">
                <span class="dashicons dashicons-controls-play"></span>
                <?php echo e(_wpcc('Continue')); ?>

            </button>
        </div>

        
        <div class="info">
            <span class="dashicons dashicons-warning"></span>
            <?php echo e(_wpcc('Do not close your browser while URLs are being crawled.')); ?>

        </div>

        
        <div id="status"></div>

        
        <div class="table-controls">
            <a role="button" class="show-all-responses"><?php echo e(_wpcc('Show all results')); ?></a>
            <a role="button" class="hide-all-responses"><?php echo e(_wpcc('Hide all results')); ?></a>
        </div>

        
        <table id="table-url-queue-manual-crawl">
            
            <thead>
            <tr>
                <th class="status"><?php echo e(_wpcc('Status')); ?></th>
                <th class="site"><?php echo e(_wpcc('Site')); ?></th>
                <th class="category"><?php echo e(_wpcc('Category')); ?></th>
                <th class="image"><?php echo e(_wpcc('Image')); ?></th>
                <th class="post-url"><?php echo e(_wpcc('Post URL')); ?></th>
                <th class="controls">
                    <a role="button" class="remove-all"><?php echo e(_wpcc('Remove all')); ?></a>
                </th>
            </tr>
            </thead>

            
            <tbody>

            
            <tr class="prototype url hidden">
                <td class="status">
                    <span class="dashicons dashicons-controls-pause"></span>
                </td>
                <td class="site"></td>
                <td class="category"></td>
                <td class="image"></td>
                <td class="post-url"></td>
                <td class="controls">
                    
                    <button class="button wpcc-button repeat" type="button" title="<?php echo e(_wpcc("Retry/recrawl")); ?>">
                        <span class="dashicons dashicons-controls-repeat"></span>
                    </button>

                    
                    <button class="button wpcc-button delete" type="button" title="<?php echo e(_wpcc("Delete")); ?>">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </td>
            </tr>

            
            <tr class="prototype response hidden">
                <td class="" colspan="6">
                    <div class="response"></div>
                </td>
            </tr>

            </tbody>
        </table>
    </div>

    
    <span class="default-message"><?php echo e(_wpcc("No URLs waiting to be saved.")); ?></span>
<?php $__env->stopSection(true); ?>

<?php echo $__env->make('tools.base.tool-container', [
    'id'                => 'container-url-queue-manual-crawl',
    'noToggleButton'    => true
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/tools/partials/component-urls.blade.php ENDPATH**/ ?>