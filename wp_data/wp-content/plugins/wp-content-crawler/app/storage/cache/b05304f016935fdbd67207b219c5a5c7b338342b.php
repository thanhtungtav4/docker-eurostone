<?php

$apiRetrievalUrl = 'https://tech.yandex.com/translate/';
$apiRetrievalAnchor = sprintf('<a href="%2$s" target="_blank">%1$s</a>', _wpcc('Click here to get your API key.'), $apiRetrievalUrl);
$suffixApiKey = 'api_key';

?>



<?php $__env->startSection('api-options'); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey($suffixApiKey, $clientClass),
        'title' => _wpcc('API Key'),
        'info'  => _wpcc('API key retrieved from Yandex Translate.') . ' ' . $apiRetrievalAnchor,
        'class' => $clientKey
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(true); ?>
<?php echo $__env->make('general-settings.translation.translation-api-settings-base', [
    'apiOptionKeySuffixes' => [$suffixApiKey],
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/general-settings/translation/settings-yandex_translate.blade.php ENDPATH**/ ?>