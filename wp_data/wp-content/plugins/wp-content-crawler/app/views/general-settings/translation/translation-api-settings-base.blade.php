{{-- 
    Required variables:
        string              $clientClass:           Class of a translation API client. E.g. GoogleTranslateAPIClient::class
        string              $clientKey:             Key of a translation API client. E.g. 'google_translate'
        array               $translationLanguages:  Translation languages returned from TranslationService::getTranslationLanguagesForView()
        array               $apiOptionKeySuffixes:  Suffixes of option keys specific for this API. E.g. 'api_key' for Google Translate API
                                                    is the suffix for option key '_wpcc_translate_google_translate_api_key'. These are
                                                    used to define required values for the tests.
        bool                $isOption:              True if this view is intended for general settings page
        TranslationService  $service:               TranslationService instance
--}}

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

{{-- SECTION TITLE --}}
@include('partials.table-section-title', [
    'title' => sprintf(_wpcc('%1$s Options'), $service->getAPIName($clientKey)),
    'class' => $clientKey
])

{{-- TRANSLATION SERVICE - TRANSLATE FROM --}}
<tr class="{{ $clientKey }}" aria-label="{{ $keyFrom }}">
    <td>
        @include('form-items/label', [
            'for'   =>  $keyFrom,
            'title' =>  _wpcc('Translate from'),
            'info'  =>  _wpcc('Select the language of the content of crawled posts.')
        ])
    </td>
    <td>
        @if($isLanguagesAvailable)
            @include('form-items/select', [
                'name'      =>  $keyFrom,
                'options'   =>  $languagesFrom,
                'isOption'  =>  $isOption,
            ])
        @else
            @include('form-items/partials/button-load-languages', $optionsLoadLanguagesButton + ['id' => $keyFrom])
        @endif
    </td>
</tr>

{{-- TRANSLATION SERVICE - TRANSLATE TO --}}
<tr class="{{ $clientKey }}" aria-label="{{ $keyTo }}">
    <td>
        @include('form-items/label', [
            'for'   =>  $keyTo,
            'title' =>  _wpcc('Translate to'),
            'info'  =>  _wpcc('Select the language to which the content should be translated.')
        ])
    </td>
    <td>
        @if($isLanguagesAvailable)
            @include('form-items/select', [
                'name'      =>  $keyTo,
                'options'   =>  $languagesTo,
                'isOption'  =>  $isOption,
            ])
        @else
            @include('form-items/partials/button-load-languages', $optionsLoadLanguagesButton + ['id' => $keyTo])
        @endif
    </td>
</tr>

{{-- TRANSLATION SERVICE - REFRESH LANGUAGES --}}
@if($isLanguagesAvailable)
    <tr class="{{ $clientKey }}">
        <td>
            @include('form-items/label', $optionsRefreshLanguagesLabel)
        </td>
        <td>
            @include('form-items/partials/button-load-languages', $optionsLoadLanguagesButton)
        </td>
    </tr>

    <tr class="{{ $clientKey }}">
        <td>
            @include('form-items/label', $optionsClearLanguagesLabel)
        </td>
        <td>
            @include('form-items/partials/button-clear-languages', $optionsClearLanguagesButton)
        </td>
    </tr>
@endif

{{-- INCLUDE API OPTIONS --}}
@yield('api-options')

{{-- TRANSLATION SERVICE - TEST --}}
<tr class="{{ $clientKey }}" aria-label="{{ $keyTest }}">
    <td>
        @include('form-items/label', [
            'for'   =>  $keyTest,
            'title' =>  _wpcc('Test Translation Options'),
            'info'  =>  _wpcc('You can write any text to test the translation options you configured.')
        ])
    </td>
    <td>
        @include('form-items/textarea', [
            'name'          =>  $keyTest,
            'placeholder'   =>  _wpcc('Test text to translate...'),
            'data'          =>  $testOptionData,
            'addon'         =>  'dashicons dashicons-search',
            'test'          =>  true,
            'addonClasses'  => 'wcc-test-translation ' . $clientKey,
        ])
        @include('partials/test-result-container')
    </td>
</tr>