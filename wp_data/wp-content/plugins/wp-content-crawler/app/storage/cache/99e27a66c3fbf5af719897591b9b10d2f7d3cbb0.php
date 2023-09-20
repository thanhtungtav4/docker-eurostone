<?php

$videoUrl        = 'https://www.youtube.com/watch?v=imQd2pGj7-o';
$suffixApiKey    = 'api_key';
$suffixProjectId = 'project_id';

?>



<?php $__env->startSection('api-options'); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey($suffixProjectId, $clientClass),
        'title' => _wpcc('Project ID'),
        'info'  => _wpcc('Project ID retrieved from Google Cloud Console.') . ' ' . _wpcc_trans_how_to_get_it($videoUrl),
        'class' => $clientKey
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey($suffixApiKey, $clientClass),
        'title' =>  _wpcc('API Key'),
        'info'  =>  _wpcc('API key retrieved from Google Cloud Console.') . ' ' . _wpcc_trans_how_to_get_it($videoUrl),
        'class' => $clientKey
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
<?php $__env->stopSection(true); ?>
<?php echo $__env->make('general-settings.translation.translation-api-settings-base', [
    'apiOptionKeySuffixes' => [$suffixApiKey, $suffixProjectId],
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/general-settings/translation/settings-google_translate.blade.php ENDPATH**/ ?>