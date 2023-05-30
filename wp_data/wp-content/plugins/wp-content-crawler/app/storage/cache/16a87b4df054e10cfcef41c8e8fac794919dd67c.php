<?php

$transFullUrl = _wpcc('Make sure you entered full URLs. In other words, they should start with "http" or "https".');

?>



<?php $__env->startSection('title'); ?>
    <?php echo e(_wpcc('Manually crawl posts by entering their URLs')); ?>

<?php $__env->stopSection(true); ?>

<?php $__env->startSection('content'); ?>
    <form action="" class="tool-form tool-manual-crawl">
        <?php echo $__env->make('partials.form-nonce-and-action', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <input type="hidden" name="tool_type" value="save_post">

        <div class="panel-wrap">

            <table class="wcc-settings">
                
                <?php echo $__env->make('form-items.combined.select-with-label', [
                    'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_SITE_ID,
                    'title'     =>  _wpcc('Site'),
                    'info'      =>  _wpcc('Select the site for the post you want to save.'),
                    'options'   =>  $sites,
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                
                <?php echo $__env->make('form-items.combined.category-select-with-label', [
                    'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_CATEGORY_ID,
                    'title'         =>  _wpcc('Category'),
                    'info'          =>  _wpcc('Select the category in which you want the post saved.'),
                    'categories'    =>  $categories,
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                
                <?php echo $__env->make('form-items.combined.textarea-with-label', [
                    'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_POST_URLS,
                    'title'         =>  _wpcc('Post URLs'),
                    'info'          =>  _wpcc('Enter post URLs. You can add multiple post URLs by writing each of them
                                in a new line.') . ' ' . $transFullUrl,
                    'placeholder'   => _wpcc('New line separated post URLs...'),
                    'addKeys'       => true,
                    'showButtons'   => false,
                    'rows'          => 8
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                
                <?php echo $__env->make('form-items.combined.multiple-post-and-image-url-with-label', [
                    'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_POST_AND_FEATURED_IMAGE_URLS,
                    'title' =>  _wpcc('Post and Featured Image URLs'),
                    'info'  =>  _wpcc('Enter post URLs and, if exist, their featured image URLs.') . ' ' . $transFullUrl,
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                
                <?php echo $__env->make('form-items.combined.multiple-text-with-label', [
                    'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_CATEGORY_URLS,
                    'title'         =>  _wpcc('Retrieve post URLs from these category URLs'),
                    'info'          =>  _wpcc('Enter category URLs from which the post URLs should be retrieved.'),
                    'placeholder'   => _wpcc('Category URL...'),
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                
                <?php echo $__env->make('form-items.combined.input-with-label', [
                    'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_MAX_POSTS_TO_BE_CRAWLED,
                    'title'         =>  _wpcc('Pause after crawling this number of posts'),
                    'info'          =>  _wpcc('How many posts at max you want to be crawled. The crawling will be paused
                            when the number of URLs that have been crawled reaches this number. This option is
                            valid only when crawling now. Entering 0 or leaving this empty means unlimited posts.'),
                    'placeholder'   => _wpcc('Number of posts to be crawled before pausing...'),
                    'type'          => 'number',
                    'min'           => 0
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                
                <?php echo $__env->make('form-items.combined.input-with-label', [
                    'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_MAX_PARALLEL_CRAWLING_COUNT,
                    'title'         =>  _wpcc('Maximum parallel crawling count'),
                    'info'          =>  sprintf(_wpcc('How many posts can be crawled at the same time? Increasing this number
                        will put additional load onto your server. Default: %1$s'), 1),
                    'placeholder'   => _wpcc('Max parallel crawling count...'),
                    'type'          => 'number',
                    'min'           => 1
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                
                <?php echo $__env->make('form-items.combined.checkbox-with-label', [
                    'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_MANUAL_CRAWLING_TOOL_CLEAR_AFTER_SUBMIT,
                    'title' =>  _wpcc('Clear entered URLs after I click submit button'),
                    'info'  =>  _wpcc('When you check this, the URLs you have entered into the inputs will be cleared after you
                            click one of the submit buttons.'),
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            </table>

            
            <div class="button-container">
                
                <?php echo $__env->make('form-items/submit-button', [
                    'text'  =>  _wpcc('Crawl now'),
                    'class' => 'crawl-now',
                    'title' => _wpcc('The URLs you entered will be crawled one by one, as soon as you click this. Your browser needs to stay open until all URLs are finished being crawled.'),
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                
                <?php echo $__env->make('form-items/submit-button', [
                    'text'  =>  _wpcc('Add to the database'),
                    'class' => 'add-to-database',
                    'title' => _wpcc('The URLs you entered will be added to the database. They will be crawled using your scheduling settings.'),
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        </div>
    </form>
<?php $__env->stopSection(true); ?>

<?php echo $__env->make('tools.base.tool-container', [
    'id' => 'tool-manual-crawl'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/tools/tools/tool-manually-crawl.blade.php ENDPATH**/ ?>