<?php /** @var string $name */ ?>
<div class="inputs">
    <?php echo $__env->make('form-items.partials.short-code-buttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="input-group">
        <div class="input-container">
            <?php wp_editor(isset($settings[$name]) ? $settings[$name][0] : '', $name, [
                "media_buttons"         =>  false,
                "editor_height"         =>  isset($height) ? $height : 320
            ]) ?>
        </div>
    </div>
</div><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/form-items/template-editor.blade.php ENDPATH**/ ?>