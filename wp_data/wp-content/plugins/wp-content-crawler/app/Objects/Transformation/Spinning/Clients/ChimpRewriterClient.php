<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 21/02/2019
 * Time: 16:32
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Spinning\Clients;


use Exception;
use WPCCrawler\Objects\Chunk\Enum\ChunkType;
use WPCCrawler\Objects\Chunk\TransformationChunker;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Spinning\TextSpinner;
use WPCCrawler\Utils;

class ChimpRewriterClient extends AbstractSpinningAPIClient {

    /** @var string */
    private $apiUrl = 'http://api.chimprewriter.com/';
    /** @var int */
    private $maxAppIdLength = 100; // This is mandated by the API.

    /*
     * API KEYS
     */

    // Required params
    /** @var string */
    private $keyAppEmail            = 'email';
    /** @var string */
    private $keyApiKey              = 'apiKey';
    /** @var string */
    private $keyAppId               = 'aid';
    /** @var string */
    private $keyText                = 'text';

    // Normal params
    /** @var string */
    private $keyQuality             = 'quality';                    // int      1, 2, 3, 4 or 5. Default: 4
    /** @var string */
    private $keyPhraseQuality       = 'phrasequality';              // int      1, 2, 3, 4 or 5: Default: 3
    /** @var string */
    private $keyPosMatch            = 'posmatch';                   // int      3, 2, 1 or 0. Default: 3
    /** @var string */
    private $keyRewrite             = 'rewrite';                    // bool     If 1, no spintax, returns a human-readable text. Default: 1
    /** @var string */
    private $keyLanguage            = 'language';                   // string   Two letter language code for the desired language. Default: en

    // Advanced params
    /** @var string */
    private $keySentenceRewrite     = 'sentencerewrite';            // bool     Set to 1 to use artificial intelligence tools to automatically rewrite sentences: Default: 0
    /** @var string */
    private $keyGrammarCheck        = 'grammarcheck';               // bool     Set to 1 to verify grammar on the result article for very high quality spin. Default: 0

    // Extra params
    /** @var string */
    private $keyReorderParagraphs   = 'reorderparagraphs';          // bool     Set to 1 to verify grammar on the result article for very high quality spin. Default: 0
    /** @var string */
    private $keyReplacePhrases      = 'replacephraseswithphrases';  // bool     Always replace phrases with equivalent phrases, regardless of quality. Default: 0
    /** @var string */
    private $keySpinWithinSpin      = 'spinwithinspin';             // bool     If set to 1, if there is existing spin syntax in the content you send up, the API will spin any relevant content inside this syntax. Default: 0
    /** @var string */
    private $keySpinTidy            = 'spintidy';                   // bool     Runs a spin tidy pass over the result article. Default: 1
    /** @var string */
    private $keyExcludeOriginal     = 'excludeoriginal';            // bool     Excludes the original word form the result article. Used for maximum uniqueness from the original article. Default: 0
    /** @var string */
    private $keyReplaceFrequency    = 'replacefrequency';           // int      1, 2, 3, ... Default: 1
    /** @var string */
    private $keyMaxSynonyms         = 'maxsyns';                    // int      The maximum number of synonyms to be used for any one word or phrase. Default: 10
    /** @var string */
    private $keyInstantUnique       = 'instantunique';              // int      0, 1 or 2. Default: 0
    /** @var string */
    private $keyMaxSpinDepth        = 'maxspindepth';               // int      Define a maximum spin level depth in returned article. Default: 0

    /** @var string */
    private $keyProtectedTerms      = 'protectedterms';             // string   Comma separated list of words or phrases to protect from spin: Default: ''

    /*
     * SETTING KEYS
     */

    /** @var string */
    private $settingKeyAppEmail          = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_EMAIL;
    /** @var string */
    private $settingKeyApiKey            = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_API_KEY;
    /** @var string */
    private $settingKeyAppId             = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_APP_ID;

