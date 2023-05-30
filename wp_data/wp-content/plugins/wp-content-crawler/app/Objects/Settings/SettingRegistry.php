<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 16/04/2019
 * Time: 21:05
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Settings;


class SettingRegistry {

    /**
     * @var array<string, SettingData> A key-value pair where keys are constants defined in {@link SettingKey} and the
     * values are instances of {@link SettingData}.
     */
    private $registry;

    /**
     * @var string[]|null An array of {@link SettingKey} constants, storing only the keys having a single value, i.e.
     * not an array value.
     */
    private $singleKeys = null;

    /**
     * @var array|null An associative array where keys are setting keys and their values are their default values, i.e.
     *      [key1 => defaultVal1, key2 => defaultVal2]. This will contain only the keys which have default values
     *      defined.
     */
    private $defaults = null;

    /**
     * @param SettingData[] $settingDataArr
     * @since 1.9.0
     */
    public function __construct(array $settingDataArr) {
        $this->registry = $this->makeRegistry($settingDataArr);
    }

    /**
     * Get data of a setting by using the setting's key.
     *
     * @param string|null $key One of the constants defined in {@link SettingKey} or a setting key.
     * @return SettingData|null If exists, {@link SettingData}. Otherwise, null.
     * @since 1.9.0
     */
    public function getSettingData(?string $key): ?SettingData {
        if (!$key) return null;

        if (isset($this->registry[$key])) {
            return $this->registry[$key];
        }

        return null;
    }

    /**
     * Get the keys of the settings in this registry storing only non-array a values.
     *
     * @return string[] See {@link $singleKeys}
     * @since 1.9.0
     */
    public function getSingleKeys(): array {
        if ($this->singleKeys === null) {

            $this->singleKeys = [];
            foreach($this->registry as $key => $settingData) {
                /** @var SettingData $settingData */
                if (!$settingData->isSingleValue()) continue;

                $this->singleKeys[] = $key;
            }
        }

        return $this->singleKeys;
    }

    /**
     * @return string[] Setting keys in this registry. A sequential array consisting of strings.
     * @since 1.9.0
     */
    public function getKeys(): array {
        return array_keys($this->registry);
    }

    /**
     * @return array See {@link $defaults}
     * @since 1.9.0
     */
    public function getDefaults(): array {
        if ($this->defaults === null) {

            $this->defaults = [];
            foreach($this->registry as $key => $settingData) {
                /** @var SettingData $settingData $default */
                $default = $settingData->getDefaultVal();
                if ($default === null) continue;

                $this->defaults[$key] = $default;
            }
        }

        return $this->defaults;
    }

    /**
     * @return SettingData[]
     * @since 1.9.0
     */
    public function getAllSettingData(): array {
        return array_values($this->registry);
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Make a registry using an array of {@link SettingData}. This actually creates an indexed array.
     *
     * @param SettingData[] $settingDataArr
     * @return array An associative array where the keys are the keys of the settings, and the values are the SettingData
     *               instances.
     * @since 1.9.0
     */
    private function makeRegistry($settingDataArr): array {
        if (!is_array($settingDataArr)) return [];

        $registry = [];
        foreach($settingDataArr as $settingData) {
            if (!($settingData instanceof SettingData)) {
                continue;
            }

            $registry[$settingData->getKey()] = $settingData;
        }

        return $registry;
    }

}