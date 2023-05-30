<?php

$videoUrl           = 'https://www.youtube.com/watch?v=VHZIQcctixY';
$suffixClientSecret = 'client_secret';

?>



<?php $__env->startSection('api-options'); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey($suffixClientSecret, $clientClass),
        'title' =>  _wpcc('Client Secret'),
        'info'  =>  _wpcc('Client secret retrieved from Microsoft Azure Portal.') . ' ' . _wpcc_trans_how_to_get_it($videoUrl),
        'class' => $clientKey
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(true); ?>
<?php echo $__env->make('general-settings.translation.translation-api-settings-base', [
    'apiOptionKeySuffixes' => [$suffixClientSecret],
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/general-settings/translation/settings-microsoft_translator_text.blade.php ENDPATH**/ ?>