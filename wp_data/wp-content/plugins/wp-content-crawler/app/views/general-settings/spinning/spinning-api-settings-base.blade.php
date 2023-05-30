{{--
    Required variables:
        string          $clientClass:           Class of a translation API client. E.g. GoogleTranslateAPIClient::class
        string          $clientKey:             Key of a translation API client. E.g. 'google_translate'
        array           $apiOptionKeySuffixes:  Suffixes of option keys specific for this API. E.g. 'api_key' for Google Translate API
                                                is the suffix for option key '_wpcc_translate_google_translate_api_key'. These are
                                                used to define required values for the tests.
        string          $registrationLink:      A link that goes to the registration page for new account creation for the API
        bool            $isOption:              True if this view is intended for general settings page
        SpinningService $service:               SpinningService instance
--}}

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

{{-- SECTION TITLE --}}
@include('partials.table-section-title', [
    'title' => sprintf(_wpcc('%1$s Options'), $service->getAPIName($clientKey)),
    'class' => $clientKey
])

{{-- REGISTRATION LINK --}}
<tr class="{{ $clientKey }}">
    <td>
        @include('form-items.label', [
            'title' =>  _wpcc('Register'),
            'info'  =>  _wpcc('If you do not have an account, you can click this link to register to the service.')
        ])
    </td>
    <td>
        <div class="input-group">
            <a href="{{ $registrationLink }}" target="_blank">{{ _wpcc('Click here to register if you do not have an account') }}</a>
        </div>
    </td>
</tr>

{{-- INCLUDE API OPTIONS --}}
@yield('api-options')

{{-- SPINNING SERVICE - TEST --}}
<tr class="{{ $clientKey }}" aria-label="{{ $keyTest }}">
    <td>
        @include('form-items.label', [
            'for'   =>  $keyTest,
            'title' =>  _wpcc('Test Spinning Options'),
            'info'  =>  _wpcc('You can write any text to test the spinning options you configured.')
        ])
    </td>
    <td>
        @include('form-items.textarea', [
            'name'          =>  $keyTest,
            'placeholder'   =>  _wpcc('Test text to spin...'),
            'data'          =>  $testOptionData,
            'addon'         =>  'dashicons dashicons-search',
            'test'          =>  true,
            'addonClasses'  => 'wcc-test-spinning ' . $clientKey,
        ])
        @include('partials.test-result-container')
    </td>
</tr>

{{-- SPINNING SERVICE - USAGE STATISTICS --}}
<tr class="{{ $clientKey }}">
    <td>
        @include('form-items.label', [
            'for'   =>  $keyUsageStats,
            'title' =>  _wpcc('Check API Usage Statistics'),
            'info'  =>  _wpcc('Click the button to see API usage statistics.')
        ])
    </td>
    <td>
        <div class="input-group addon">
            <input type="hidden" name="{{ $keyUsageStats }}" value="1">
            @include('form-items.partials.button-addon-test', [
                'data'          =>  [
                    'testType'          =>  \WPCCrawler\Test\Test::$TEST_TYPE_SPINNING_API_STATS,
                    'serviceType'       =>  $clientKey,
                    'requiredSelectors' =>  $requiredSelectorsStringForOptionSuffices,
                ],
                'addon'         =>  'dashicons dashicons-media-text',
                'addonTitle'    => _wpcc('Get usage statistics'),
                'test'          => true,
                'addonClasses'  => 'wcc-test-spinning api-stats ' . $clientKey,
            ])
        </div>
        @include('partials.test-result-container')
    </td>
</tr>