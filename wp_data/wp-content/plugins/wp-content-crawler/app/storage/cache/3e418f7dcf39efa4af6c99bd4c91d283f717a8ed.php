<?php if(isset($buttons)): ?>
    <div class="input-group">
        <div class="input-container input-button-container short-code-container">
            <?php /** @var \WPCCrawler\Objects\ShortCodeButton $button */ ?>
            <?php $__currentLoopData = $buttons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button class="button wpcc-button" type="button"
                        data-shortcode-name="<?php echo e($button->getCode()); ?>"
                        data-clipboard-text="<?php echo e($button->getCodeWithBrackets()); ?>"
                        data-wpcc-toggle="wpcc-tooltip"
                        data-placement="<?php echo e(isset($tooltipPos) && $tooltipPos ? $tooltipPos : 'top'); ?>"
                        title="<?php echo e($button->getDescription()); ?>"
                ><?php echo e($button->getCodeWithBrackets()); ?></button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if(!isset($noCustomShortCodes) || !$noCustomShortCodes): ?>
                <div class="custom-short-code-container"></div>
            <?php endif; ?>

        </div>
    </div>
<?php endif; ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/partials/short-code-buttons.blade.php ENDPATH**/ ?>