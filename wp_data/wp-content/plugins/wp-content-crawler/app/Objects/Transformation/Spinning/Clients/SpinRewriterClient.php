<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 23/02/2019
 * Time: 23:12
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Spinning\Clients;


use Exception;
use Illuminate\Support\Str;
use WPCCrawler\Objects\Chunk\Enum\ChunkType;
use WPCCrawler\Objects\Chunk\TransformationChunker;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Spinning\TextSpinner;
use WPCCrawler\Utils;

class SpinRewriterClient extends AbstractSpinningAPIClient {

    /*

        Notes:
            . Maximum number of words that can be sent in a single request is 4000. This is not written anywhere in the
              API documentation.
            . There must be 7 seconds between two API requests. From their website:
                "You can only submit entirely new text for analysis once every 7 seconds."
     */

    /** @var int The time that Spin Rewriter requires to send a response */
    private const REQUIRED_TIMEOUT_DURATION_IN_SECONDS = 150;

    /** @var int  */
    private const REQUIRED_DURATION_BETWEEN_TWO_REQUESTS_IN_SECONDS = 7;

    private const API_URL = 'http://www.spinrewriter.com/action/api';

    private const AFFILIATE_PARAM = 'ref=29b13';

    /*
     * API KEYS
     */

    /** @var string */
    private $keyAction              = 'action';
    /** @var string */
    private $keyText                = 'text';

    /** @var string */
    private $keyApiEmail            = 'email_address';
    /** @var string */
    private $keyApiKey              = 'api_key';

    /** @var string */
    private $keyProtectedTerms      = 'protected_terms';            // New line-separated string
    /** @var string */
    private $keyConfidenceLevel     = 'confidence_level';           // "low", "medium" or "high"
    /** @var string */
    private $keyAutoProtectedTerms  = 'auto_protected_terms';       // "true" or "false"
    /** @var string */
    private $keyNestedSpintax       = 'nested_spintax';             // "true" or "false"
    /** @var string */
    private $keyAutoSentences       = 'auto_sentences';             // "true" or "false"
    /** @var string */
    private $keyAutoParagraphs      = 'auto_paragraphs';            // "true" or "false"
    /** @var string */
    private $keyAutoNewParagraphs   = 'auto_new_paragraphs';        // "true" or "false"
    /** @var string */
    private $keyAutoSentenceTrees   = 'auto_sentence_trees';        // "true" or "false"
    /** @var string */
    private $keyUseOnlySynonyms     = 'use_only_synonyms';          // "true" or "false"
    /** @var string */
    private $keyReorderParagraphs   = 'reorder_paragraphs';         // "true" or "false"

    /*
     * CUSTOM KEYS
     */

    /** @var string */
    private $keyTextWithSpintax = 'text_with_spintax';          // true or false. This is not sent to the API, used internally.

    /*
     * SETTING KEYS
     */

    /** @var string */
    private $settingKeyApiEmail             = SettingKey::WPCC_SPINNING_SPIN_REWRITER_EMAIL;
    /** @var string */
    private $settingKeyApiKey               = SettingKey::WPCC_SPINNING_SPIN_REWRITER_API_KEY;
    /** @var string */
    private $settingKeyConfidenceLevel      = SettingKey::WPCC_SPINNING_SPIN_REWRITER_CONFIDENCE_LEVEL;
    /** @var string */
    private $settingKeyAutoProtectedTerms   = SettingKey::WPCC_SPINNING_SPIN_REWRITER_AUTO_PROTECTED_TERMS;
    /** @var string */
    private $settingKeyNestedSpintax        = SettingKey::WPCC_SPINNING_SPIN_REWRITER_NESTED_SPINTAX;
    /** @var string */
    private $settingKeyAutoSentences        = SettingKey::WPCC_SPINNING_SPIN_REWRITER_AUTO_SENTENCES;
    /** @var string */
    private $settingKeyAutoParagraphs       = SettingKey::WPCC_SPINNING_SPIN_REWRITER_AUTO_PARAGRAPHS;
    /** @var string */
    private $settingKeyAutoNewParagraphs    = SettingKey::WPCC_SPINNING_SPIN_REWRITER_AUTO_NEW_PARAGRAPHS;
    /** @var string */
    private $settingKeyAutoSentenceTrees    = SettingKey::WPCC_SPINNING_SPIN_REWRITER_AUTO_SENTENCE_TREES;
    /** @var string */
    private $settingKeyUseOnlySynonyms      = SettingKey::WPCC_SPINNING_SPIN_REWRITER_USE_ONLY_SYNONYMS;
    /** @var string */
    private $settingKeyReorderParagraphs    = SettingKey::WPCC_SPINNING_SPIN_REWRITER_REORDER_PARAGRAPHS;
    /** @var string */
    private $settingKeyTextWithSpintax      = SettingKey::WPCC_SPINNING_SPIN_REWRITER_TEXT_WITH_SPINTAX;

