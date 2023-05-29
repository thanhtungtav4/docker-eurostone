<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/08/16
 * Time: 10:21
 */

namespace WPCCrawler\Objects\Traits;


use WPCCrawler\Factory;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Settings\SettingRegistryService;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Settings\SettingService;
use WPCCrawler\Utils;

trait SettingsTrait {

    /** @var array Stores all settings */
    private $_settings = [];

    /** @var array Stores single keys */
    private $_singleKeys = [];

    /** @var array */
    private $_generalSettingsKeys = [];

    /** @var array */
    private $_generalSettings = [];

    /** @var array */
    private $_defaultGeneralSettings = [];

    /** @var bool Populated from the settings. True if a general setting key should be populated from supplied settings. */
    private $_doNotUseGeneralSettings = false;

    /** @var bool Stores whether the settings were prepared or not */
    private $_isPrepared = false;

    /** @var null|SettingsImpl */
    private $_settingsImpl = null;

    /**
     * Set the settings. This method unserializes values of the supplied array. So, when you use {@link getSetting},
     * you will get unserialized value.
     *
     * @param array $settings   Key,value pairs
     * @param array $singleKeys Setting keys that should return single values.
     * @param bool  $prepare True if the settings should be prepared. Otherwise, false.
     */
    public function setSettings($settings, $singleKeys = [], $prepare = true): void {
        $this->_settings    = $settings;
        $this->_singleKeys  = $singleKeys;
        $this->_isPrepared  = !$prepare;

        $this->invalidateSettingsImpl();
        $this->_prepareSettings();
    }

    /**
     * Set whether general settings should be used or not.
     *
     * @param bool $doNotUseGeneralSettings True if the general settings should not be used.
     * @since 1.9.0
     */
    public function setDoNotUseGeneralSettings($doNotUseGeneralSettings): void {
        $this->_doNotUseGeneralSettings = $doNotUseGeneralSettings;
    }

    /**
     * @return bool True if general settings are not used. Otherwise, false.
     * @since 1.9.0
     */
    public function isUseGeneralSettings() {
        return !$this->_doNotUseGeneralSettings;
    }

    /**
     * Get value of a setting.
     *
     * @param string $key        Setting key
     * @param mixed  $default    Default value that should be returned if the key is not found in the settings.
     * @param bool   $allowEmpty Set this to true if you do not want to allow empty values. If you do not allow empty
     *                           values and the value is empty, the setting will be tried to be retrieved from default
     *                           general settings.
     * @return mixed Value
     */
    public function getSetting($key, $default = false, $allowEmpty = false) {
        // If this is a general setting
        if(in_array($key, $this->_generalSettingsKeys)) {
            $generalSettingDefault = $this->_getSingleSetting($this->_defaultGeneralSettings, $key, $default, $allowEmpty);

            if($this->_doNotUseGeneralSettings) {
                $val = $this->_getSingleSetting($this->_settings, $key, $generalSettingDefault, $allowEmpty);

            } else {
                $val = $this->_getSingleSetting($this->_generalSettings, $key, $generalSettingDefault, $allowEmpty);
            }

        // This is a setting that should be found in supplied settings.
        } else {
            $val = $this->_getSingleSetting($this->_settings, $key, $default, $allowEmpty);
        }

        return $val;
    }

    /**
     * Get value of a checkbox setting
     *
     * @param string $key   Key for the setting
     * @return bool
     */
    public function getSettingForCheckbox($key): bool {
        if(!$this->_doNotUseGeneralSettings && in_array($key, $this->_generalSettingsKeys)) {
            return isset($this->_generalSettings[$key]);

        } else {
            return isset($this->_settings[$key]);
        }
    }

    /**
     * Add a key that should return a single value, instead of array, when it is acquired via {@link getSetting}.
     *
     * @param string $key
     */
    public function addSingleKey($key): void {
        if(in_array($key, $this->_singleKeys)) return;
        $this->_singleKeys[] = $key;

        $this->invalidateSettingsImpl();
    }

    /**
     * Get prepared settings.
     *
     * @return array Settings
     */
    public function getSettings() {
        return $this->_settings;
    }

