<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/10/2019
 * Time: 11:20
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Translation\Clients;


use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use WPCCrawler\Objects\Chunk\Enum\ChunkType;
use WPCCrawler\Objects\Chunk\TransformationChunker;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Translation\TextTranslator;
use WPCCrawler\Utils;

class YandexTranslateAPIClient extends AbstractTranslateAPIClient {

    /*
        YANDEX TRANSLATOR SETTINGS

        Some notes:
            * Limits can be found here: https://tech.yandex.com/translate/doc/dg/reference/translate-docpage/
            * For POST requests, the maximum size of the text being passed is 10,000 characters.
     */

    /** @var string Yandex Translate API's main URL */
    private $host = 'https://translate.yandex.net/api/v1.5/tr.json/';

    /** @var string Endpoint to which the translation request should be sent */
    private $endpointTranslate = 'translate';

    /** @var string Endpoint to which a request to get the list of supported languages should be sent */
    private $endpointGetLanguages = 'getLangs';

    /** @var string */
    private $keyApiKey = 'key';

    /** @var string */
    private $settingKeyFrom     = SettingKey::WPCC_TRANSLATION_YANDEX_TRANSLATE_FROM;
    /** @var string */
    private $settingKeyTo       = SettingKey::WPCC_TRANSLATION_YANDEX_TRANSLATE_TO;
    /** @var string */
    private $settingKeyApiKey   = SettingKey::WPCC_TRANSLATION_YANDEX_TRANSLATE_API_KEY;

    /** @var string Stores the API key using which the requests will be authenticated. */
    private $apiKey;

    /** @var string From language */
    private $from;

    /** @var string To language */
    private $to;

    /** @var Client|null */
    private $client;

    /**
     * @param array $options An associative array that must have 'key' key.
     *                        'key': The API key using which the requests will be authenticated.
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
        $this->apiKey = $this->getOption($this->keyApiKey);
    }

    /**
     * Translate texts using the settings. This method might override the options given in the constructor.
     *
     * @param TextTranslator $textTranslator
     * @return array See {@link TextTranslator::translate()}
     * @throws Exception When the settings do not have the required options.
     * @since 1.9.0
     * @uses  TextTranslator::translate()
     */
    public function translate(TextTranslator $textTranslator) {
        if (!$this->from || !$this->to) {
            throw new Exception("From and to languages must be set.");
        }

        // Create the language option's value in the requested format. It can be either:
        //  . <source-lang>-<target-lang>
        //  . <target-lang>
        // If source language does not exist, it means the language should be detected.
        $langOption = implode('-', array_filter([
            $this->sanitizeFrom($this->from),
            $this->to
        ]));

        return $textTranslator->translate($this, [
            'lang' => $langOption
        ]);
    }

