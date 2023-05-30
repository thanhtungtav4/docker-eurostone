<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 16/04/2019
 * Time: 18:43
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Settings;


use WPCCrawler\Objects\Enums\TabKey;
use WPCCrawler\Objects\Enums\ValueType;

/**
 * Stores data about a single setting. It might be an option or a post meta. The data stored about the setting indicates
 * the key of the setting, the data structure of the setting's value, and the setting's default value.
 *
 * This class uses the constants defined in {@link SettingKey} and {@link ValueType}.
 *
 * @package WPCCrawler\Objects\Settings
 * @since   1.8.1
 */
class SettingData {

    /** @var string Key of this setting. One of the constants defined in {@link SettingKey} */
    private $key;

    /** @var int Data structure of the value of this setting. One of the constants defined in {@link ValueType}. */
    private $valueType;

    /** @var mixed Default value for this setting. The default value is null by default. */
    private $defaultVal = null;

    /** @var string|null Stores the tab under which the setting exists. One of the constants defined in {@link TabKey}. */
    private $tabKey = null;

    /**
     * @param string      $key        See {@link $key}
     * @param int         $valueType  See {@link $valueType}
     * @param mixed       $defaultVal See {@link $defaultVal}
     * @param string|null $tabKey     See {@link $tabKey}
     * @since 1.9.0
     * @since 1.10.0 Added $tabKey
     */
    public function __construct(string $key, int $valueType, $defaultVal = null, $tabKey = null) {
        // We do not check the validity of $key and $valueType by checking their availability in SettingKey and
        // SettingValueType classes, respectively, because custom keys and value types can be used to create an instance.
        // For example, other plugins, using the filters of this plugin, can define custom settings. In order not to
        // prevent them from doing so, we do not check the validity of the given parameters.
        $this->key          = $key;
        $this->valueType    = $valueType;
        $this->defaultVal   = $defaultVal;
        $this->tabKey       = $tabKey;
    }

    /**
     * @return string See {@link $key}
     * @since 1.9.0
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @return int See {@link $valueType}
     * @since 1.9.0
     */
    public function getValueType() {
        return $this->valueType;
    }

    /**
     * @return null|mixed See {@link $defaultVal}
     * @since 1.9.0
     */
    public function getDefaultVal() {
        return $this->defaultVal;
    }

    /**
     * @param string|null $tabKey See {@link $defaultVal}
     * @return $this
     * @since 1.10.0
     */
    public function setTabKey(?string $tabKey) {
        $this->tabKey = $tabKey;
        return $this;
    }

    /**
     * @return string|null See {@link $tabKey}
     * @since 1.10.0
     */
    public function getTabKey(): ?string {
        return $this->tabKey;
    }

    /**
     * @return bool True if this setting stores a single value, i.e. not an array.
     * @since 1.9.0
     */
    public function isSingleValue() {
        return $this->getValueType() !== ValueType::T_ARRAY;
    }
}
