

<?php

$videoUrl     = 'https://www.youtube.com/watch?v=yRzzUjE0rzk';

$suffixAccessKey    = 'access_key';
$suffixSecret       = 'secret';
$suffixRegion       = 'region';

?>



<?php $__env->startSection('api-options'); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey($suffixAccessKey, $clientClass),
        'title' => _wpcc('Access Key'),
        'info'  => _wpcc('Access key retrieved from Amazon Translate.') . ' ' . _wpcc_trans_how_to_get_it($videoUrl),
        'class' => $clientKey
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey($suffixSecret, $clientClass),
        'title' => _wpcc('Secret'),
        'info'  => _wpcc('Secret retrieved from Amazon Translate.') . ' ' . _wpcc_trans_how_to_get_it($videoUrl),
        'class' => $clientKey
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.select-with-label', [
        'name'      => $service->getOptionKey($suffixRegion, $clientClass),
        'title'     => _wpcc('Region'),
        'info'      => _wpcc('Region to which the API requests will be sent. You can select the region closest to where your server is.'),
        'options'   => \WPCCrawler\Objects\Transformation\Translation\Clients\AmazonTranslateAPIClient::getRegions(),
        'class'     => $clientKey
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(true); ?>
<?php echo $__env->make('general-settings.translation.translation-api-settings-base', [
    'apiOptionKeySuffixes' => [
        $suffixAccessKey,
        $suffixSecret,
        $suffixRegion
    ],
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/general-settings/translation/settings-amazon_translate.blade.php ENDPATH**/ ?>