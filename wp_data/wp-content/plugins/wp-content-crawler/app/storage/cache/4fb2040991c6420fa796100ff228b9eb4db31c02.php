<div class="wrap container-tools wcc-settings-meta-box" id="container-tools">
    <h1><?php echo e(_wpcc('Tools')); ?></h1>

    
    <h2 class="nav-tab-wrapper">
        <a href="#" data-tab="#tab-manual-crawling"     class="nav-tab nav-tab-active"><?php echo e(_wpcc('Manual Crawling')); ?></a>
        <a href="#" data-tab="#tab-manual-recrawling"   class="nav-tab"><?php echo e(_wpcc('Manual Recrawling')); ?></a>
        <a href="#" data-tab="#tab-urls"                class="nav-tab"><?php echo e(_wpcc('URLs')); ?></a>
    </h2>

    
    <div id="tab-manual-crawling" class="tab">
        <?php echo $__env->make('tools/tabs/tab-manual-crawling', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php

        /**
         * Fires at the end of closing tag of the content area in Tools page
         *
         * @since 1.6.3
         */
        do_action('wpcc/view/tools');

        ?>
    </div>

    
    <div id="tab-manual-recrawling" class="tab hidden">
        <?php echo $__env->make('tools/tabs/tab-manual-recrawling', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    
    <div id="tab-urls" class="tab hidden">
        <?php echo $__env->make('tools/tabs/tab-urls', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/tools/main.blade.php ENDPATH**/ ?>