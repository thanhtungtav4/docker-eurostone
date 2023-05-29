<?php
    $keyDevToolsState       = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_STATE;
    $keyUrl                 = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_URL;
    $keyTestButtonBehavior  = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_TEST_BUTTON_BEHAVIOR;
    $keyTargetHtmlTag       = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_TARGET_HTML_TAG;
    $keySelectionBehavior   = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_SELECTION_BEHAVIOR;
    $keyApplyManipulations  = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_APPLY_MANIPULATION_OPTIONS;
    $keyUseImmediately      = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_USE_IMMEDIATELY;
    $keyRemoveScripts       = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_REMOVE_SCRIPTS;
    $keyRemoveStyles        = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_REMOVE_STYLES;
?>

<input type="hidden" name="<?php echo e($keyDevToolsState); ?>" value='<?php echo isset($settings[$keyDevToolsState]) ? $settings[$keyDevToolsState][0] : ''; ?>'>
<div class="dev-tools-content-container" data-wcc="<?php echo e(isset($data) && $data ? json_encode($data) : json_encode([])); ?>">
    
    <div class="dev-tools-content" tabindex="-1">
        
        <div class="lightbox-title">Hi</div>

        
        <div class="toolbar">
            
            <div class="address-bar">
                <div class="button-container">
                    
                    <span class="dashicons dashicons-arrow-left-alt button-option back disabled"
                          title="<?php echo e(_wpcc("Click to go back")); ?>"></span>

                    
                    <span class="dashicons dashicons-arrow-right-alt button-option forward disabled"
                          title="<?php echo e(_wpcc("Click to go forward")); ?>"></span>

                    
                    <span class="dashicons dashicons-update button-option refresh disabled"
                          title="<?php echo e(_wpcc("Click to refresh")); ?>"></span>
                </div>

                <?php echo $__env->make('form-items.text', [
                    'name' => $keyUrl,
                    'class' => 'toolbar-input-container url-input',
                    'placeholder' => _wpcc('URL starting with http...'),
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <div class="button-container">
                    
                    <span class="dashicons dashicons-admin-collapse button-option go"
                          title="<?php echo e(_wpcc("Click to go to the URL")); ?>"></span>

                    
                    <span class="dashicons dashicons-menu button-option sidebar-open"
                          title="<?php echo e(_wpcc("Click to open the sidebar")); ?>"></span>
                </div>
            </div>

            
            <div class="css-selector-tools">
                <div class="button-container">
                    
                    <?php echo $__env->make('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-use',
                        'iconClass' => 'dashicons dashicons-yes',
                        'title' => _wpcc('Use the selector'),
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                <?php echo $__env->make('form-items.text', [
                    'name' => \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_CSS_SELECTOR,
                    'class' => 'toolbar-input-container css-selector-input',
                    'placeholder' => _wpcc('CSS selector...'),
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <div class="button-container">
                    
                    <?php echo $__env->make('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-test',
                        'iconClass' => 'dashicons dashicons-search',
                        'title' => _wpcc('Test the selector'),
                        'data' => [
                            'urlSelector' => sprintf('#%s', $keyUrl),
                            'testType' => \WPCCrawler\Test\Test::$TEST_TYPE_HTML,
                            'url' => 0
                        ]
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    
                    <?php echo $__env->make('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-clear-highlights',
                        'iconClass' => 'dashicons dashicons-editor-removeformatting',
                        'title' => _wpcc('Clear the highlights'),
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    
                    <?php echo $__env->make('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-show-alternatives',
                        'iconClass' => 'dashicons dashicons-image-rotate-right',
                        'title' => _wpcc('Show alternative selectors'),
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    
                    <?php echo $__env->make('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-remove-elements',
                        'iconClass' => 'dashicons dashicons-trash',
                        'title' => _wpcc('Remove elements matching this CSS selector from current page'),
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>

            
            <div class="options">
                
                <div class="left">
                    
                    <button class="button wpcc-button button-small button-option toggle-hover-select active" title="<?php echo e(_wpcc("Toggle hover select")); ?>">
                        <span class="dashicons dashicons-external"></span>
                    </button>

                    
                    <?php echo $__env->make('form-items.dev-tools.partial.vertical-separator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    
                    <label for="<?php echo e($keyTargetHtmlTag); ?>">
                        <input type="text" name="<?php echo e($keyTargetHtmlTag); ?>" class="target-html-tag" placeholder="<?php echo e(_wpcc("Target tag...")); ?>"
                            title="<?php echo e(sprintf(_wpcc('Enter an HTML element tag name to restrict the selection with only elements having this tag name. E.g. %1$s'), 'img')); ?>">
                    </label>

                    
                    <?php echo $__env->make('form-items.dev-tools.partial.vertical-separator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    
                    <select name="<?php echo e($keySelectionBehavior); ?>" title="<?php echo e(_wpcc("Select the behavior of CSS selector finder")); ?>">
                        <option value="unique"><?php echo e(_wpcc("Unique")); ?></option>
                        <option value="similar"><?php echo e(_wpcc("Similar")); ?></option>
                        <option value="similar_specific"><?php echo e(_wpcc("Similar (Specific)")); ?></option>
                        <option value="contains"><?php echo e(_wpcc("Contains")); ?></option>
                    </select>

                    
                    <div class="selected-elements"></div>

                </div>

                
                <div class="right">
                    
                    <label for="<?php echo e($keyTestButtonBehavior); ?>">
                        <?php echo e(_wpcc("Test via")); ?>

                        <select name="<?php echo e($keyTestButtonBehavior); ?>" id="<?php echo e($keyTestButtonBehavior); ?>" class="test-button-behavior">
                            <option value="php"><?php echo e(_wpcc("PHP")); ?></option>
                            <option value="js"><?php echo e(_wpcc("JavaScript")); ?></option>
                            <option value="both" selected="selected"><?php echo e(_wpcc("Both")); ?></option>
                        </select>
                    </label>

                    
                    <?php echo $__env->make('form-items.dev-tools.partial.vertical-separator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    
                    <label title="<?php echo e(_wpcc('When checked, manipulation options defined in the settings will be applied to the source code before showing the source code')); ?>"
                            for="<?php echo e($keyApplyManipulations); ?>">
                        <input type="checkbox"
                                id="<?php echo e($keyApplyManipulations); ?>"
                                class="apply-manipulation-options"
                                name="<?php echo e($keyApplyManipulations); ?>"
                                tabindex="-1"
                                checked="checked"> <?php echo e(_wpcc("Manipulations")); ?>

                    </label>

                    
                    <?php echo $__env->make('form-items.dev-tools.partial.vertical-separator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    
                    <label for="<?php echo e($keyUseImmediately); ?>" title="<?php echo e(_wpcc("Use the selector immediately when clicked")); ?>">
                        <input type="checkbox" id="<?php echo e($keyUseImmediately); ?>" class="use-immediately" name="<?php echo e($keyUseImmediately); ?>" tabindex="-1"> <?php echo e(_wpcc("Use immediately")); ?>

                    </label>

                    
                    <?php echo $__env->make('form-items.dev-tools.partial.vertical-separator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    
                    <label for="<?php echo e($keyRemoveScripts); ?>">
                        <input type="checkbox" id="<?php echo e($keyRemoveScripts); ?>" class="remove-scripts" name="<?php echo e($keyRemoveScripts); ?>" tabindex="-1" checked="checked"> <?php echo e(_wpcc("Remove scripts")); ?>

                    </label>

                    
                    <?php echo $__env->make('form-items.dev-tools.partial.vertical-separator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    
                    <label for="<?php echo e($keyRemoveStyles); ?>">
                        <input type="checkbox" id="<?php echo e($keyRemoveStyles); ?>" class="remove-styles" name="<?php echo e($keyRemoveStyles); ?>" tabindex="-1"> <?php echo e(_wpcc("Remove styles")); ?>

                    </label>
                </div>
            </div>

            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        
        <iframe frameborder="0" class="source"></iframe>

        
        <div class="sidebar">
            
            <span class="dashicons dashicons-no-alt sidebar-close"></span>

            <?php echo $__env->make('form-items.dev-tools.sidebar-section', [
                'title' => _wpcc('History'),
                'class' => 'history',
                'buttons' => [
                    'dashicons dashicons-trash clear-history'
                ]
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('form-items.dev-tools.sidebar-section', [
                'title' => _wpcc('Alternative Selectors'),
                'class' => 'alternative-selectors'
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('form-items.dev-tools.sidebar-section', [
                'title' => _wpcc('All Used Selectors'),
                'class' => 'used-selectors'
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        </div>

        
        <div class="iframe-status hidden"></div>
    </div>
</div>


<style id="iframe-style"><?php echo \WPCCrawler\Factory::assetManager()->getDevToolsIframeStyle(); ?></style>
<?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/dev-tools/dev-tools-content-container.blade.php ENDPATH**/ ?>