<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 16/02/2019
 * Time: 18:42
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Base;

use Exception;
use Illuminate\Support\Str;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Exceptions\TransformationFailedException;
use WPCCrawler\Objects\Transformation\Objects\TransformableFieldList;
use WPCCrawler\Utils;

/**
 * Base class for a transformation service. A transformation service is a service that transforms details of a post. For
 * example, a translation service or a spinning service can be considered as transformation services, since they both
 * transform the details of a post. This class assumes a transformation service is used over an API. Hence, this class
 * provides the necessary backbone for registering and dealing with the APIs of transformation services.
 *
 * @package WPCCrawler\Objects\Transformation\Base
 * @since   1.8.1
 */
abstract class AbstractTransformationService {

    /**
     * @var array<class-string<AbstractTransformAPIClient>, string> An associative array that stores transformation
     *      service API class name as key. The value is a string that is used as a key for the transformation API. For
     *      example, 'google_translate' can be used as key for GoogleTranslateAPIClient class. In that case, the array
     *      should be:
     *        [ GoogleTranslateAPIClient::class => 'google_translate' ]
     */
    private $apiRegistry = [];

    /**
     * @var array An associative array where keys are registered transformation API keys and the values are arrays. The
     *            value array must contain 'name' key with value being the name of the transformation API. E.g.
     *            ['name' => 'Google Translate']. These values will be used to show the user available APIs and let
     *            him/her select an API using a select HTML element.
     */
    private $optionsForSelect;

    /**
     * @var string %1$s: Value returned from {@link getOptionKeyPrefix()}. E.g. 'translation'<br>
     *             %2$s: Identifier for transformation API. E.g. 'google_translate'<br>
     *             %3$s: Actual option key. For example, 'api_key'.<br>
     *             An example output could be '_wpcc_translation_google_translate_api_key'
     */
    private $optionKeyFormat = '_wpcc_%1$s_%2$s_%3$s';

    /** This is a singleton */
    protected function __construct() {
        // Initialize the options for select with a default option.
        $this->optionsForSelect = ['-1' => _wpcc('Select a service')];
    }

    /**
     * Register an API client
     *
     * @param class-string<AbstractTransformAPIClient> $class An API client class
     * @param string       $key   A unique key for the client. It must not contain any spaces. Preferably, lowercase.
     * @param string       $name  Name for the API client. This will be shown to the user where appropriate, such as
     *                            selecting an API in a select HTML element.
     * @return AbstractTransformationService
     * @since 1.9.0
     */
    public function registerAPIClient(string $class, string $key, string $name): self {
        // If this class is already registered, do not register it again.
        if (isset($this->apiRegistry[$class])) return $this;

        $this->apiRegistry[$class] = $key;
        $this->optionsForSelect[$key] = [
            'name' => $name
        ];

        return $this;
    }

    /**
     * Get the options that can be used in a select element that the user can select which transformation service he/she
     * wants to use.
     *
     * @return array
     */
    public function getOptionsForSelect(): array {
        return $this->optionsForSelect;
    }

    /**
     * @return array<class-string<AbstractTransformAPIClient>, string> See {@link $apiRegistry}
     * @since 1.9.0
     */
    public function getAPIs(): array {
        return $this->apiRegistry;
    }

    /**
     * Get name of a transformation API client class
     *
     * @param string $apiClientKey Key of a registered transformation API client
     * @return string Name of the API that can be shown to the user
     * @since 1.9.0
     */
    public function getAPIName($apiClientKey) {
        return $this->optionsForSelect[$apiClientKey]['name'];
    }

    /**
     * Get key (identifier) of a previously-registered transformation API service
     *
     * @param class-string<AbstractTransformAPIClient> $transformationApiClientClass Class name of a class that extends
     *                                                                               the class returned from
     *                                                                               {@link getBaseAPIClientClassName()}
     * @return string|null The key for the API class
     * @since 1.9.0
     */
    public function getKeyFor($transformationApiClientClass) {
        if (!$transformationApiClientClass) return null;
        return Utils::array_get($this->apiRegistry, $transformationApiClientClass, null);
    }

