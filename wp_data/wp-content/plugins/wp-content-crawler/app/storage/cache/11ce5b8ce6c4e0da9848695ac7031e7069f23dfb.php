<?php $__env->startSection('title'); ?>
    <?php echo e(_wpcc('Manually recrawl (update) a post')); ?>

<?php $__env->stopSection(true); ?>

<?php $__env->startSection('content'); ?>
    <form action="" class="tool-form">
        

        <?php echo $__env->make('partials.form-nonce-and-action', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <input type="hidden" name="tool_type" value="recrawl_post">

        <div class="panel-wrap">

            <table class="wcc-settings">
                
                <?php echo $__env->make('form-items.combined.input-with-label', [
                    'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_RECRAWL_POST_ID,
                    'title'         =>  _wpcc('Post ID'),
                    'info'          =>  _wpcc('Write the ID of the post you want to update.'),
                    'type'          =>  'number',
                    'min'           =>  0,
                    'placeholder'   => _wpcc('Post ID...')
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            </table>

            <?php echo $__env->make('form-items/submit-button', [
                'text'  =>  _wpcc('Recrawl'),
                'class' => 'recrawl'
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </form>
<?php $__env->stopSection(true); ?>

<?php echo $__env->make('tools.base.tool-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/tools/tools/tool-manually-recrawl.blade.php ENDPATH**/ ?>