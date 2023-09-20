<?php
    /**
     * For the tests in the options box, we will send the test data by getting it from the test data presenter. So,
     * the value of the 'extra' key for the data sent via AJAX must contain the selector and the target data attribute
     * for the test data presenter. Instead of hard-coding this for every testable form item in the options box, we
     * define it here once and use it for the form items. This value will be available for every view included in this
     * view. If more items need to be added to $dataExtra, then it should be merged with $dataExtra to make sure the
     * test data is always included in the AJAX request.
     *
     * @var array $dataExtra
     */
    $dataExtra = [
        'test'   => [
            'selector'  => '#test-data-presenter',
            'data'      => 'results'
        ]
    ];
?>

<div class="options-box-container hidden" id="options-box-container">

    <div class="options-box">
        
        <div class="box-title"></div>

        
        <div class="input-details"></div>

        
        <div class="box-container">

            
            <h2 class="nav-tab-wrapper">
                
                <a href="#" data-tab="#tab-options-box-find-replace"        class="nav-tab nav-tab-active"><?php echo e(_wpcc('Find-Replace')); ?></a>
                <a href="#" data-tab="#tab-options-box-general"             class="nav-tab"><?php echo e(_wpcc('General')); ?></a>
                <a href="#" data-tab="#tab-options-box-calculations"        class="nav-tab"><?php echo e(_wpcc('Calculations')); ?></a>
                <a href="#" data-tab="#tab-options-box-templates"           class="nav-tab"><?php echo e(_wpcc('Templates')); ?></a>

                
                <a href="#" data-tab="#tab-options-box-file-find-replace"   class="nav-tab"><?php echo e(_wpcc('Find-Replace')); ?></a>
                <a href="#" data-tab="#tab-options-box-file-operations"     class="nav-tab"><?php echo e(_wpcc('File Operations')); ?></a>
                <a href="#" data-tab="#tab-options-box-file-templates"      class="nav-tab"><?php echo e(_wpcc('Templates')); ?></a>

                
                <a href="#" data-tab="#tab-options-box-notes"               class="nav-tab"><?php echo e(_wpcc('Notes')); ?></a>

                
                <a href="#" data-tab="#tab-options-box-import-export"       class="nav-tab">
                    <span class="dashicons dashicons-upload"></span>
                    <span class="dashicons dashicons-download"></span>
                </a>
            </h2>

            
            <div class="tab-content section">

                
                <div id="tab-options-box-find-replace" class="tab">
                    <?php echo $__env->make('form-items.options-box.tabs.tab-find-replace', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                
                <div id="tab-options-box-file-find-replace" class="tab hidden">
                    <?php echo $__env->make('form-items.options-box.tabs.file.tab-file-find-replace', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                
                <div id="tab-options-box-file-operations" class="tab hidden">
                    <?php echo $__env->make('form-items.options-box.tabs.file.tab-file-operations', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                
                <div id="tab-options-box-general" class="tab hidden">
                    <?php echo $__env->make('form-items.options-box.tabs.tab-general', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                
                <div id="tab-options-box-calculations" class="tab hidden">
                    <?php echo $__env->make('form-items.options-box.tabs.tab-calculations', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                
                <div id="tab-options-box-templates" class="tab hidden">
                    <?php echo $__env->make('form-items.options-box.tabs.tab-templates', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                
                <div id="tab-options-box-file-templates" class="tab hidden">
                    <?php echo $__env->make('form-items.options-box.tabs.file.tab-file-templates', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                
                <div id="tab-options-box-notes" class="tab hidden">
                    <?php echo $__env->make('form-items.options-box.tabs.tab-notes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                
                <div id="tab-options-box-import-export" class="tab hidden">
                    <?php echo $__env->make('form-items.options-box.tabs.tab-import-export', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

            </div>

            
            <div class="test-data-presenter" id="test-data-presenter">
                
                <div class="header">

                    
                    <div class="title">
                        <span><?php echo e(_wpcc('Current test data')); ?></span>
                        <span class="count">(<span class="number">0</span>)</span>
                        <a role="button" class="invalidate hidden"><?php echo e(_wpcc('Invalidate')); ?></a>
                    </div>

                </div>

                
                <div class="data hidden"></div>
            </div>

        </div>
    </div>

</div><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/options-box/options-box-container.blade.php ENDPATH**/ ?>