    /*
     * ACTIONS
     */

    /** @var string */
    private $actionUniqueVariation  = 'unique_variation';
    /** @var string */
    private $actionTextWithSpintax  = 'text_with_spintax';
    /** @var string */
    private $actionApiQuota         = 'api_quota';

    /**
     * Initialize the API client using the already-set options (See {@link getOption()})
     *
     * @return void
     * @since 1.9.0
     */
    public function init() {

    }

    /**
     * @param SettingsImpl $settings See {@link setOptionsUsingSettings()}
     * @return void
     * @since 1.9.0
     */
    protected function doSetOptionsUsingSettings(SettingsImpl $settings): void {
        $this->setOptions([
            $this->keyApiEmail           => $settings->getSetting($this->settingKeyApiEmail, null),
            $this->keyApiKey             => $settings->getSetting($this->settingKeyApiKey, null),
            $this->keyConfidenceLevel    => $this->getConfidenceLevelFromSettings($settings),
            $this->keyAutoProtectedTerms => $this->getCheckboxValueFromSettings($settings, $this->settingKeyAutoProtectedTerms),
            $this->keyNestedSpintax      => $this->getCheckboxValueFromSettings($settings, $this->settingKeyNestedSpintax),
            $this->keyAutoSentences      => $this->getCheckboxValueFromSettings($settings, $this->settingKeyAutoSentences),
            $this->keyAutoParagraphs     => $this->getCheckboxValueFromSettings($settings, $this->settingKeyAutoParagraphs),
            $this->keyAutoNewParagraphs  => $this->getCheckboxValueFromSettings($settings, $this->settingKeyAutoNewParagraphs),
            $this->keyAutoSentenceTrees  => $this->getCheckboxValueFromSettings($settings, $this->settingKeyAutoSentenceTrees),
            $this->keyUseOnlySynonyms    => $this->getCheckboxValueFromSettings($settings, $this->settingKeyUseOnlySynonyms),
            $this->keyReorderParagraphs  => $this->getCheckboxValueFromSettings($settings, $this->settingKeyReorderParagraphs),
            $this->keyTextWithSpintax    => $this->getCheckboxValueFromSettings($settings, $this->settingKeyTextWithSpintax),
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
        // To combine the texts, each text is wrapped in an HTML tag having an ID attribute. So, the size of the texts
        // will increase in case of combining. The length constraints defined in the chunker should consider this. The
        // limits must be assigned considering that an HTML tag with an ID attribute will be added to each text.

        $maxLength              = 3800;
        $maxItemCountPerChunk   = 50;
        $maxLengthPerChunk      = $maxLength;

        return new TransformationChunker(ChunkType::T_WORDS, $texts, $maxLength, $maxLengthPerChunk, $maxItemCountPerChunk);
    }

    /**
     * Spin texts using the settings. This method might override the options given in the constructor.
     *
     * @param TextSpinner  $textSpinner
     * @return array See {@link TextSpinner::spin()}
     * @uses  TextSpinner::spin()
     * @since 1.9.0
     */
    public function spin(TextSpinner $textSpinner) {
        return $textSpinner->spin($this, $this->getOptions());
    }

    /**
     * Spin an array of strings.
     *
     * @param array $texts   A flat sequential array of texts
     * @param array $options Spinning options
     * @return array Spun texts in the same order as $texts. If an error occurs, returns an empty array.
     * @throws Exception See {@link getUniqueVariation()}
     * @since 1.8.0
     */
    public function spinBatch(array $texts, $options = []) {
        // Check if the spintax should be returned
        $textWithSpintax = Utils::array_get($options, $this->keyTextWithSpintax, false) === "true";

        // Remove the spintax key. We use this internally to decide the request type. We do not send it to the API.
        unset($options[$this->keyTextWithSpintax]);

        $results = [];
        $total = count($texts);
        $current = 0;
        foreach($texts as $text) {
            ++$current;
            $currentTime = time();

            // Get the result accordingly
            $result = $textWithSpintax ? $this->getTextWithSpintax($text, $options) : $this->getUniqueVariation($text, $options);
            $results[] = $result !== null ? $result : $text; // If an error occurs, add the original text as result.

            // If there will be another request, wait for the duration mandated by the API.
            if ($current !== $total) {
                $secondsPassed = time() - $currentTime;
                sleep((int) max(0, static::REQUIRED_DURATION_BETWEEN_TWO_REQUESTS_IN_SECONDS - $secondsPassed));
            }
        }

        return $results;
    }

    /**
     * Get usage statistics of the API.
     *
     * @return array|null An associative array where keys are the names of statistics and the values are the values of
     *                    the statistics
     * @throws Exception See {@link request()}
     * @since 1.9.0
     */
    public function getUsageStatistics() {
        $result = $this->request($this->actionApiQuota, $this->getOptions());
        if (!$result) return null;

        // Get the stats from the result
        $quotaDescription   = Utils::array_get($result, 'response', null);
        $requestsMade       = Utils::array_get($result, 'api_requests_made', null);
        $requestsAvailable  = Utils::array_get($result, 'api_requests_available', null);

        // Prepare the stats
        $stats = [
            _wpcc('Quota description')  => $quotaDescription,
            _wpcc('Requests made')      => $requestsMade,
            _wpcc('Requests available') => $requestsAvailable
        ];

        // Remove invalid-valued items
        foreach($stats as $k => $v) {
            if (!$v) unset($stats[$k]);
        }

        return $stats;
    }

    public function getTestResultMessage(SettingsImpl $settings) {
        return sprintf(
            _wpcc('<b>Email:</b> %1$s, <b>API Key:</b> %2$s'),
            $settings->getSetting($this->settingKeyApiEmail),
            $settings->getSetting($this->settingKeyApiKey)
        );
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Get spun text in spintax format
     *
     * @param string $text    Text to be spun
     * @param array  $options API request parameters
     * @return null|string
     * @throws Exception See {@link request()}
     * @since 1.9.0
     */
    private function getTextWithSpintax($text, $options = []) {
        return $this->makeSpinRequest($this->actionTextWithSpintax, $text, $options);
    }

    /**
     * Get spun text in a human readable format
     *
     * @param string $text    Text to be spun
     * @param array  $options API request parameters
     * @return null|string
     * @throws Exception See {@link request()}
     * @since 1.9.0
     */
    private function getUniqueVariation($text, $options = []) {
        return $this->makeSpinRequest($this->actionUniqueVariation, $text, $options);
    }

    /**
     * Make a spin request to the API and get the result
     *
     * @param string $action  API action, e.g. {@link $actionUniqueVariation}
     * @param string $text    Text to be spun
     * @param array  $options API request parameters
     * @return null|string
     * @throws Exception See <a href='psi_element://request()'>request()</a>
     * @since 1.9.0
     */
    private function makeSpinRequest($action, $text, $options = []) {
        $options[$this->keyText] = $text;
        $result = $this->request($action, $options);

        // If there is no result, return null.
        if (!$result) return null;

        // Get the response
        $response = Utils::array_get($result, 'response', null);
        if (!$response) return null;

        // Return the response, which is the spun text.
        return $response;
    }

    /**
     * Make an API request
     *
     * @param string $action API action
     * @param array  $params Key-value pair. The parameters to be sent to the API.
     * @return null|array If the request was successful, array. Otherwise, null.
     * @throws Exception When an error is returned in case of failed request. See also {@link prepareParams()}
     * @since 1.9.0
     */
    private function request($action, $params) {
        $apiUrl = static::API_URL;

        // Add action
        $params[$this->keyAction] = $action;

        $params = $this->prepareParams($params);

        // Prepare the query string with parameters
        $queryString = http_build_query($params);

        // Spin Rewriter requires some time to respond
        set_time_limit(static::REQUIRED_TIMEOUT_DURATION_IN_SECONDS);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);

        // See: http://www.php.net/manual/en/function.curl-setopt.php
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);                                        // Do not limit the connection time by setting it to 0
        curl_setopt($ch, CURLOPT_TIMEOUT, static::REQUIRED_TIMEOUT_DURATION_IN_SECONDS);    // Limit execution time of all cURL functions

        $execResult = curl_exec($ch);
        $result = is_string($execResult) ? trim($execResult) : null;
        curl_close($ch);

        // If there is no result, return null.
        if (!$result) return null;

        $parsedResult = json_decode($result, true);
        if (!$parsedResult) return null;

        $success = strtolower(Utils::array_get($parsedResult, 'status', '')) === "ok";

        // If the request was not successful
        if (!$success) {
            $response = Utils::array_get($parsedResult, 'response', null);

            // If there is an output, it contains the error message. Throw an exception with the error message.
            if ($response) throw new Exception(_wpcc("API Response") . ': ' . $response);

            // Otherwise, return null.
            return null;
        }

        return $parsedResult;
    }

    /**
     * Validates the parameters that will be sent with the API request.
     *
     * @param array $params Parameters that will be sent to the API
     * @return array
     * @throws Exception When required parameters do not exist
     * @since 1.9.0
     */
    private function prepareParams($params) {
        $validated = [];
        foreach($params as $key => $value) {
            if ($value === '' || $value === null) continue;

            $trimmed = trim($value);
            if (strlen($trimmed) === 0) continue;

            if ($value === true)  $value = "true";
            if ($value === false) $value = "false";

            $validated[$key] = $value;
        }

        // Get required params and check if they all exist
        $email  = Utils::array_get($validated, $this->keyApiEmail, null);
        $apiKey = Utils::array_get($validated, $this->keyApiKey, null);
        $action = Utils::array_get($validated, $this->keyAction, null);

        if ($action === null) {
            throw new Exception(_wpcc("API action must be provided."));
        }

        if ($email === null || $apiKey === null) {
            // Throw an exception if a required parameter does not exist.
            throw new Exception(_wpcc("You must provide email and API key"));
        }

        // If there are protected strings, add them as protected terms
        if ($this->getProtectedStrings()) {
            // The API requires the terms to be provided as a new line-separated string.
            $validated[$this->keyProtectedTerms] = implode("\n", $this->getProtectedStrings());
        }

        return $validated;
    }

    /**
     * Get confidence level from the settings
     *
     * @param SettingsImpl $settings
     * @return string "low", "medium" or "high"
     * @since 1.9.0
     */
    private function getConfidenceLevelFromSettings(SettingsImpl $settings) {
        $value = $settings->getSetting($this->settingKeyConfidenceLevel, null);

        $default = "medium";
        if (!$value) return $default;

        $value = strtolower($value);
        $candidates = ['low', 'medium', 'high'];
        return in_array($value, $candidates) ? $value : $default;
    }

    /**
     * Get boolean value as a string.
     *
     * @param SettingsImpl $settings
     * @param string $key
     * @return string "true" or "false"
     * @since 1.9.0
     */
    private function getCheckboxValueFromSettings(SettingsImpl $settings, $key) {
        return $settings->getSettingForCheckbox($key) ? "true" : "false";
    }

    /*
     * STATIC METHODS
     */

    /**
     * Get confidence level options to be shown in a select HTML element
     *
     * @return array Key-value pair where keys are option values and values are option names.
     * @since 1.9.0
     */
    public static function getConfidenceLevelsForSelect() {
        return [
            'low'    => _wpcc('Low'),
            'medium' => _wpcc('Medium'),
            'high'   => _wpcc('High'),
        ];
    }

    /**
     * Get affiliate URL link for a URL from spinrewriter.com
     *
     * @param string $url A URL from spinrewriter.com
     * @return string Affiliate URL
     * @since 1.9.0
     */
    public static function getAffiliateLink($url) {
        return $url . (Str::contains($url, '?') ? '&' : '?') . static::AFFILIATE_PARAM;
    }
}
