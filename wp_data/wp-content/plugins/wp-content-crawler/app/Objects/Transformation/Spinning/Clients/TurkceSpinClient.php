<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/02/2020
 * Time: 18:11
 *
 * @since 1.10.0
 */

namespace WPCCrawler\Objects\Transformation\Spinning\Clients;

use GuzzleHttp\Client;
use WPCCrawler\Objects\Chunk\Enum\ChunkType;
use WPCCrawler\Objects\Chunk\TransformationChunker;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Spinning\TextSpinner;
use WPCCrawler\Utils;

class TurkceSpinClient extends AbstractSpinningAPIClient {

    const API_BASE_URL      = 'https://turkcespin.com/api/';
    const SPIN_ENDPOINT     = 'spin';
    const DETAILS_ENDPOINT  = 'detail';

    /** @var string */
    private $keyApiToken = 'api_token';
    /** @var string */
    private $settingKeyApiToken = SettingKey::WPCC_SPINNING_TURKCE_SPIN_API_TOKEN;

    /** @var Client */
    private $client = null;

    /**
     * @inheritDoc
     */
    public function init() {

    }

    /**
     * @inheritDoc
     */
    protected function doSetOptionsUsingSettings(SettingsImpl $settings) {
        $this->setOptions([
            $this->keyApiToken => $settings->getSetting($this->settingKeyApiToken, null),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function createChunker(array $texts): TransformationChunker {
        // There is no limit defined by TurkceSpin or the documentation (which does not exist) does not provide it. So,
        // we just define very high values so that the chunker does not care.
        $limit = 10000000;
        return new TransformationChunker(ChunkType::T_BYTES, $texts, $limit, $limit, $limit);
    }

    /**
     * @inheritDoc
     */
    public function spin(TextSpinner $textSpinner) {
        return $textSpinner->spin($this, $this->getOptions());
    }

    /**
     * @inheritDoc
     */
    public function spinBatch(array $texts, $options = []) {
        $results = [];
        foreach($texts as $text) {
            $result = $this->spinSingle($text);

            if ($result === null) {
                $result = $text;
                Informer::addInfo(_wpcc('Text could not be spun with TurkceSpin API. Due to this, original text is used.'));
            }

            $results[] = $result;
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function getUsageStatistics() {
        $response = $this->getClient()->get(static::DETAILS_ENDPOINT, [
            'query' => $this->withToken()
        ]);

        // Get the response contents and parse it to JSON
        $body    = $response->getBody()->getContents();
        $bodyArr = json_decode($body, true);

        return is_array($bodyArr) ? Utils::array_get($bodyArr, 'data', []) : [];
    }

    public function getTestResultMessage(SettingsImpl $settings) {
        return sprintf(
            _wpcc('<b>API Token:</b> %1$s'),
            $settings->getSetting($this->settingKeyApiToken)
        );
    }
    
    /*
     * PRIVATE HELPERS
     */

    /**
     * Spin a single text with the API
     *
     * @param string $text The text to be spun
     * @return string|null If the text was spun, the spun text. Otherwise, null.
     * @since 1.10.0
     */
    private function spinSingle($text) {
        $response = $this->getClient()->post(static::SPIN_ENDPOINT, [
            'query' => $this->withToken([
                'article' => $text
            ])
        ]);

        // Get the response contents and parse it to JSON
        $body    = $response->getBody()->getContents();
        $bodyArr = json_decode($body, true);

        // If it cannot be parsed to JSON, it means there is an error.
        if (!is_array($bodyArr)) {
            return null;
        }

        // Get the status code and the spun article
        $status  = Utils::array_get($bodyArr, 'status');
        $article = Utils::array_get($bodyArr, 'article');

        // If the status is not OK or the article does not exist, return null.
        if ($status !== 'ok' || !$article) {
            $message = $bodyArr['msg'] ?? null;
            if ($message !== null) {
                Informer::addInfo($message);
            }

            return null;
        }

        // The text has been spun. Return the spun text.
        return $article;
    }

    /**
     * @return Client The client
     * @since 1.10.0
     */
    private function getClient() {
        if ($this->client === null) {
            $this->client = new Client([
                'base_uri' => static::API_BASE_URL
            ]);
        }

        return $this->client;
    }

    /**
     * Get an array with its 'token' key is set
     *
     * @param array|null $array An associative array. If this is null, an empty array will be used.
     * @return array The same associative array with 'token' key is set to the token provided by the user
     * @since 1.10.0
     */
    private function withToken($array = null) {
        if (!$array) $array = [];
        $array['token'] = $this->getOption($this->keyApiToken);

        return $array;
    }
}