    /**
     * Get transformation API client class name using its key (identifier).
     *
     * @param string $key The key
     * @return string|null Name of the class or null if there is no class for the given key.
     * @since 1.9.0
     */
    public function getClassFor($key) {
        if (!$key) return null;
        return Utils::array_get(array_flip($this->apiRegistry), $key, null);
    }

    /**
     * Transforms the given texts
     *
     * @param SettingsImpl $settings See {@link transformWithApiClient()}
     * @param array        $texts    See {@link transformWithApiClient()}
     * @param bool         $dryRun   See {@link transformWithApiClient()}
     *
     * @return null|array See {@link transformWithApiClient()}
     * @throws TransformationFailedException See {@link transformWithApiClient()}
     * @throws Exception See {@link createApiClientUsingSettings()}
     * @uses  AbstractTransformationService::transformWithApiClient()
     * @since 1.9.0
     */
    public function transform(SettingsImpl $settings, $texts, $dryRun = false) {
        $client = $this->createApiClientUsingSettings($settings);
        if (!$client) return null;

        return $this->transformWithApiClient($client, $texts, $dryRun);
    }

    /**
     * Transforms the given texts using the given API client.
     *
     * @param AbstractTransformAPIClient $client   See {@link applyTransformation()}
     * @param array                      $texts    See {@link applyTransformation()}
     * @param bool                       $dryRun   See {@link applyTransformation()}
     *
     * @return null|array See {@link applyTransformation()}
     * @throws TransformationFailedException See {@link applyTransformation()}
     * @uses  AbstractTransformationService::applyTransformation()
     * @since 1.9.0
     */
    public function transformWithApiClient(AbstractTransformAPIClient $client, $texts, $dryRun = false) {
        $transformedTexts = $this->applyTransformation($client, $texts, $dryRun);

        // If the transformation is not successful, throw an exception.
        if(is_array($transformedTexts) && sizeof($texts) != sizeof($transformedTexts)) {
            throw new TransformationFailedException("WPCC - Texts could not be transformed.");
        }

        return $transformedTexts;
    }

    /**
     * Creates an API client instance using the settings.
     *
     * @param SettingsImpl $settings            Settings that will be used to retrieve API client details such as API
     *                                          key and target and source languages. These settings are the settings of
     *                                          the plugin, actually. You can directly provide the plugin's settings or
     *                                          the ones that are related to the transformation service.
     * @return null|AbstractTransformAPIClient  Null or an object that is an instance of the class returned from
     *                                          {@link getBaseAPIClientClassName()}
     * @since 1.9.0
     * @throws Exception See {@link AbstractTransformAPIClient::setOptionsUsingSettings()}
     */
    public function createApiClientUsingSettings(SettingsImpl $settings): ?AbstractTransformAPIClient {
        // Get the selected API service key from settings
        $selectedTransformationService = $settings->getSetting($this->getOptionKeyForSelectedService());

        // Get the class for the selected API
        $apiClass = $this->getClassFor($selectedTransformationService);

        // If there is no API class for the selected service key, notify the user and stop.
        if (!$apiClass) {
            Informer::addError(sprintf(_wpcc('Selected transformation service %1$s is not valid'), $selectedTransformationService))
                ->addAsLog();
            return null;
        }

        // Create an instance of the selected API client
        $client = new $apiClass();

        // Make sure the client is valid
        if (!$this->validateApiClientClass($client)) return null;

        /** @var AbstractTransformAPIClient $client */
        $client->setOptionsUsingSettings($settings);

        return $client;
    }

