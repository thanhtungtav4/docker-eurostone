<?php $__env->startSection('title'); ?>
    <?php echo e(_wpcc('Unlock all URLs')); ?>

<?php $__env->stopSection(true); ?>

<?php $__env->startSection('content'); ?>
    <form action="" class="tool-form">
        <?php echo $__env->make('partials.form-nonce-and-action', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <input type="hidden" name="tool_type" value="unlock_all_urls">

        <div class="panel-wrap">

            
            <div>
                <p>
                    <?php echo e(_wpcc('The plugin locks the URLs which it is processing. This is to avoid processing
                        already-being-processed URLs. But sometimes things go wrong either with your server or the target server
                        and some URLs stay locked. You can see these URLs shown as "currently being crawled/recrawled" in
                        dashboard. If you see some URLs are stuck there and you do not want to see them, you can use this tool
                        to unlock all URLs.')); ?>

                </p>
            </div>

            <?php echo $__env->make('form-items/submit-button', [
                'text'  =>  _wpcc('Unlock All URLs')
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </form>
<?php $__env->stopSection(true); ?>

<?php echo $__env->make('tools.base.tool-container', [
    'id'                => 'tool-unlock-urls',
    'noToggleButton'    => true,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/tools/tools/tool-unlock-urls.blade.php ENDPATH**/ ?>