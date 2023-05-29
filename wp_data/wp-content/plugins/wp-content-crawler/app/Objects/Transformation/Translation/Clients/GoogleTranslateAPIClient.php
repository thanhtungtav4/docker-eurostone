<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 30/12/2018
 * Time: 11:58
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\Transformation\Translation\Clients;


use Exception;
use Google\Cloud\Translate\V2\TranslateClient;
use WPCCrawler\Objects\Chunk\Enum\ChunkType;
use WPCCrawler\Objects\Chunk\TransformationChunker;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Translation\TextTranslator;
use WPCCrawler\Utils;

class GoogleTranslateAPIClient extends AbstractTranslateAPIClient {

    // TODO: Do not use Google's client library because it is too dependent on other packages. The library adds 0.9 MB
    //  to the plugin's artifact file, which makes the size 120% and file count 132%. We can just use already-available
    //  Guzzle to send a few requests.

    /*
        GOOGLE TRANSLATE SETTINGS

        Some notes:
            * Limits can be found here: https://cloud.google.com/translate/quotas
            * Google does not charge per request. It charges per character. So, no limit for request count.
            * Google suggests maximum request length of 2000 characters.

        Additional notes:
            * Setting max text count per batch greater than 128 causes "too many text segments" error. This is coming
              from experiments. I did not see anything about this limit on the web. Keeping it below 128 is good practice.
    */

    /** @var string */
    private $keyProjectId   = 'projectId';
    /** @var string */
    private $keyApiKey      = 'key';

    /** @var string */
    private $settingKeyFrom         = SettingKey::WPCC_TRANSLATION_GOOGLE_TRANSLATE_FROM;
    /** @var string */
    private $settingKeyTo           = SettingKey::WPCC_TRANSLATION_GOOGLE_TRANSLATE_TO;
    /** @var string */
    private $settingKeyProjectId    = SettingKey::WPCC_TRANSLATION_GOOGLE_TRANSLATE_PROJECT_ID;
    /** @var string */
    private $settingKeyApiKey       = SettingKey::WPCC_TRANSLATION_GOOGLE_TRANSLATE_API_KEY;

    /** @var TranslateClient */
    private $client;

    /** @var string From language */
    private $from;

    /** @var string To language */
    private $to;

    /**
     * @param array $options An associative array that must have 'projectId' and 'key' keys.
     *                       'projectId':   ID of the project created in Google Cloud Console
     *                       'key':         API key existing for the given project ID
     */
    public function __construct($options = []) {
        parent::__construct($options);
    }

    /**
     * Initialize the API client using the already-set options (See {@link getOption()})
     *
     * @return void
     * @since 1.9.0
     */
    public function init() {
        $projectId = $this->getOption($this->keyProjectId);
        $key       = $this->getOption($this->keyApiKey);

        $this->initTranslator($projectId, $key);
    }

    /**
     * Translate texts using the settings. This method might override the options given in the constructor.
     *
     * @param TextTranslator $textTranslator
     * @return array See {@link TextTranslator::translate()}
     * @since 1.9.0
     * @throws Exception When {@link $from} or {@link $to} does not exist.
     * @uses  TextTranslator::translate()
     */
    public function translate(TextTranslator $textTranslator) {
        if (!$this->from || !$this->to) {
            throw new Exception("From and to languages must be set.");
        }

        return $textTranslator->translate($this, [
            'source' => $this->sanitizeFrom($this->from),
            'target' => $this->to,
        ]);
    }