    /** @var string */
    private $settingKeyQuality           = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_QUALITY;
    /** @var string */
    private $settingKeyPhraseQuality     = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_PHRASE_QUALITY;
    /** @var string */
    private $settingKeyPosMatch          = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_POS_MATCH;
    /** @var string */
    private $settingKeyDoNotRewrite      = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_DO_NOT_REWRITE;
    /** @var string */
    private $settingKeyLanguage          = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_LANGUAGE;
    /** @var string */
    private $settingKeySentenceRewrite   = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_SENTENCE_REWRITE;
    /** @var string */
    private $settingKeyGrammarCheck      = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_GRAMMAR_CHECK;
    /** @var string */
    private $settingKeyReorderParagraphs = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_REORDER_PARAGRAPHS;
    /** @var string */
    private $settingKeyReplacePhrases    = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_REPLACE_PHRASES_WITH_PHRASES;
    /** @var string */
    private $settingKeySpinWithinSpin    = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_SPIN_WITHIN_SPIN;
    /** @var string */
    private $settingKeySpinTidy          = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_SPIN_TIDY;
    /** @var string */
    private $settingKeyExcludeOriginal   = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_EXCLUDE_ORIGINAL;
    /** @var string */
    private $settingKeyReplaceFrequency  = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_REPLACE_FREQUENCY;
    /** @var string */
    private $settingKeyMaxSynonyms       = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_MAX_SYNS;
    /** @var string */
    private $settingKeyInstantUnique     = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_INSTANT_UNIQUE;
    /** @var string */
    private $settingKeyMaxSpinDepth      = ''; // SettingKey::WPCC_SPINNING_CHIMP_REWRITER_MAX_SPIN_DEPTH;

    /*
     * COMMANDS
     */

