

<?php
/** @var string $clientClass */
/** @var string $clientKey */
/** @var array $apiOptionKeySuffixes */
// Get option keys for the given API client
$keyTest        = $service->getOptionKey('test', $clientClass);
$keyUsageStats  = $service->getOptionKey('usage_stats', $clientClass);

$testRequiredSelectorsString = "#{$keyTest}";
$testOptionData = [
    'testType'    =>  \WPCCrawler\Test\Test::$TEST_TYPE_SPINNING,
    'serviceType' =>  $clientKey,
];

$requiredSelectorsStringForOptionSuffices = implode(' & ', array_map(function($suffix) use (&$service, $clientClass) {
    return '#' . $service->getOptionKey($suffix, $clientClass);
}, $apiOptionKeySuffixes));

if ($requiredSelectorsStringForOptionSuffices) {
    $testRequiredSelectorsString .= ' & ' . $requiredSelectorsStringForOptionSuffices;
}

$testOptionData['requiredSelectors'] =  $testRequiredSelectorsString;

?>


<?php echo $__env->make('partials.table-section-title', [
    'title' => sprintf(_wpcc('%1$s Options'), $service->getAPIName($clientKey)),
    'class' => $clientKey
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<tr class="<?php echo e($clientKey); ?>">
    <td>
        <?php echo $__env->make('form-items.label', [
            'title' =>  _wpcc('Register'),
            'info'  =>  _wpcc('If you do not have an account, you can click this link to register to the service.')
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
    <td>
        <div class="input-group">
            <a href="<?php echo e($registrationLink); ?>" target="_blank"><?php echo e(_wpcc('Click here to register if you do not have an account')); ?></a>
        </div>
    </td>
</tr>


<?php echo $__env->yieldContent('api-options'); ?>


<tr class="<?php echo e($clientKey); ?>" aria-label="<?php echo e($keyTest); ?>">
    <td>
        <?php echo $__env->make('form-items.label', [
            'for'   =>  $keyTest,
            'title' =>  _wpcc('Test Spinning Options'),
            'info'  =>  _wpcc('You can write any text to test the spinning options you configured.')
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
    <td>
        <?php echo $__env->make('form-items.textarea', [
            'name'          =>  $keyTest,
            'placeholder'   =>  _wpcc('Test text to spin...'),
            'data'          =>  $testOptionData,
            'addon'         =>  'dashicons dashicons-search',
            'test'          =>  true,
            'addonClasses'  => 'wcc-test-spinning ' . $clientKey,
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('partials.test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
</tr>


<tr class="<?php echo e($clientKey); ?>">
    <td>
        <?php echo $__env->make('form-items.label', [
            'for'   =>  $keyUsageStats,
            'title' =>  _wpcc('Check API Usage Statistics'),
            'info'  =>  _wpcc('Click the button to see API usage statistics.')
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
    <td>
        <div class="input-group addon">
            <input type="hidden" name="<?php echo e($keyUsageStats); ?>" value="1">
            <?php echo $__env->make('form-items.partials.button-addon-test', [
                'data'          =>  [
                    'testType'          =>  \WPCCrawler\Test\Test::$TEST_TYPE_SPINNING_API_STATS,
                    'serviceType'       =>  $clientKey,
                    'requiredSelectors' =>  $requiredSelectorsStringForOptionSuffices,
                ],
                'addon'         =>  'dashicons dashicons-media-text',
                'addonTitle'    => _wpcc('Get usage statistics'),
                'test'          => true,
                'addonClasses'  => 'wcc-test-spinning api-stats ' . $clientKey,
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <?php echo $__env->make('partials.test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </td>
</tr><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/general-settings/spinning/spinning-api-settings-base.blade.php ENDPATH**/ ?>