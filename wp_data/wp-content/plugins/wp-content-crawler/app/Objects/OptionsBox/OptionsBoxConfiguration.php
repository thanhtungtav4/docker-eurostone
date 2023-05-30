<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/12/2018
 * Time: 10:17
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\OptionsBox;

use WPCCrawler\Objects\OptionsBox\Enums\OptionsBoxTab;
use WPCCrawler\Objects\OptionsBox\Enums\OptionsBoxType;
use WPCCrawler\Utils;

/**
 * Creates options box configuration. The configuration can be used in the front end to tell the options box what to
 * show, what to hide, etc.
 *
 * @package WPCCrawler\Objects\OptionsBox
 * @since   1.8.0
 */
class OptionsBoxConfiguration {

    /** @var null|OptionsBoxConfiguration */
    private static $instance = null;

    /** @var string */
    const KEY_BOX = 'box';

    /** @var string */
    const KEY_TYPE = 'type';

    /** @var string */
    private $keyTabs = 'tabs';

    /** @var array Stores the configuration. */
    private $config = [];

    /** This is a singleton. */
    private function __construct() { }

    /**
     * Initialize a configuration
     *
     * @return OptionsBoxConfiguration
     * @since 1.8.0
     */
    public static function init() {
        if (static::$instance === null) static::$instance = new OptionsBoxConfiguration();
        static::$instance->reset();

        return static::$instance;
    }

    /**
     * Resets the config to an empty array.
     * @since 1.8.0
     */
    private function reset(): void {
        $this->config = [];
    }

    /**
     * @param string $type One of the constants defined in {@link OptionsBoxType}
     * @return OptionsBoxConfiguration
     * @since 1.8.0
     */
    public function setType($type) {
        // Make sure the type is valid
        if (!OptionsBoxType::isValidValue($type)) return $this;

        // Set the box type
        $keyType = static::KEY_TYPE;
        $keyBox = static::KEY_BOX;
        Utils::array_set($this->config, "{$keyBox}.{$keyType}", $type);

        return $this;
    }

    /**
     * Add an options box configuration option.
     *
     * @param string $optionName
     * @param mixed  $optionValue
     * @return $this
     * @since 1.8.0
     */
    public function addBoxConfig($optionName, $optionValue) {
        // Set box config using dot notation
        $keyBox = static::KEY_BOX;
        Utils::array_set($this->config, "{$keyBox}.{$optionName}", $optionValue);

        return $this;
    }

    /**
     * @param string $tabKey One of the tab constants defined in {@link OptionsBoxTab}
     * @param string $optionName One of the option constants defined in {@link OptionsBoxTab}
     * @param mixed  $optionValue
     * @return $this
     * @since 1.8.0
     */
    public function addTabOption($tabKey, $optionName, $optionValue) {
        // Make sure the tab key is valid
        if (!OptionsBoxTab::isValidValue($tabKey)) return $this;

        // Set tab options using dot notation
        Utils::array_set($this->config, "{$this->keyTabs}.{$tabKey}.{$optionName}", $optionValue);

        return $this;
    }

    /**
     * Get the configuration
     *
     * @return array
     * @since 1.8.0
     */
    public function get() {
        return $this->config;
    }
}