<?php if(isset($template)): ?>
    <div class="details">
        <h2>
            <span><?php echo e(_wpcc('Template')); ?></span>
            <?php if(isset($templateMessage) && $templateMessage): ?>
                <span class="small"><?php echo e($templateMessage); ?></span>
            <?php endif; ?>
            <button class="button wpcc-button" id="go-to-details"><?php echo e(_wpcc('Go to details')); ?></button>
        </h2>
        <div class="inside">
            <div class="template">
                <?php echo $template; ?>

            </div>

            
            <?php if(isset($template) && isset($showSourceCode) && $showSourceCode): ?>
                <div class="source-code-container">
                    <?php echo $__env->make('site-tester.partial.toggleable-textarea', [
                        'title'      => _wpcc('Source Code') . ' (' . _wpcc("Character count") . ': ' . mb_strlen($template) . ')',
                        'toggleText' => _wpcc('Toggle source code'),
                        'id'         => 'source-code',
                        'hidden'     => true,
                        'content'    => $template
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            <?php endif; ?>

            <div class="clear-fix"></div>
        </div>
        <div class="clear-fix"></div>
    </div>
<?php endif; ?>


<?php if(\WPCCrawler\Objects\Informing\Informer::getInfos()): ?>
    <div class="details information">
        <h2>
            <span><?php echo e(_wpcc('Information')); ?></span>
            <button class="button wpcc-button go-to-top"><?php echo e(_wpcc('Go to top')); ?></button>
        </h2>
        <div class="inside">
            <?php echo $__env->make('partials.info-list', ['noTitle' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
<?php endif; ?>


<?php if(isset($postDetailViews)): ?>
    <?php echo $postDetailViews; ?>

<?php endif; ?>


<div class="details" id="details">
    <h2>
        <span><?php echo e(_wpcc('Details')); ?></span>
        <button class="button wpcc-button go-to-top"><?php echo e(_wpcc('Go to top')); ?></button>
    </h2>
    <div class="inside">
        <?php echo $__env->make('site-tester.partial.detail-table', [
            'tableData' => $info
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <?php if(isset($data)): ?>
            <div class="data-container">
                <?php
                    /** @var array $data */
                    $str = (print_r($data, true));
                ?>
                <?php echo $__env->make('site-tester.partial.toggleable-textarea', [
                    'title'      => _wpcc('Data'),
                    'toggleText' => _wpcc('Toggle data'),
                    'id'         => 'post-data',
                    'hidden'     => true,
                    'content'    => $str
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        <?php endif; ?>

        <div class="clear-fix"></div>
        <div class="go-to-top-container">
            <button class="button wpcc-button go-to-top"><?php echo e(_wpcc('Go to top')); ?></button>
        </div>

    </div>
</div><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-tester/test-results.blade.php ENDPATH**/ ?>