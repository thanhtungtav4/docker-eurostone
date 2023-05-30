<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 28/01/2019
 * Time: 10:09
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Translation;

use Exception;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformAPIClient;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformationService;
use WPCCrawler\Objects\Transformation\Exceptions\TransformationFailedException;
use WPCCrawler\Objects\Transformation\Translation\Clients\AbstractTranslateAPIClient;
use WPCCrawler\Objects\Transformation\Translation\Clients\AmazonTranslateAPIClient;
use WPCCrawler\Objects\Transformation\Translation\Clients\GoogleTranslateAPIClient;
use WPCCrawler\Objects\Transformation\Translation\Clients\MicrosoftTranslateAPIClient;
use WPCCrawler\Objects\Transformation\Translation\Clients\YandexTranslateAPIClient;
use WPCCrawler\Utils;

/**
 * @since 1.9.0
 */
class TranslationService extends AbstractTransformationService {

    // TODO: DeepL Translation API implementation: https://www.deepl.com
    
    /** @var TranslationService|null */
    private static $instance = null;

    const SERVICE_KEY_GOOGLE_TRANSLATE      = 'google_translate';
    const SERVICE_KEY_MICROSOFT_TRANSLATOR  = 'microsoft_translator_text';
    const SERVICE_KEY_YANDEX_TRANSLATE      = 'yandex_translate';
    const SERVICE_KEY_AMAZON_TRANSLATE      = 'amazon_translate';

    /*
     * == HOW TO ADD A NEW TRANSLATION API TO THE PLUGIN ==
     * See README
     */

    /** @var string Prefix of option keys that are used to store available languages for APIs */
    private $languageCacheOptionKeyPrefix = '_wpcc_translation_cached_languages_';

    /**
     * @return TranslationService
     * @since 1.9.0
     */
    public static function getInstance(): TranslationService {
        if (static::$instance === null) {
            static::$instance = new TranslationService();
        }
        
        return static::$instance;
    }

    /** This is a singleton. */
    protected function __construct() {
        parent::__construct();

        // Register APIs
        $this
            ->registerAPIClient(GoogleTranslateAPIClient::class,    TranslationService::SERVICE_KEY_GOOGLE_TRANSLATE,       _wpcc('Google Cloud Translation API'))
            ->registerAPIClient(MicrosoftTranslateAPIClient::class, TranslationService::SERVICE_KEY_MICROSOFT_TRANSLATOR,   _wpcc('Microsoft Translator Text API'))
            ->registerAPIClient(YandexTranslateAPIClient::class,    TranslationService::SERVICE_KEY_YANDEX_TRANSLATE,       _wpcc('Yandex Translate API'))
            ->registerAPIClient(AmazonTranslateAPIClient::class,    TranslationService::SERVICE_KEY_AMAZON_TRANSLATE,       _wpcc('Amazon Translate API'))
        ;
    }

    /**
     * @return string Name of the base API client class. This class is used to check if an API that is wanted to be
     * registered is valid or not.
     *
     * @return class-string
     * @since 1.9.0
     */
    protected function getBaseAPIClientClassName(): string {
        return AbstractTranslateAPIClient::class;
    }

    /**
     * @return string Prefix to be used when creating an option key for the transformation APIs. For example, for
     *                translation, this prefix can be 'translation'. To understand how this is used, see
     *                {@link optionKeyFormat}
     * @since 1.9.0
     */
    protected function getOptionKeyPrefix(): string {
        return 'translation';
    }

    /**
     * @return string Option key (post meta key) that stores the selected transformation service. This is basically the
     *                name of the input field (select field) that the user interacts with to select a transformation
     *                service. For example '_wpcc_selected_translation_service'
     * @since 1.9.0
     */
    protected function getOptionKeyForSelectedService(): string {
        return SettingKey::WPCC_SELECTED_TRANSLATION_SERVICE;
    }

