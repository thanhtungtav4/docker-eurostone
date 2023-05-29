<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 17/02/2019
 * Time: 19:17
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Base;


use Exception;
use WPCCrawler\Objects\Chunk\TransformationChunker;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Utils;

abstract class AbstractTransformAPIClient {

    /** @var array */
    private $options;

    /**
     * @param array $options API options
     * @since 1.9.0
     */
    public function __construct($options = []) {
        $this->setOptions($options);
    }

    /**
     * Set API options
     *
     * @param array $options
     * @since 1.9.0
     */
    public function setOptions($options): void {
        $this->options = $options ?: [];
        $this->init();
    }

    /**
     * @return array
     * @since 1.9.0
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Initialize the API client using the already-set options (See {@link getOption()})
     *
     * @return void
     * @since 1.9.0
     */
    public abstract function init();

    /**
     * Set the options of this API client using given settings.
     *
     * @param SettingsImpl $settings
     * @return void
     * @since 1.9.0
     * @throws Exception When the settings do not have the required options.
     */
    public abstract function setOptionsUsingSettings(SettingsImpl $settings);

    /**
     * Create the chunker that will be used to divide given texts into several parts to satisfy the requirements of the
     * API
     *
     * @param array $texts See {@link TransformationChunker::__construct()}
     * @return TransformationChunker
     * @throws Exception See {@link TransformationChunker::__construct()}
     * @since 1.9.0
     */
    public abstract function createChunker(array $texts): TransformationChunker;

    /**
     * Get the message that will be shown in the results of the test conducted to test this API. Hint: You can use
     * the options of this API in the text to inform the user about them as well.
     *
     * @param SettingsImpl $settings Settings for which a test result message should be shown
     * @return null|string
     * @since 1.9.0
     */
    public function getTestResultMessage(SettingsImpl $settings) {
        return null;
    }

    /*
     * PROTECTED HELPERS
     */

    /**
     * Get value of an option using dot notation.
     *
     * @param string $key A dot notation key that will be used retrieve the option from {@link $options}.
     * @param mixed  $default
     * @return mixed Value of the options or $default
     * @since 1.9.0
     */
    protected function getOption($key, $default = null) {
        return Utils::array_get($this->options, $key, $default);
    }

}