    /**
     * Set the options of this API client using given settings.
     *
     * @param SettingsImpl $settings
     * @return void
     * @since 1.9.0
     * @throws Exception When the settings do not have the required options.
     */
    public function setOptionsUsingSettings(SettingsImpl $settings) {
        $projectId = $settings->getSetting($this->settingKeyProjectId);
        $apiKey    = $settings->getSetting($this->settingKeyApiKey);

        if(!$projectId || !$apiKey) {
            throw new Exception("You must provide a valid project ID and a valid API key for Google Cloud Translation API to work properly.");
        }

        $this->from = $settings->getSetting($this->settingKeyFrom);
        $this->to   = $settings->getSetting($this->settingKeyTo);

        $this->setOptions([
            $this->keyProjectId => $projectId,
            $this->keyApiKey    => $apiKey
        ]);
    }

    /**
     * Create the chunker that will be used to divide given texts into several parts to satisfy the requirements of the
     * API
     *
     * @param array $texts See {@link TransformationChunker::__construct()}
     * @return TransformationChunker
     * @throws Exception
     * @since 1.9.0
     */
    public function createChunker(array $texts): TransformationChunker {
        return new TransformationChunker(ChunkType::T_CHARS, $texts, 1800, 1800, 100);
    }

    /**
     * Translate an array of strings.
     *
     * @param array $texts   A flat sequential array of texts
     * @param array $options Translation options
     * @return array Translated texts in the same order as $texts.
     * @since 1.8.0
     */
    public function translateBatch(array $texts, $options = []) {
        try {
            $translations = $this->client->translateBatch($texts, $options);

            /*
             * The response array is structured as:
             *      'source':  ISO 639-1 code of the source language of the raw text
             *      'input':   Raw text
             *      'text':    Translated text
             *      'model':   The model to use for the translation request. May be `nmt` or `base`. Defaults to null.
             *                 Since there is no "model" parameter that can be passed to the function, this will always
             *                 be null.
             */

            // Here, we get all 'text' values.
            return array_column($translations, 'text');

        // Catch the errors and add them to the other errors.
        } catch(Exception $e) {
            $message = $e->getMessage();
            $response = json_decode($message, true);

            Informer::add((new Information(
                get_class($e),
                Utils::array_get($response, "error.message", ''),
                InformationType::ERROR
            ))->setException($e)->addAsLog());

            return [];
        }
    }

    /**
     * Get languages
     *
     * @param array $options A key-value pair array that defines the options for to-be-retrieved languages
     * @return array An array structured as [ ["code" => "lang1code", "name" => "Lang 1 Name], ["code" => "lang2code", "name" => "Lang 2 Name"], ... ]
     *               In case of error, returns an empty array.
     * @since 1.8.0
     */
    public function localizedLanguages($options = []) {
        // If the target locale does not exist, add WP's current locale as the target.
        $options += [
            "target" => Utils::getLocaleCode()
        ];

        try {
            $languages = $this->client->localizedLanguages($options);

        } catch(Exception $e) {
            $languages  = [];
            $error      = json_decode($e->getMessage(), true);
            $message    = sprintf("%s (%s - %s)",
                Utils::array_get($error, "error.message"),
                Utils::array_get($error, "error.status"),
                Utils::array_get($error, "error.code")
            );

            // Add as information message
            Informer::addError($message)->setException($e)->addAsLog();
        }

        return $languages;
    }

    public function getTestResultMessage(SettingsImpl $settings) {
        return sprintf(
            _wpcc('<b>From:</b> %1$s, <b>To:</b> %2$s, <b>Project ID:</b> %3$s, <b>API Key:</b> %4$s'),
            $settings->getSetting($this->settingKeyFrom),
            $settings->getSetting($this->settingKeyTo),
            $settings->getSetting($this->settingKeyProjectId),
            $settings->getSetting($this->settingKeyApiKey)
        );
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Initialize {@link $translator}.
     *
     * @param string $projectId Project ID retrieved for Google Cloud Translate API
     * @param string $key       The key for Google Cloud Translate API
     * @since 1.9.0
     */
    private function initTranslator($projectId, $key): void {
        // Create a translate client with the given credentials
        $this->client = new TranslateClient([
            'projectId' => $projectId,
            'key'       => $key,
        ]);
    }
}
