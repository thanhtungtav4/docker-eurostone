<?php

$apiDocsUrl = \WPCCrawler\Objects\Transformation\Spinning\Clients\SpinRewriterClient::getAffiliateLink('https://www.spinrewriter.com/cp-api');
$transFormatEnableFeature = _wpcc('Check this to enable "%1$s" feature.');
$transVisitApiPage = sprintf(_wpcc('For more information, visit %1$s page.'), '<a href="' . $apiDocsUrl . '" target="_blank">' . _wpcc('API documentation') . '</a>');

$callbackCreateCheckboxVars = function($suffix, $title, $featureName, $additionalInfo = null) use (&$clientKey, &$clientClass, &$service, &$transVisitApiPage, &$transFormatEnableFeature) {
    /** @var \WPCCrawler\Objects\Transformation\Spinning\SpinningService $service */
    $info = sprintf($transFormatEnableFeature, $featureName);

    if ($additionalInfo) {
        $info .= ' ' . $additionalInfo;
    }

    $info .= ' ' . $transVisitApiPage;
    return [
        'name'  => $service->getOptionKey($suffix, $clientClass),
        'title' => $title,
        'info'  => $info,
        'class' => $clientKey
    ];
};

$suffixApiKey           = 'api_key';
$suffixEmail            = 'email';
$suffixConfidenceLevel  = 'confidence_level';

$checkboxOptions = [
    $callbackCreateCheckboxVars('auto_protected_terms', _wpcc('Auto Protected Terms'),  _wpcc('auto protected terms')),
    $callbackCreateCheckboxVars('nested_spintax',       _wpcc('Nested Spintax'),        _wpcc('nested spintax')),
    $callbackCreateCheckboxVars('auto_sentences',       _wpcc('Auto Sentences'),        _wpcc('auto sentences')),
    $callbackCreateCheckboxVars('auto_paragraphs',      _wpcc('Auto Paragraphs'),       _wpcc('auto paragraphs'), sprintf(_wpcc('This requires %1$s option to be enabled.'), '<b>' . _wpcc('Text with spintax') . '</b>')),
    $callbackCreateCheckboxVars('auto_new_paragraphs',  _wpcc('Auto New Paragraphs'),   _wpcc('auto new paragraphs')),
    $callbackCreateCheckboxVars('auto_sentence_trees',  _wpcc('Auto Sentence Trees'),   _wpcc('auto sentence trees')),
    $callbackCreateCheckboxVars('use_only_synonyms',    _wpcc('Use Only Synonyms'),     _wpcc('use only synonyms')),
    $callbackCreateCheckboxVars('reorder_paragraphs',   _wpcc('Reorder Paragraphs'),    _wpcc('reorder paragraphs')),
];

?>



<?php $__env->startSection('api-options'); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'          => $service->getOptionKey($suffixEmail, $clientClass),
        'title'         => _wpcc('Email address'),
        'info'          => _wpcc('Email address that you used to register to Spin Rewriter.'),
        'placeholder'   => _wpcc('Email address...'),
        'class'         => $clientKey
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'          => $service->getOptionKey($suffixApiKey, $clientClass),
        'title'         => _wpcc('API Key'),
        'info'          => _wpcc('API key retrieved from Spin Rewriter.'),
        'placeholder'   => _wpcc('API key...'),
        'class'         => $clientKey
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.select-with-label', [
        'name'      => $service->getOptionKey($suffixConfidenceLevel, $clientClass),
        'title'     => _wpcc('Confidence Level'),
        'info'      => _wpcc('Select the confidence level for the spun text.') . ' ' . $transVisitApiPage,
        'options'   => \WPCCrawler\Objects\Transformation\Spinning\Clients\SpinRewriterClient::getConfidenceLevelsForSelect(),
        'class'     => $clientKey
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php $__currentLoopData = $checkboxOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variables): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('form-items.combined.checkbox-with-label', $variables, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => $service->getOptionKey('text_with_spintax', $clientClass),
        'title' => _wpcc('Text with spintax?'),
        'info'  => _wpcc('Check this if you want to get the spinning result in spin syntax, not human readable text.') . ' ' . $transVisitApiPage,
        'class' => $clientKey,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(true); ?>
<?php echo $__env->make('general-settings.spinning.spinning-api-settings-base', [
    'apiOptionKeySuffixes'  => [$suffixApiKey, $suffixEmail],
    'registrationLink'      => \WPCCrawler\Objects\Transformation\Spinning\Clients\SpinRewriterClient::getAffiliateLink('https://www.spinrewriter.com/'),
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/general-settings/spinning/settings-spin_rewriter.blade.php ENDPATH**/ ?>