    /**
     * Get transformable fields from the selected options for a given identifier.
     *
     * @param array  $selectedOptions A sequential array where values are the selected option values.
     * @param string $identifier      An identifier used in {@link prepareTransformableFieldForSelect()}
     * @since 1.9.0
     * @return array An array of prepared transformable field keys.
     */
    public function getTransformableFieldsFromSelect($selectedOptions, $identifier): array {
        if (!$selectedOptions || !$identifier) return [];

        $identifierLength = strlen($identifier) + 1; // +1 for "."

        return array_filter(array_map(function($optionValue) use (&$identifier, &$identifierLength) {
            return Str::startsWith($optionValue, $identifier) ? substr($optionValue, $identifierLength) : false;
        }, $selectedOptions));
    }

    /**
     * Get an option key for a transformation API client class
     *
     * @param string $optionSuffix Suffix for the key. This will be appended to the base key name.
     * @param class-string<AbstractTransformAPIClient> $apiClientClass Class name for a registered transformation API
     *                                                                 client. E.g. GoogleTranslateAPIClient::class
     * @return string Option key For example, if the suffix is 'from' and the API client class is
     *                GoogleTranslateAPIClient, then the created option key, assuming the API class' key is
     *                'google_translate', will be '_wpcc_translation_google_translate_from'
     * @since 1.9.0
     */
    public function getOptionKey($optionSuffix, $apiClientClass): string {
        return sprintf($this->optionKeyFormat, $this->getOptionKeyPrefix(), $this->getKeyFor($apiClientClass), $optionSuffix);
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * Checks if the given API client is an instance of the base API client class returned from
     * {@link getBaseAPIClientClassName()}. If not, notifies the user and adds an error log.
     *
     * @param object $client
     * @return bool True if the client class is valid. Otherwise, false.
     * @since 1.9.0
     */
    protected function validateApiClientClass($client): bool {
        if (!is_a($client, $this->getBaseAPIClientClassName())) {
            Informer::addError(sprintf('Class %1$s is not a %2$s. You must provide a %2$s.', get_class($client), $this->getBaseAPIClientClassName()))
                ->addAsLog();
            return false;
        }

        return true;
    }

    /*
     * STATIC METHODS
     */

    /**
     * Prepare transformable fields to be shown in a select HTML element. This basically prepends the given identifier
     * to each transformable field's key.
     *
     * @param array $transformableFields A key-value pair. For translation, this can be retrieved from
     *                                   {@link Transformable::getTransformableFields()} by calling
     *                                   {@link TransformableFieldList::toAssociativeArray()}
     * @param string $identifier An identifier that will be prepended to each key of $transformableFields
     * @return array<string, string> Transformable fields with the identifier prepended to their keys
     * @since 1.9.0
     */
    public static function prepareTransformableFieldForSelect(array $transformableFields, string $identifier): array {
        if (!$transformableFields || !$identifier) return [];

        // Prepare the keys of the transformable fields by prepending the identifier
        $options = [];
        foreach($transformableFields as $dotKey => $description) {
            $options["$identifier.$dotKey"] = $description;
        }

        return $options;
    }

    /*
     * PROTECTED ABSTRACT METHODS
     */

    /**
     * @return class-string Name of the base API client class. This class is used to check if an API that is wanted to
     * be registered is valid or not.
     * @since 1.9.0
     */
    protected abstract function getBaseAPIClientClassName(): string;

    /**
     * @return string Prefix to be used when creating an option key for the transformation APIs. For example, for
     *                translation, this prefix can be 'translation'. To understand how this is used, see
     *                {@link optionKeyFormat}
     * @since 1.9.0
     */
    protected abstract function getOptionKeyPrefix(): string;

    /**
     * @return string Option key (post meta key) that stores the selected transformation service. This is basically the
     *                name of the input field (select field) that the user interacts with to select a transformation
     *                service. For example '_wpcc_selected_translation_service'
     * @since 1.9.0
     */
    protected abstract function getOptionKeyForSelectedService(): string;

    /**
     * Apply transformation, such as translation or spinning, to the given texts.
     *
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
    protected abstract function applyTransformation(AbstractTransformAPIClient $client, $texts, $dryRun = false);

}