    /**
     * Set the options of this API client using given settings.
     *
     * @param SettingsImpl $settings
     * @return void
     * @throws Exception When the settings do not have the required options.
     * @since 1.9.0
     */
    public function setOptionsUsingSettings(SettingsImpl $settings) {
        $apiKey = $settings->getSetting($this->settingKeyApiKey);

        if(!$apiKey) {
            throw new Exception("You must provide a valid API key for Yandex Translate API to work properly.");
        }

        $this->from   = $settings->getSetting($this->settingKeyFrom);
        $this->to     = $settings->getSetting($this->settingKeyTo);

        $this->setOptions([
            $this->keyApiKey => $apiKey
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
        return new TransformationChunker(ChunkType::T_CHARS, $texts, 9750, 9750, 100);
    }

    /**
     * Translate an array of strings.
     *
     * @param array $texts   A flat sequential array of texts
     * @param array $options Translation options. 'lang' must exist and in the format of '<source-lang>-<target-lang>'.
     *                       For example, 'en-tr' means translate from English to Turkish. If the language must be
     *                       detected, the format should be '<target-lang>'. For example, 'tr' means detect the language
     *                       and translate it to Turkish.
     * @return array Translated texts in the same order as $texts. If an error occurs, returns an empty array.
     * @throws Exception If the response cannot be parsed or the translations cannot be retrieved from the response
     * @since 1.9.0
     */
    public function translateBatch(array $texts, $options = []) {
        if (!$texts) return $texts;

        // If a text type is not provided, set it as 'html'. Otherwise, HTMLs get distorted horrifically.
        $options += [
            'format' => 'html'
        ];

        // Prepare the texts such that it can be directly appended to the request URL
        $queryTextPart = implode("&", array_map(function($text) {
            return "text=" . urlencode($text);
        }, $texts));

        // Create the query string using the options
        $queryStr = http_build_query([
                'key' => $this->apiKey
            ] + $options);

        // Append the texts to the query
        $queryStr .= "&" . $queryTextPart;

        // Send a translation request to the API
        try {
            $response = $this->getClient()->post($this->endpointTranslate, [
                'query' => $queryStr
            ]);

        } catch (TransferException $e) {
            Informer::add((new Information(get_class($e), trim($e->getMessage()), InformationType::ERROR))
                ->setException($e)
                ->addAsLog());

            return [];
        }

        // Get the response text
        $responseText = $response->getBody()->getContents();

        // Parse it to JSON
        $responseJson = json_decode($responseText, true);

        // Make sure the response is parsed to JSON correctly.
        if (!$responseJson) {
            throw new Exception(sprintf(
                _wpcc('The response retrieved from Yandex Translate API could not be parsed to JSON. Message: %1$s'),
                json_last_error_msg()
            ));
        }

        // Get the translations
        $translations = Utils::array_get($responseJson, 'text');

        if (!$translations) {
            throw new Exception(_wpcc('Translations do not exist in the response retrieved from Yandex Translate API'));
        }

        return $translations;
    }

    /**
     * Get languages
     *
     * @param array $options A key-value pair array that defines the options for to-be-retrieved languages
     * @return array An array structured as [ ["code" => "lang1code", "name" => "Lang 1 Name], ["code" => "lang2code",
     *                       "name" => "Lang 2 Name"], ... ] In case of error, returns an empty array.
     * @throws Exception If response could not be parsed to JSON or it does not have required values.
     * @since 1.9.0
     */
    public function localizedLanguages($options = []) {
        try {
            $response = $this->getClient()->post($this->endpointGetLanguages, [
                'query' => [
                    'key' => $this->apiKey,
                    'ui'  => Utils::getLocaleCode()
                ]
            ]);

        } catch (TransferException $e) {
            Informer::addError(trim($e->getMessage()))->setException($e)->addAsLog();
            return [];
        }

        // Get the response text
        $responseText = $response->getBody()->getContents();

        // Parse it to JSON
        $responseJson = json_decode($responseText, true);

        // Make sure the response is parsed to JSON correctly.
        if (!$responseJson) {
            throw new Exception(sprintf(
                _wpcc('Languages of Yandex Translate API could not be retrieved. Message: %1$s'),
                json_last_error_msg()
            ));
        }

        // Get the languages
        $languages = Utils::array_get($responseJson, "langs");

        if (!$languages) {
            throw new Exception(_wpcc('Languages do not exist in the response retrieved from Yandex Translate API'));
        }

        // Prepare the languages in the required format
        $result = [];
        foreach($languages as $code => $name) {
            $result[] = [
                "code" => $code,
                "name" => $name
            ];
        }

        return $result;
    }

    public function getTestResultMessage(SettingsImpl $settings) {
        return sprintf(
            _wpcc('<b>From:</b> %1$s, <b>To:</b> %2$s, <b>API Key:</b> %3$s'),
            $settings->getSetting($this->settingKeyFrom),
            $settings->getSetting($this->settingKeyTo),
            $settings->getSetting($this->settingKeyApiKey)
        );
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * @return Client
     * @since 1.9.0
     */
    public function getClient() {
        if (!$this->client) {
            $this->client = new Client([
                'base_uri' => $this->host,
                'headers'  => [
                    'Accept'       => '*/*',
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]);
        }

        return $this->client;
    }
}