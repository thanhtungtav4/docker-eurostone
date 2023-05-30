<div class="wrap">
    <h1><?php echo e(_wpcc('Site Tester')); ?></h1>

    <div class="content">
        <form action="" id="tester-form" type="post">

            
            <?php echo $__env->make('partials.form-nonce-and-action', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            
            <div class="input-group site">
                <label for="site_id"><?php echo e(_wpcc('Site')); ?> </label>
                <select name="site_id" id="site_id">
                    <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($site->ID); ?>"
                            <?php if($categoryUrls && isset($categoryUrls[$site->ID])): ?>
                                data-category-urls='<?php echo json_encode($categoryUrls[$site->ID]); ?>'
                            <?php endif; ?>
                        ><?php echo e($site->post_title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div class="input-group test-type">
                <label for="test_type"><?php echo e(_wpcc('Test Type')); ?></label>
                <select name="test_type" id="test_type">
                    <?php $__currentLoopData = \WPCCrawler\Factory::testController()->getGeneralTestTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testName => $testType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($testType); ?>"><?php echo e($testName); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div class="input-group url" id="url-input-group">
                <label for="test_url_part"><?php echo e(_wpcc('Test URL')); ?></label>
                <input type="text" name="test_url_part" id="test_url_part" placeholder="<?php echo e(_wpcc('Full URL or URL without domain...')); ?>">
            </div>

            
            <div class="input-group submit">
                <button class="button wpcc-button" type="submit"><?php echo e(_wpcc('Test')); ?></button>
            </div>
        </form>

        <?php echo $__env->make('site-tester.test-history', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div id="test-results" class="hidden">

        </div>
    </div>
</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/site-tester/main.blade.php ENDPATH**/ ?>