<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/10/2019
 * Time: 23:16
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Translation\Clients;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use WPCCrawler\Objects\Chunk\Enum\ChunkType;
use WPCCrawler\Objects\Chunk\TransformationChunker;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Translation\Clients\AwsTranslate\Credentials\Credentials;
use WPCCrawler\Objects\Transformation\Translation\Clients\AwsTranslate\Signature\SignatureV4;
use WPCCrawler\Objects\Transformation\Translation\TextTranslator;
use WPCCrawler\Utils;

class AmazonTranslateAPIClient extends AbstractTranslateAPIClient {

    /*

    Notes:
        API Reference:  https://docs.aws.amazon.com/translate/latest/dg/API_Reference.html
        Regions:        https://docs.aws.amazon.com/general/latest/gr/rande.html#translate_region
        Limits:         https://docs.aws.amazon.com/en_pv/translate/latest/dg/what-is-limits.html

        - Maximum text length per request is 5000 bytes. Not all ASCII characters are 1 byte. An ASCII character can be
          4 bytes: https://stackoverflow.com/a/4850316/2883487 Hence, to be on the safe side, limiting the maximum
          character length to 5000/4 = 1250 is OK. Otherwise, the texts should be chunked by their byte size, not
          character count.
        - Character encoding should be UTF-8
        - The requests sent to AWS must be signed. An example of how to create the signature:
          https://docs.aws.amazon.com/translate/latest/dg/examples-sigv4.html
     */

    /**
     * @var string Format that can be used to create a base URL for the translate API. This has only 1 variable which
     *             should be replaced by the code of a region that supports translate API.
     *             E.g. sprintf($hostFormat, "us-west-2")
     */
    private $hostFormat = 'https://translate.%s.amazonaws.com';

    /** @var string */
    private $accessKey;

    /** @var string */
    private $secret;

    /** @var string */
    private $region;

    /** @var string */
    private $from;

    /** @var string */
    private $to;

    /** @var null|Client */
    private $client;

    /** @var string */
    private $settingKeyFrom         = SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_FROM;
    /** @var string */
    private $settingKeyTo           = SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_TO;
    /** @var string */
    private $settingKeyAccessKeyId  = SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_ACCESS_KEY;
    /** @var string */
    private $settingKeySecretKey    = SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_SECRET;
    /** @var string */
    private $settingKeyRegion       = SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_REGION;

    /** @var string */
    private $keyAccessKey   = 'access_key';
    /** @var string */
    private $keySecret      = 'secret';
    /** @var string */
    private $keyRegion      = 'region';

    /** @var SignatureV4 */
    private $signatureV4;

    /** @var Credentials */
    private $credentials;

    /**
     * @param array $options An associative array that must have 'key' key.
     *                          'access_key':   The access key using which the requests will be authenticated.
     *                          'secret':       The secret using which the requests will be authenticated.
     *                          'region':       The AWS region to which the requests will be sent
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
        $this->accessKey    = $this->getOption($this->keyAccessKey) ?? '';
        $this->secret       = $this->getOption($this->keySecret)    ?? '';
        $this->region       = $this->getOption($this->keyRegion)    ?? '';

        $this->signatureV4    = new SignatureV4('translate', $this->region);
        $this->credentials    = new Credentials($this->accessKey, $this->secret);
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

        return $textTranslator->translate($this, [
            // If the language should be detected, AWS wants the source language to be set to "auto"
            'SourceLanguageCode' => $this->sanitizeFrom($this->from) ? $this->from : 'auto',
            'TargetLanguageCode' => $this->to,
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
        $accessKey  = $settings->getSetting($this->settingKeyAccessKeyId);
        $secret     = $settings->getSetting($this->settingKeySecretKey);
        $region     = $settings->getSetting($this->settingKeyRegion);

        if (!$accessKey || !$secret || !$region || !$this->isValidRegion($region)) {
            throw new Exception("You must provide a valid access key, a secret, and a region for Amazon Translate API to work properly.");
        }

        $this->from   = $settings->getSetting($this->settingKeyFrom);
        $this->to     = $settings->getSetting($this->settingKeyTo);

        $this->setOptions([
            $this->keyAccessKey => $accessKey,
            $this->keySecret    => $secret,
            $this->keyRegion    => $region,
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
        return new TransformationChunker(ChunkType::T_BYTES, $texts, 4800, 4800 * 100, 100);
    }

    /**
     * Translate an array of strings.
     *
     * @param array $texts   A flat sequential array of texts
     * @param array $options Translation options
     * @return array Translated texts in the same order as $texts. If an error occurs, returns an empty array.
     * @since 1.8.0
     */
    public function translateBatch(array $texts, $options = []) {
        // Created by following the instructions in:
        //  http://docs.guzzlephp.org/en/stable/quickstart.html#concurrent-requests
        $requests = function() use (&$texts, $options) {
            $defaultHeaders = [
                'Content-Type'  => 'application/x-amz-json-1.1',
                'X-Amz-Target'  => 'AWSShineFrontendService_20170701.TranslateText',
            ];

            // Create the endpoint URL using the selected region
            $uri = sprintf($this->hostFormat, $this->region);

            foreach($texts as $text) {
                // Add to-be-translated text to the options
                $options['Text'] = $text;

                // JSON-encode the options and add the encoded string as the request body
                $request        = new Request('POST', $uri, $defaultHeaders, json_encode($options) ?: null);
                $signedRequest  = $this->signatureV4->signRequest($request, $this->credentials);

                yield function() use (&$signedRequest, $options) {
                    return $this->getClient()->sendAsync($signedRequest, $options);
                };
            }
        };

        $translations = [];

        $pool = new Pool($this->getClient(), $requests(), [
            // Define how many requests can be sent in parallel
            'concurrency' => 25,

            // This is delivered for each successful response
            'fulfilled'   => function ($response, $index) use (&$translations, &$texts) {
                /** @var mixed|ResponseInterface $response */
                $translation = $this->onRequestFulfilled($response);
                $translations[$index] = $translation ?: $texts[$index];
            },

            // This is delivered for each failed request
            'rejected'    => function ($reason, $index) use (&$translations, &$texts) {
                /** @var mixed|ResponseInterface $reason */
                $this->onRequestRejected($reason);
                $translations[$index] = $texts[$index];
            },
        ]);

        // Initiate the transfers and create a promise
        $promise = $pool->promise();

        // Force the pool of requests to complete.
        $promise->wait();

        ksort($translations);
        return $translations;
    }