    /** @var string */
    private $cmdChimpRewrite    = 'ChimpRewrite';
    /** @var string */
    private $cmdStatistics      = 'Statistics';

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
    protected function doSetOptionsUsingSettings(SettingsImpl $settings) {
        $appId = $settings->getSetting($this->settingKeyAppId, null);

        // App ID must be at max 100 chars. Make it at max 100 chars.
        if ($appId && mb_strlen($appId) > $this->maxAppIdLength) {
            $appId = mb_substr($appId, 0, $this->maxAppIdLength);
        }

        $this->setOptions([
            $this->keyAppId             => $appId,
            $this->keyAppEmail          => $settings->getSetting($this->settingKeyAppEmail, null),
            $this->keyApiKey            => $settings->getSetting($this->settingKeyApiKey, null),

            $this->keyQuality           => $settings->getSetting($this->settingKeyQuality, null),
            $this->keyPhraseQuality     => $settings->getSetting($this->settingKeyPhraseQuality, null),
            $this->keyPosMatch          => $settings->getSetting($this->settingKeyPosMatch, null),
            $this->keyRewrite           => !$settings->getSettingForCheckbox($this->settingKeyDoNotRewrite),
            $this->keyLanguage          => $settings->getSetting($this->settingKeyLanguage, null),
            $this->keySentenceRewrite   => $settings->getSettingForCheckbox($this->settingKeySentenceRewrite),
            $this->keyGrammarCheck      => $settings->getSettingForCheckbox($this->settingKeyGrammarCheck),
            $this->keyReorderParagraphs => $settings->getSettingForCheckbox($this->settingKeyReorderParagraphs),
            $this->keyReplacePhrases    => $settings->getSettingForCheckbox($this->settingKeyReplacePhrases),
            $this->keySpinWithinSpin    => $settings->getSettingForCheckbox($this->settingKeySpinWithinSpin),
            $this->keySpinTidy          => $settings->getSettingForCheckbox($this->settingKeySpinTidy),
            $this->keyExcludeOriginal   => $settings->getSettingForCheckbox($this->settingKeyExcludeOriginal),
            $this->keyReplaceFrequency  => $settings->getSetting($this->settingKeyReplaceFrequency, null),
            $this->keyMaxSynonyms       => $settings->getSetting($this->settingKeyMaxSynonyms, null),
            $this->keyInstantUnique     => $settings->getSetting($this->settingKeyInstantUnique, null),
            $this->keyMaxSpinDepth      => $settings->getSetting($this->settingKeyMaxSpinDepth, null),
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
        return new TransformationChunker(ChunkType::T_WORDS, $texts, 3800, 3800, 1);
    }

    /**
     * Spin texts using the settings. This method might override the options given in the constructor.
     *
     * @param TextSpinner  $textSpinner
     * @return array See {@link TextSpinner::spin()}
     * @uses  TextSpinner::spin()
     * @since 1.9.0
     * @throws Exception When the settings do not have the required options.
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
     * @since 1.8.0
     * @throws Exception See {@link chimpRewrite()}
     */
    public function spinBatch(array $texts, $options = []) {
        $results = [];
        foreach($texts as $text) {
            $result = $this->chimpRewrite($text, $options);
            $results[] = $result !== null ? $result : $text; // If an error occurs, add the original text as result.
        }

        return $results;
    }

    /**
     * Get usage statistics of the API.
     *
     * @return array|null An associative array where keys are the names of statistics and the values are the values of
     *                    the statistics
     * @since 1.9.0
     * @throws Exception See {@link request()}
     */
    public function getUsageStatistics() {
        // The usage statistics API method requires the parameters below. So, no need to send all available parameters
        // existing in the options. Extract these from the options and send them only.
        $requiredParams = [$this->keyAppId, $this->keyApiKey, $this->keyAppEmail];

        $params = [];
        foreach($requiredParams as $key) $params[$key] = $this->getOption($key);

        return $this->request($this->cmdStatistics, $params);
    }

    public function getTestResultMessage(SettingsImpl $settings) {
        return sprintf(
            _wpcc('<b>Email:</b> %1$s, <b>API Key:</b> %2$s, <b>App ID:</b> %3$s'),
            $settings->getSetting($this->settingKeyAppEmail),
            $settings->getSetting($this->settingKeyApiKey),
            $settings->getSetting($this->settingKeyAppId)
        );
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Spins or rewrites an article. Standard spinning costs 1 credit per 5000 words. Advanced spinning costs 1 credit
     * per 500 words. A spin is “advanced” if any of the advanced parameters below are set to 1.
     *
     * @param string $text   The text to be spun
     * @param array $options API options for this method:
     *  - Normal params
     *  'quality':          Synonym replacement quality: 5 – Best, 4 – Better, 3 – Good, 2 – Average, 1 – All. Default: 4
     *  'phrasequality':    Phrase replacement quality: 5 – Best, 4 – Better, 3 – Good, 2 – Average, 1 – All. Default: 3
     *  'posmatch':         Required Part of Speech (POS) match for a spin: 3 – Full, 2 – Loose, 1 – Extremely Loose,
     *                      0 – None. Default: 3
     *  'rewrite':          If set to 1, results are returned as a rewritten article with no Spintax. Otherwise, an
     *                      article with Spintax is returned. Note that with rewrite as 1, the original word will always
     *                      be removed. Default: 0
     *  'language':         Two letter language code for the desired language. (To be updated). Default: 'en'
     *
     *  - Advanced params
     *  'sentencerewrite':  Set to 1 to use artificial intelligence tools to automatically rewrite sentences. Default: 0
     *  'grammarcheck':     Set to 1 to verify grammar on the result article for very high quality spin. Default: 0
     *
     *  - Extra params
     *  'protectedterms'    Comma separated list of words or phrases to protect from spin i.e.
     *                      ‘my main keyword,my second keyword’. Default: ''
     *
     *  There are many other parameters that can be used. See the API documentation page.
     *
     * @return string|null Spun text or null if an error occurs
     * @throws Exception When a result could not be retrieved from the response. Also see {@link request()}
     * @see https://chimprewriter.com/api/documentation/
     * @since 1.9.0
     */
    private function chimpRewrite($text, $options = []) {
        // If there is no text, no need to make a request.
        if (trim($text) === '') return '';

        $options[$this->keyText] = $text;
        $result = $this->request($this->cmdChimpRewrite, $options);

        // The result has "status" key that has "success" value when the request is successful. It also has "output"
        // that stores the spun text or error message.
        $success = Utils::array_get($result, 'status') === 'success';
        $output  = Utils::array_get($result, 'output');

        // If the request was not successful
        if (!$success) {
            // If there is an output, it contains the error message. Throw an exception with the error message.
            if ($output) throw new Exception($output);

            // Otherwise, return null.
            return null;
        }

        return $output;
    }

    /**
     * @param string $command API command
     * @param array  $params  Parameters that should be sent to the API
     * @return array|null
     * @since 1.9.0
     * @throws Exception When an error is returned. Also see {@link validateParams()}
     */
    private function request($command, $params) {
        // Prepare the parameters
        $params = $this->validateParams($this->addDefaultParams($params));

        $apiUrl = rtrim($this->apiUrl, '/');

        // Prepare the query string with parameters
        $queryString = Utils::buildQueryString($apiUrl, $params);

        // Make the request
        $req = curl_init();
        curl_setopt($req, CURLOPT_URL, $apiUrl . '/' . $command);
        curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($req, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($req, CURLOPT_POST, true);
        curl_setopt($req, CURLOPT_POSTFIELDS, $queryString);

        $execResult = curl_exec($req);
        $result = is_string($execResult) ? trim($execResult) : null;
        curl_close($req);

        // If there is no result, return null.
        if (!$result) return null;

        $parsedResult = json_decode($result, true);
        if (!$parsedResult) return null;

        return $parsedResult;
    }

    /**
     * Validate parameters that should be sent to the API.
     *
     * @param array $params Parameters to be validated
     * @return array An array consisting of validated parameters
     * @since 1.9.0
     * @throws Exception If a required parameter does not exist in the validated parameters array
     */
    private function validateParams($params) {
        if (!$params) $params = [];

        $validated = [];
        foreach($params as $key => $value) {
            // If the value is null or false, do not add it.
            if ($value === null) continue;

            if ($value === true || $value === false) {
                $value = $value === true ? 1 : 0;

            } else {
                // If the trimmed value is empty, do not add it.
                $value = trim($value);
                if ($value === '') continue;
            }

            $validated[$key] = $value;
        }

        // Get required params and check if they all exist
        $email  = Utils::array_get($validated, $this->keyAppEmail, null);
        $apiKey = Utils::array_get($validated, $this->keyApiKey, null);
        $appId  = Utils::array_get($validated, $this->keyAppId, null);

        if ($email === null || $apiKey === null || $appId === null) {
            // Throw an exception if a required parameter does not exist.
            throw new Exception(_wpcc("You must provide email, API key, and application ID"));
        }

        return $validated;
    }

    /**
     * Add parameters that should exist in all API requests to the given parameters array.
     *
     * @param array $params Parameters that should be sent to the API. An array consisting of key-value pairs.
     * @return array Parameters array that contains the default parameters
     * @since 1.9.0
     */
    private function addDefaultParams($params) {
        if (!$params) $params = [];

        // By default, we do not want to get a result that contains spintax (spinning syntax). We want to have a
        // readable text.
        if (!isset($params[$this->keyRewrite])) {
            $params[$this->keyRewrite] = 1;
        }

        // If there are strings that should not be spun, add them as protected terms.
        if ($this->getProtectedStrings()) {
            $protectedTerms = Utils::array_get($params, $this->keyProtectedTerms, '');
            if (is_string($protectedTerms)) {
                if ($protectedTerms) $protectedTerms .= ',';

                $protectedTerms .= join(',', $this->getProtectedStrings());
                $params[$this->keyProtectedTerms] = $protectedTerms;
            }

        }

        return $params;
    }

    /*
     * STATIC METHODS
     */

    /**
     * @return array Quality parameter options to be shown in a select element
     * @since 1.9.0
     */
    public static function getQualityOptionsForSelect() {
        return [
            ''  => _wpcc('Select quality'),
            '1' => '1 (' . _wpcc('Best')    . ')',
            '2' => '2 (' . _wpcc('Better')  . ')',
            '3' => '3 (' . _wpcc('Good')    . ')',
            '4' => '4 (' . _wpcc('Average') . ')',
            '5' => '5 (' . _wpcc('All')     . ')',
        ];
    }

    /**
     * @return array Phrase quality parameter options to be shown in a select element
     * @since 1.9.0
     */
    public static function getPhraseQualityOptionsForSelect() {
        return [
            ''  => _wpcc('Select quality'),
            '1' => '1 (' . _wpcc('Best')    . ')',
            '2' => '2 (' . _wpcc('Better')  . ')',
            '3' => '3 (' . _wpcc('Good')    . ')',
            '4' => '4 (' . _wpcc('Average') . ')',
            '5' => '5 (' . _wpcc('All')     . ')',
        ];
    }

    /**
     * @return array POS match parameter options to be shown in a select element
     * @since 1.9.0
     */
    public static function getPosMatchOptionsForSelect() {
        return [
            ''  => _wpcc('Select part of speech match'),
            '3' => '3 (' . _wpcc('Full')            . ')',
            '2' => '2 (' . _wpcc('Loose')           . ')',
            '1' => '1 (' . _wpcc('Extremely loose') . ')',
            '0' => '0 (' . _wpcc('None')            . ')',
        ];
    }

    /**
     * @return array Instant unique parameter options to be shown in a select element
     * @since 1.9.0
     */
    public static function getInstantUniqueOptionsForSelect() {
        return [
            ''  => _wpcc('Select instant unique type'),
            '0' => '0 (' . _wpcc('No instant unique')  . ')',
            '1' => '1 (' . _wpcc('Full character set') . ')',
            '2' => '2 (' . _wpcc('Best character set') . ')',
        ];
    }
}
