<?php

$registrationUrl = "http://www.turkcespin.com/";
$apiDocsUrl      = "http://www.turkcespin.com/api";

$transVisitApiPage = sprintf(_wpcc('For more information, visit %1$s page.'), '<a href="' . $apiDocsUrl . '" target="_blank">' . _wpcc('API documentation') . '</a>');

$suffixApiToken = 'api_token';

?>



<?php $__env->startSection('api-options'); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'          => $service->getOptionKey($suffixApiToken, $clientClass),
        'title'         => _wpcc('API Token'),
        'info'          => _wpcc('API token retrieved from Türkçe Spin.') . ' ' . $transVisitApiPage,
        'placeholder'   => _wpcc('API token...'),
        'class'         => $clientKey
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(true); ?>
<?php echo $__env->make('general-settings.spinning.spinning-api-settings-base', [
    'apiOptionKeySuffixes'  => [$suffixApiToken],
    'registrationLink'      => $registrationUrl,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/general-settings/spinning/settings-turkce_spin.blade.php ENDPATH**/ ?>