    /**
     * Get languages
     *
     * @param array $options A key-value pair array that defines the options for to-be-retrieved languages
     * @return array An array structured as [ ["code" => "lang1code", "name" => "Lang 1 Name], ["code" => "lang2code",
     *               "name" => "Lang 2 Name"], ... ] In case of error, returns an empty array.
     * @since 1.8.0
     */
    public function localizedLanguages($options = []) {
        // Amazon Translate does not have an endpoint to dynamically get the supported languages. Hence, we hard-code
        // them here, unfortunately.
        $langs = [
            "Afrikaans"             => "af",
            "Albanian"              => "sq",
            "Amharic"               => "am",
            "Arabic"                => "ar",
            "Armenian"              => "hy",
            "Azerbaijani"           => "az",
            "Bengali"               => "bn",
            "Bosnian"               => "bs",
            "Bulgarian"             => "bg",
            "Catalan"               => "ca",
            "Chinese (Simplified)"  => "zh",
            "Chinese (Traditional)" => "zh-TW",
            "Croatian"              => "hr",
            "Czech"                 => "cs",
            "Danish"                => "da",
            "Dari"                  => "fa-AF",
            "Dutch"                 => "nl",
            "English"               => "en",
            "Estonian"              => "et",
            "Farsi (Persian)"       => "fa",
            "Filipino, Tagalog"     => "tl",
            "Finnish"               => "fi",
            "French"                => "fr",
            "French (Canada)"       => "fr-CA",
            "Georgian"              => "ka",
            "German"                => "de",
            "Greek"                 => "el",
            "Gujarati"              => "gu",
            "Haitian Creole"        => "ht",
            "Hausa"                 => "ha",
            "Hebrew"                => "he",
            "Hindi"                 => "hi",
            "Hungarian"             => "hu",
            "Icelandic"             => "is",
            "Indonesian"            => "id",
            "Irish"                 => "ga",
            "Italian"               => "it",
            "Japanese"              => "ja",
            "Kannada"               => "kn",
            "Kazakh"                => "kk",
            "Korean"                => "ko",
            "Latvian"               => "lv",
            "Lithuanian"            => "lt",
            "Macedonian"            => "mk",
            "Malay"                 => "ms",
            "Malayalam"             => "ml",
            "Maltese"               => "mt",
            "Marathi"               => "mr",
            "Mongolian"             => "mn",
            "Norwegian"             => "no",
            "Pashto"                => "ps",
            "Polish"                => "pl",
            "Portuguese"            => "pt",
            "Portuguese (Portugal)" => "pt-PT",
            "Punjabi"               => "pa",
            "Romanian"              => "ro",
            "Russian"               => "ru",
            "Serbian"               => "sr",
            "Sinhala"               => "si",
            "Slovak"                => "sk",
            "Slovenian"             => "sl",
            "Somali"                => "so",
            "Spanish"               => "es",
            "Spanish (Mexico)"      => "es-MX",
            "Swahili"               => "sw",
            "Swedish"               => "sv",
            "Tamil"                 => "ta",
            "Telugu"                => "te",
            "Thai"                  => "th",
            "Turkish"               => "tr",
            "Ukrainian"             => "uk",
            "Urdu"                  => "ur",
            "Uzbek"                 => "uz",
            "Vietnamese"            => "vi",
            "Welsh"                 => "cy",
        ];

        $results = [];
        foreach($langs as $name => $code) {
            $results[] = [
                "name" => $name,
                "code" => $code
            ];
        }

        return $results;
    }

