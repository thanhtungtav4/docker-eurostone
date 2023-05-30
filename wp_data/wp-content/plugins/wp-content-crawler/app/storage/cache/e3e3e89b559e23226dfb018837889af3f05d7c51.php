<?php $__env->startSection('title'); ?>
    <?php echo e(_wpcc("Clear URLs")); ?>

<?php $__env->stopSection(true); ?>

<?php $__env->startSection('content'); ?>
    <form action="" class="tool-form">
        

        <?php echo $__env->make('partials.form-nonce-and-action', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <input type="hidden" name="tool_type" value="delete_urls">

        <div class="panel-wrap">

            <table class="wcc-settings">
                
                <?php echo $__env->make('form-items.combined.select-with-label', [
                    'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_CLEAR_URLS_SITE_ID,
                    'title'     =>  _wpcc('Site'),
                    'info'      =>  _wpcc('Select the site whose URLs you want to be deleted from the database.'),
                    'options'   =>  $sites,
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                
                <?php echo $__env->make('form-items.combined.select-with-label', [
                    'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_URL_TYPE,
                    'title' =>  _wpcc('URL Type'),
                    'info'  =>  _wpcc('Select URL types to be cleared for the specified site. If you clear the URLs
                        waiting in the queue, those URLs will not be saved, unless they are collected again. If you
                        clear already-saved URLs, those URLs may end up in the queue again, and they may be saved
                        as posts again. So, you may want to delete the posts as well, unless you want duplicate content.'),
                    'options'   =>  $urlTypes,
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                
                <?php echo $__env->make('form-items.combined.checkbox-with-label', [
                    'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_SAFETY_CHECK,
                    'title' =>  _wpcc("I'm sure"),
                    'info'  =>  _wpcc('Check this to indicate that you are sure about this.'),
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </table>

            <?php echo $__env->make('form-items/submit-button', [
                'text'  =>  _wpcc('Delete URLs')
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        </div>
    </form>
<?php $__env->stopSection(true); ?>

<?php echo $__env->make('tools.base.tool-container', [
    'id' => 'tool-clear-urls'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/tools/tools/tool-clear-urls.blade.php ENDPATH**/ ?>