    /**
     * @param AbstractTransformAPIClient $client   The API client that will be used to transform the given texts. The
     *                                             client is an instance of the class returned from
     *                                             {@link getBaseAPIClientClassName()}
     * @param array                      $texts    A flat array of texts, probably retrieved from
     *                                             {@link ValueExtractor::fillAndFlatten()}.
     * @param bool                       $dryRun   If true, the texts will not be transformed. Instead, they will be
     *                                             appended dummy values to mock the transformation.
     *
     * @return null|array If the selected transformation service does not exist, returns null. Otherwise, transformed
     *                    $texts.
     * @throws TransformationFailedException If required parameters for the transformation service selected in the
     *                                       settings are not valid, or there is a transformation error.
     * @since 1.9.0
     */
    protected function applyTransformation(AbstractTransformAPIClient $client, $texts, $dryRun = false) {
        /** @var AbstractTranslateAPIClient $client */
        // Translate the texts considering user's settings
        $textTranslator = new TextTranslator($texts, $dryRun);

        try {
            return $client->translate($textTranslator);

        } catch (Exception $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /*
     *
     */

    /**
     * @return array
     * @since 1.9.0
     */
    public function getLanguagesForView() {
        $languages = $this->getSupportedLanguages();
        $apiLanguages = [];

        foreach($this->getAPIs() as $clz => $key) {
            $langs = Utils::array_get($languages, $key, []);
            $apiLanguages[$key] = [
                'from' => $this->prepareFromLanguagesForSelect($langs),
                'to'   => $langs,
            ];
        }

        return $apiLanguages;
    }

    /**
     * Get language cache option key of a previously-registered translate API service
     *
     * @param class-string<AbstractTransformAPIClient> $translateApiClientClass Class name of an {@link AbstractTranslateAPIClient}
     * @return string|null The language cache option key for the API class
     * @since 1.9.0
     */
    public function getLanguageCacheOptionKeyFor($translateApiClientClass): ?string {
        $key = $this->getKeyFor($translateApiClientClass);
        if (!$key) return null;

        return $this->languageCacheOptionKeyPrefix . $key;
    }

    /**
     * Get supported languages for translation APIs.
     *
     * @param array $data                A 1-dimensional associative array. If this is not available, supported languages
     *                                   will be tried to be retrieved from the cache in case <b>$fromCacheIfPossible</b>
     *                                   is true. The required keys are as follows:
     *                                   <br><br>
     *                                   <b>'serviceType'</b> (string): Key (identifier) of a registered translate API
     *                                      client class. The keys are available in {@link $translationAPIs} variable.
     *                                   <br><br>
     *                                   <b>'serializedTranslationOptions'</b> (string): A serialized string storing all
     *                                      translation settings available in the plugin. This string will be parsed
     *                                      using {@link parse_str()} and a {@link SettingsImpl} instance will be
     *                                      created using the settings. Required configurations for the API clients will
     *                                      be retrieved from the settings.
     *
     * @param bool $fromCacheIfPossible  True if you want to get the cached results if they exist. False if you want to
     *                                   get the results by making requests no matter what. If this is false, $data must
     *                                   be provided.
     *
     * @return array The structure is: <i>[
     *                                   'service1_key' => ["code1" => "Lang 1", "code2" => "Lang 2", ...],
     *                                   'service2_key' => ["code1" => "Lang 1", "code2" => "Lang 2", ...]
     *                                 ]</i>
     */
    public function getSupportedLanguages($data = [], $fromCacheIfPossible = true) {
        $results = [
            "errors" => []
        ];

        // Get the service type and serialized translation options from the given data
        $serviceType                  = Utils::array_get($data, 'serviceType', null);
        $serializedTranslationOptions = Utils::array_get($data, 'serializedTranslationOptions', null);

        // If there are serialized translation options, parse them and create the settings.
        /** @var SettingsImpl|null $settings */
        $settings = null;
        if ($serializedTranslationOptions) {
            parse_str($serializedTranslationOptions, $settingsArr);
            if ($settingsArr) {
                $settings = new SettingsImpl($settingsArr, Factory::postService()->getSingleMetaKeys());
                $settings->setDoNotUseGeneralSettings(true);
            }
        }

        // Populate the results with languages by retrieving them from the registered API clients
        foreach($this->getAPIs() as $apiClass => $key) {
            // Get the cache key for this API client
            $cacheKey = $this->getLanguageCacheOptionKeyFor($apiClass);
            if ($cacheKey === null) continue;
            
            $languages = [];

            // If the results are requested from the cache
            if ($fromCacheIfPossible) {
                $languages = get_option($cacheKey, null);

            // Otherwise, if there are settings and this API is the one whose languages should be retrieved
            } else if($settings && $key === $serviceType) {
                // Create an API instance and set its options using the given settings
                /** @var AbstractTranslateAPIClient $client */
                $client = new $apiClass();

                // Make sure the client is valid
                if (!$this->validateApiClientClass($client)) continue;

                try {
                    $client->setOptionsUsingSettings($settings);

                    // Get the languages
                    $languages = $client->getLocalizedLanguagesAsAssocArray();

                    // Store the languages in the database as a cache
                    update_option($cacheKey, $languages, false);

                } catch (Exception $e) {
                    // Add the errors to the result
                    $results["errors"][] = $e->getMessage();
                }
            }

            // Add the found languages to the results
            $results[$key] = $languages;
        }

        // Add the service type to the results
        $results["serviceType"] = $serviceType;

        // If there are information messages, add them as errors.
        if ($infos = Informer::getInfos()) {
            $infos = array_map(function($info) {
                return $info->getDetails();
            }, $infos);

            $results["errors"] = array_merge($results["errors"], $infos);
        }

        return $results;
    }

    /**
     * Invalidate the cache for available languages for a service type.
     *
     * @param string $serviceType One of the values of the array returned by {@link getAPIs()}
     * @return bool True if there is no more a cache for the given service type. If deletion of the cache was not
     *                            successful, returns false.
     * @throws Exception If the given service type does not exist.
     * @since 1.9.0
     */
    public function invalidateLanguageCacheForService($serviceType) {
        if (!$serviceType || !in_array($serviceType, array_values($this->getAPIs()))) {
            throw new Exception(sprintf(_wpcc('Service type "%1$s" does not exist.'), $serviceType));
        }

        $result = false;
        foreach($this->getAPIs() as $clz => $key) {
            if ($key !== $serviceType) continue;

            // Get the key for the cache
            $cacheKey = $this->getLanguageCacheOptionKeyFor($clz);
            if ($cacheKey === null) continue;

            // Get the available cache, if any.
            $cache = get_option($cacheKey, null);

            // If there is no cache or we have successfully deleted the cache, operation is successful.
            $result = $cache === null || delete_option($cacheKey);

            break;
        }

        return $result;
    }

    /**
     * Prepares "from" languages for select by prepending a "detect language" item.
     *
     * @param array $languages
     * @return array
     */
    public function prepareFromLanguagesForSelect($languages) {
        $detectLanguageItem = ['detect' => _wpcc("Detect language")];
        return $languages ? $detectLanguageItem + $languages : $languages;
    }

    /*
     * STATIC HELPERS
     */

    /**
     * Handles "load/refresh translation languages" request.
     *
     * @param array   $data     Request data.
     * @param boolean $isOption False if this is for site settings. True if this is for general settings.
     * @return string
     * @since 1.9.0
     */
    public static function handleLoadRefreshTranslationLanguagesRequest($data, $isOption) {
        $result = static::getInstance()->getSupportedLanguages($data, false);

        // Send the error view if there are any errors.
        if(isset($result["errors"]) && $result["errors"]) {
            return static::createErrorResponse($result["errors"]);
        }

        $serviceType = Utils::array_get($result, 'serviceType');
        $languages   = $result[$serviceType];

        return static::createLanguageViewResponse($languages, $serviceType, $isOption);
    }

    /**
     * Handles "clear languages" request
     *
     * @param array   $data     Request data. It must contain 'serviceType' key and a valid service type as the value
     *                          of the key.
     * @param boolean $isOption False if this is for site settings. True if this is for general settings.
     * @return string JSON
     * @since 1.9.0
     */
    public static function handleClearLanguagesRequest($data, $isOption) {
        $serviceType = Utils::array_get($data, 'serviceType', null);
        $errorMessage = null;

        try {
            $success = static::getInstance()->invalidateLanguageCacheForService($serviceType);

            if (!$success) {
                $errorMessage = _wpcc('Languages could not be deleted from the database.');
            }

        } catch (Exception $e) {
            // If there was an error, inform the user.
            Informer::addError($e->getMessage())->setException($e)->addAsLog();
            $errorMessage = $e->getMessage();
        }

        return $errorMessage !== null ?
            static::createErrorResponse([$errorMessage]) :
            static::createLanguageViewResponse([], $serviceType, $isOption);
    }

    /*
     * PRIVATE STATIC HELPERS
     */

    /**
     * Create a view as a response for a request that should return select elements of "from" and "to" language options
     * of a translation service.
     *
     * @param array   $languages   Languages for a <b><i>single</i></b> service type, in the format of the result
     *                             returned from {@link getSupportedLanguages()}
     * @param string  $serviceType One of the <b><i>values</i></b> of the array returned from {@link getAPIs()}
     * @param boolean $isOption    False if this is for site settings. True if this is for general settings.
     * @return string JSON A JSON string that contains a single key, 'view'. 'view.from' stores the HTML for the select
     *                element of "from", 'view.to' stores the HTML for the select element of "to".
     * @since 1.9.0
     */
    private static function createLanguageViewResponse($languages, $serviceType, $isOption): string {
        $keyFrom    = "_wpcc_translation_{$serviceType}_from";
        $keyTo      = "_wpcc_translation_{$serviceType}_to";

        $data = [
            'view' => [
                'from' => Utils::view('form-items/select')->with([
                    'name'     => $keyFrom,
                    'options'  => TranslationService::getInstance()->prepareFromLanguagesForSelect($languages),
                    'isOption' => $isOption,
                ])->render(),

                'to' => Utils::view('form-items/select')->with([
                    'name'     => $keyTo,
                    'options'  => $languages,
                    'isOption' => $isOption,
                ])->render()
            ]
        ];

        return json_encode($data) ?: '{}';
    }

    /**
     * Create error response.
     *
     * @param string[] $errors A sequential string array, storing the errors.
     * @return string A JSON having 'view' and 'errors' keys. 'view' stores the HTML view to be shown to the user.
     *                'errors' stores the errors as an array.
     * @since 1.9.0
     */
    private static function createErrorResponse($errors): string {
        return json_encode([
            'view'  =>  Utils::view('partials/test-result')
                ->with("results", $errors)
                ->render(),
            'errors' => $errors,
        ]) ?: '{}';
    }
}