    public function getTestResultMessage(SettingsImpl $settings) {
        return sprintf(
            _wpcc('<b>From:</b> %1$s, <b>To:</b> %2$s, <b>Access Key:</b> %3$s, <b>Secret:</b> %4$s, <b>Region:</b> %5$s'),
            $settings->getSetting($this->settingKeyFrom),
            $settings->getSetting($this->settingKeyTo),
            $settings->getSetting($this->settingKeyAccessKeyId),
            $settings->getSetting($this->settingKeySecretKey),
            $settings->getSetting($this->settingKeyRegion)
        );
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * @return Client
     * @since 1.9.0
     */
    private function getClient() {
        if (!$this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * Handles a fulfilled request and returns the translation extracted from the response
     *
     * @param mixed|ResponseInterface $response
     * @return string|null If translation exists in the response, then the translation will be returned as a string.
     *                     Otherwise, null will be returned.
     * @since 1.9.0
     */
    private function onRequestFulfilled($response) {
        // Get the response text
        $responseText = $response->getBody()->getContents();

        // Parse it to JSON
        $responseJson = json_decode($responseText, true);

        // Make sure the response is parsed to JSON correctly.
        if (!$responseJson) {
            $message = _wpcc('The response retrieved from Amazon Translate API could not be parsed to JSON. Message: %1$s');
            $info = new Information($message, json_last_error_msg(), InformationType::ERROR);
            Informer::add($info->addAsLog());
            return null;
        }

        $translation = Utils::array_get($responseJson, 'TranslatedText');

        if (!$translation) {
            Informer::addError(_wpcc('Translation does not exist in the response retrieved from Amazon Translate API'))
                ->addAsLog();
            return null;
        }

        return $translation;
    }

    /**
     * Handles a rejected request
     *
     * @param mixed|ResponseInterface $reason
     * @since 1.9.0
     */
    private function onRequestRejected($reason): void {
        // We add $reason as a detail message in case that the reason is an instance of an unknown class. For an unknown
        // class, the string representation of the instance will be used as a detail message.
        $info = new Information(
            _wpcc('Translation request was not successful'),
            (string) $reason,
            InformationType::ERROR
        );

        // Handle the known classes
        if ($reason instanceof TransferException) {
            $info
                ->setException($reason)
                ->setDetails($reason->getMessage());
        }

        Informer::add($info->addAsLog());
    }

    /**
     * Check if a region is valid
     *
     * @param string $region Name of an AWS region available for Amazon Translate API
     * @return bool True if the region is valid. Otherwise, false.
     * @since 1.9.0
     */
    private function isValidRegion(string $region) {
        return array_key_exists($region, static::getRegions());
    }

    /*
     * PUBLIC STATIC METHODS
     */

    /**
     * Get AWS regions that can be used to access the translate API
     *
     * @return array Key-value pairs where keys are region codes and the values are names of the regions for humans
     * @link https://docs.aws.amazon.com/general/latest/gr/rande.html#translate_region
     * @since 1.9.0
     */
    public static function getRegions() {
        return [
            "us-east-2"      => "US East (Ohio)",
            "us-east-1"      => "US East (N. Virginia)",
            "us-west-1"      => "US West (N. California)",
            "us-west-2"      => "US West (Oregon)",
            "af-south-1"     => "Africa (Cape Town)",
            "ap-east-1"      => "Asia Pacific (Hong Kong)",
            "ap-southeast-3" => "Asia Pacific (Jakarta)",
            "ap-south-1"     => "Asia Pacific (Mumbai)",
            "ap-northeast-3" => "Asia Pacific (Osaka)",
            "ap-northeast-2" => "Asia Pacific (Seoul)",
            "ap-southeast-1" => "Asia Pacific (Singapore)",
            "ap-southeast-2" => "Asia Pacific (Sydney)",
            "ap-northeast-1" => "Asia Pacific (Tokyo)",
            "ca-central-1"   => "Canada (Central)",
            "cn-north-1"     => "China (Beijing)",
            "cn-northwest-1" => "China (Ningxia)",
            "eu-central-1"   => "Europe (Frankfurt)",
            "eu-west-1"      => "Europe (Ireland)",
            "eu-west-2"      => "Europe (London)",
            "eu-south-1"     => "Europe (Milan)",
            "eu-west-3"      => "Europe (Paris)",
            "eu-north-1"     => "Europe (Stockholm)",
            "me-south-1"     => "Middle East (Bahrain)",
            "sa-east-1"      => "South America (São Paulo)",
            "us-gov-west-1"  => "AWS GovCloud (US-West)",
        ];
    }
}