    /**
     * @return bool See {@link $_isPrepared}
     * @since 1.8.0
     */
    public function isSettingsPrepared() {
        return $this->_isPrepared;
    }

    /**
     * @return SettingsImpl
     * @since 1.8.0
     */
    public function getSettingsImpl(): SettingsImpl {
        if (!$this->_settingsImpl) {
            $this->_settingsImpl = new SettingsImpl($this->getSettings(), $this->_singleKeys, !$this->isSettingsPrepared());
        }

        return $this->_settingsImpl;
    }

    /*
     *
     */

    /**
     * @param SettingsImpl $settingsImpl
     * @since 1.8.0
     */
    protected function setSettingsImpl(SettingsImpl $settingsImpl): void {
        $this->_settingsImpl = $settingsImpl;
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * @param array  $array      The array that stores the setting
     * @param string $key        Key of the setting that will be retrieved from the $array
     * @param mixed  $default    Default value for the setting, which will be returned if value of the setting is not
     *                           valid.
     * @param bool   $allowEmpty Set this to true if you do not want to allow empty values. If you do not allow empty
     *                           values and the value is empty, the $default value will be returned.
     * @return mixed
     */
    private function _getSingleSetting($array, $key, $default = false, $allowEmpty = false) {
        if(isset($array[$key])) {
            if($allowEmpty || $array[$key] !== "") {
                return $this->_prepareSettingValue($key, $array[$key]);
            }

        } else {
            // Keys of the checkbox settings are not in the array when they are unchecked, since the submitted form data
            // do not contain the key. Hence, in that case, instead of returning the default, false should be returned.
            // Since this statement is reached only if the setting key is not in the array, let's check if this is a
            // checkbox setting.
            $settingData = SettingRegistryService::getInstance()->getSettingData($key);
            if ($settingData && $settingData->getValueType() === ValueType::T_BOOLEAN) {
                // This is a checkbox setting and its key is not in the array. This means the user did not check the
                // checkbox. So, the value of the setting is false.
                return false;
            }
        }

        return $default;
    }

    /**
     * Prepare the settings by unserializing serialized values.
     */
    private function _prepareSettings(): void {
        // Prepare the settings only if they were not prepared before
        if (!$this->_isPrepared) {

            $settingsPrepared = [];
            foreach($this->_settings as $key => $value) {
                $settingsPrepared[$key] = Utils::getUnserialized($this->_prepareSettingValue($key, $value));
            }

            $this->_settings = $settingsPrepared;
        }

        $this->_generalSettingsKeys = Factory::generalSettingsController()->getGeneralSettingsKeys();

        // Get general settings
        $this->_generalSettings = SettingService::getAllGeneralSettings();

        // Get the default settings
        $this->_defaultGeneralSettings = Factory::generalSettingsController()->getDefaultGeneralSettings();

        $this->_doNotUseGeneralSettings = $this->getSettingForCheckbox(SettingKey::DO_NOT_USE_GENERAL_SETTINGS);

        // Mark it as prepared
        $this->_isPrepared = true;
    }

    /**
     * Prepares the value if the key is a single key or an array key. If this trait is used for a post type, then the
     * settings are post meta values. Post meta values are retrieved as an array no matter what. That's why this method
     * exists. This method checks whether a key is among single keys. If so, it gets the first item from the array.
     * Otherwise, it returns the value without any changes if the array's 0 key is not null.
     *
     * @param string $key   Key for the setting
     * @param mixed $val    Value of the setting
     * @return mixed        If the key is a single key and the value is array, it will be the first item in the array.
     *                      Otherwise, the value without any changes.
     */
    private function _prepareSettingValue($key, $val) {
        // If this is a key that must store a single value, not an array
        if(!empty($this->_singleKeys) && in_array($key, $this->_singleKeys)) {
            if (is_array($val)) {
                $val = $val[0];
            }

        // If this key must store an array
        } else {
            if (is_array($val) && array_key_exists(0, $val) && $val[0] === null) {
                $val = null;
            }
        }

        return $val;
    }

    /**
     * Invalidates the settings object
     * @since 1.8.0
     */
    private function invalidateSettingsImpl(): void {
        $this->_settingsImpl = null;
    }
}
