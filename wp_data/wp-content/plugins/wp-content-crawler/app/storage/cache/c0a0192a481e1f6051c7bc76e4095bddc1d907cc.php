

<?php
/** @var string $clientClass */
/** @var string $clientKey */
/** @var array $translationLanguages */
/** @var array $apiOptionKeySuffixes */

// Prepare language options
$languagesFrom = $translationLanguages[$clientKey]['from'];
$languagesTo = $translationLanguages[$clientKey]['to'];

$isLanguagesAvailable = $languagesFrom && $languagesTo;
$buttonWithBase = [
    'class'                 => $clientKey,
    'isLanguagesAvailable'  => $isLanguagesAvailable,
    'data' => [
        'serviceType' => $clientKey,
    ],
];

$optionsLoadLanguagesButton = $buttonWithBase;
$optionsLoadLanguagesButton['data']['requestType'] = 'load_refresh_translation_languages';

$optionsClearLanguagesButton = $buttonWithBase;
$optionsClearLanguagesButton['data']['requestType'] = 'clear_translation_languages';

// Other variables
$optionsRefreshLanguagesLabel = [
    'title' => _wpcc('Refresh languages'),
    'info'  => _wpcc('Refresh languages by retrieving them from the API. By this way, if there are new languages, you can get them.')
];
$optionsClearLanguagesLabel = [
    'title' => _wpcc('Clear languages'),
    'info'  => _wpcc('Delete the languages stored in the database.')
];

// Get option keys for the given API client
$keyFrom    = $service->getOptionKey('from', $clientClass);
$keyTo      = $service->getOptionKey('to',   $clientClass);
$keyTest    = $service->getOptionKey('test', $clientClass);

$testRequiredSelectorsString = "#{$keyTest} & #{$keyFrom} & #{$keyTo}";
$testOptionData = [
    'fromSelector'      => '#' . $keyFrom,
    'toSelector'        => '#' . $keyTo,
    'testType'          =>  \WPCCrawler\Test\Test::$TEST_TYPE_TRANSLATION,
    'serviceType'       =>  $clientKey,
];

foreach($apiOptionKeySuffixes as $suffix) {
    $optionKey = $service->getOptionKey($suffix, $clientClass);
    $testRequiredSelectorsString .= ' & #' . $optionKey;
}

$testOptionData['requiredSelectors'] =  $testRequiredSelectorsString;
        
?>


<?php echo $__env->make('partials.table-section-title', [
    'title' => sprintf(_wpcc('%1$s Options'), $service->getAPIName($clientKey)),
    'class' => $clientKey
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<tr class="<?php echo e($clientKey); ?>" aria-label="<?php echo e($keyFrom); ?>">
    <td>
        <?php echo $__env->make('form-items/label', [
            'for'   =>  $keyFrom,
            'title' =>  _wpcc('Translate from'),
            'info'  =>  _wpcc('Select the language of the content of crawled posts.')
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
    <td>
        <?php if($isLanguagesAvailable): ?>
            <?php echo $__env->make('form-items/select', [
                'name'      =>  $keyFrom,
                'options'   =>  $languagesFrom,
                'isOption'  =>  $isOption,
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('form-items/partials/button-load-languages', $optionsLoadLanguagesButton + ['id' => $keyFrom], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    </td>
</tr>


<tr class="<?php echo e($clientKey); ?>" aria-label="<?php echo e($keyTo); ?>">
    <td>
        <?php echo $__env->make('form-items/label', [
            'for'   =>  $keyTo,
            'title' =>  _wpcc('Translate to'),
            'info'  =>  _wpcc('Select the language to which the content should be translated.')
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
    <td>
        <?php if($isLanguagesAvailable): ?>
            <?php echo $__env->make('form-items/select', [
                'name'      =>  $keyTo,
                'options'   =>  $languagesTo,
                'isOption'  =>  $isOption,
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('form-items/partials/button-load-languages', $optionsLoadLanguagesButton + ['id' => $keyTo], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    </td>
</tr>


<?php if($isLanguagesAvailable): ?>
    <tr class="<?php echo e($clientKey); ?>">
        <td>
            <?php echo $__env->make('form-items/label', $optionsRefreshLanguagesLabel, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
        <td>
            <?php echo $__env->make('form-items/partials/button-load-languages', $optionsLoadLanguagesButton, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
    </tr>

    <tr class="<?php echo e($clientKey); ?>">
        <td>
            <?php echo $__env->make('form-items/label', $optionsClearLanguagesLabel, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
        <td>
            <?php echo $__env->make('form-items/partials/button-clear-languages', $optionsClearLanguagesButton, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
    </tr>
<?php endif; ?>


<?php echo $__env->yieldContent('api-options'); ?>


<tr class="<?php echo e($clientKey); ?>" aria-label="<?php echo e($keyTest); ?>">
    <td>
        <?php echo $__env->make('form-items/label', [
            'for'   =>  $keyTest,
            'title' =>  _wpcc('Test Translation Options'),
            'info'  =>  _wpcc('You can write any text to test the translation options you configured.')
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
    <td>
        <?php echo $__env->make('form-items/textarea', [
            'name'          =>  $keyTest,
            'placeholder'   =>  _wpcc('Test text to translate...'),
            'data'          =>  $testOptionData,
            'addon'         =>  'dashicons dashicons-search',
            'test'          =>  true,
            'addonClasses'  => 'wcc-test-translation ' . $clientKey,
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
</tr><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/general-settings/translation/translation-api-settings-base.blade.php ENDPATH**/ ?>