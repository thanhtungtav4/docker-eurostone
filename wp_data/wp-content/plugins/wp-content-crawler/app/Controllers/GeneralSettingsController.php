<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 11:28
 */

namespace WPCCrawler\Controllers;


use Illuminate\Contracts\View\View;
use WPCCrawler\Environment;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Enums\PageSlug;
use WPCCrawler\Objects\Page\AbstractMenuPage;
use WPCCrawler\Objects\Settings\SettingService;
use WPCCrawler\Permission;
use WPCCrawler\Utils;

class GeneralSettingsController extends AbstractMenuPage {

    /**
     * @var array Keys for general settings. These keys are used both as options (general settings) and
     *            as post meta (custom settings for site).
     */
    private $settings;

    /** @var array */
    private $defaultGeneralSettings;

    public function __construct() {
        parent::__construct();

        $this->settings                 = Factory::settingRegistryService()->getRegistryGeneralSettings()->getKeys();
        $this->defaultGeneralSettings   = Factory::settingRegistryService()->getRegistryGeneralSettings()->getDefaults();

        /*
         * ALLOW MODIFICATION OF META KEYS WITH FILTERS
         */

        /**
         * Modify setting keys that are used to save general settings.
         *
         * @param array $settingKeys
         *
         * @since 1.6.3
         * @return array Modified setting keys
         */
        $this->settings = apply_filters('wpcc/general-settings/setting-keys', $this->settings);

        /**
         * Modify default values of general settings. These values will be used when there is no value set before for a
         * key. Also, these will be used when a value is required but it was not created by the user before.
         *
         * @param array $defaultGeneralSettings Default values of general setting keys
         *
         * @since 1.6.3
         * @return array Modified default general setting values
         */
        $this->defaultGeneralSettings = apply_filters('wpcc/general-settings/default-setting-values', $this->defaultGeneralSettings);

        /*
         *
         */

        // Set default settings when the plugin is activated
        register_activation_hook(Utils::getPluginFilePath(), function () {
            $this->setDefaultGeneralSettings();
        });
    }

    /**
     * @return string Menu title for the page
     */
    public function getMenuTitle(): string {
        return _wpcc('General Settings');
    }

    /**
     * @return string Page title
     */
    public function getPageTitle(): string {
        return _wpcc('General Settings');
    }

    /**
     * @return string Slug for the page
     */
    public function getPageSlug(): string {
        return PageSlug::GENERAL_SETTINGS;
    }

    /**
     * @return bool
     * @since 1.9.0
     */
    protected function isAllowed(): bool {
        return Permission::canViewGeneralSettings();
    }

    /**
     * Get view for the page.
     *
     * @return View Not-rendered blade view for the page
     */
    public function getView() {
        // Register assets
        Factory::assetManager()->addGuides();
        Factory::assetManager()->addPostSettings();
        Factory::assetManager()->addGeneralSettings();
        Factory::assetManager()->addTooltip();
        Factory::assetManager()->addMediaEditor();

        return Utils::view('general-settings/main')->with(SettingService::getSettingsPageVariables());
    }

    public function handlePOST(): void {
        parent::handlePOST();

        // If the user is not allowed to save the general settings, stop.
        if (!Permission::canUpdateGeneralSettings()) {
            $message = Environment::isDemo()
                ? _wpcc('Changing general settings has been disabled in the demo.')
                : _wpcc('You are not allowed to do this.');
            $this->redirectBack(false, $message);
            return;
        }

        $data       = $_POST;
        $isReset    = isset($data['reset']);
        $message    = '';
        $success    = true;

        $queryParams = [];
        if(isset($data[Environment::keyUrlHash()])) {
            $queryParams[Environment::keyUrlHash()] = $data[Environment::keyUrlHash()];
        }

        // Handle the request
        if (!$isReset) {
            $this->handleSaveRequest($data, $success, $message);

        } else {
            $this->handleResetRequest($success, $message);
        }

        // Set or remove CRON events
        Factory::schedulingService()->handleCronEvents();

        // Redirect back
        $this->redirectBack($success, $message, $queryParams);
    }

    public function handleAJAX(): void {
        parent::handleAJAX();
        $data = $this->getAjaxData();

        $handled = $this->respondToAJAX($data);
        if($handled) return;
    }

    /*
     * HELPERS
     */

    /**
     * Resets the general settings to their default values.
     *
     * @since 1.9.0
     */
    public function resetGeneralSettings(): void {
        foreach($this->settings as $optionKey) {
            // Delete the option first
            delete_option($optionKey);

            // Get its default value
            $defaultValue = Utils::array_get($this->defaultGeneralSettings, $optionKey, null);

            // No need to do anything if the default value is null, since we already deleted the option.
            if ($defaultValue === null) continue;

            // Update the option with the default value
            update_option($optionKey, $defaultValue, false);
        }
    }

    /**
     * Sets default general settings by updating options in the database with default values for the general settings.
     */
    public function setDefaultGeneralSettings(): void {
        $defaultSettings = $this->getDefaultGeneralSettings();

        foreach($defaultSettings as $key => $defaultSetting) {
            // Set only if the option does not exist.
            $currentVal = get_option($key, null);

            if($currentVal == null && $defaultSetting !== false) {
                update_option($key, $defaultSetting, false);
            }
        }
    }

    /**
     * @return array Default general settings
     */
    public function getDefaultGeneralSettings(): array {
        return $this->defaultGeneralSettings;
    }

    /**
     * Get options keys for general settings
     *
     * @return array An array of keys
     */
    public function getGeneralSettingsKeys(): array {
        return $this->settings;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Handles "save general settings" request made by clicking to the "save" button in the general settings page
     *
     * @param array  $data    Post data
     * @param bool   $success This variable will be true if the operation has succeeded
     * @param string $message This variable will have the response message that should be sent to the user.
     * @since 1.9.0
     */
    private function handleSaveRequest($data, &$success, &$message): void {
        $keys = $this->settings;
        $message = _wpcc("Settings updated.");
        $success = true;

        // Validate the password fields
        $validate = Utils::validatePasswordInput($data, $keys);
        if(!$validate["success"]) {
            $message = $validate["message"] . ' ' . _wpcc('Settings are updated, but password could not be changed.');
            $success = false;
        }

        // Save options
        foreach ($data as $key => $value) {
            if (in_array($key, $this->settings)) {
                update_option($key, $value, false);

                // Remove the key, since it is saved.
                unset($keys[array_search($key, $keys)]);
            }
        }

        // Delete options which are not set
        foreach($keys as $key) delete_option($key);
    }

    /**
     * Handles "reset general settings" request made by clicking to the "reset" button in the general settings page
     *
     * @param bool   $success This variable will be true if the operation has succeeded
     * @param string $message This variable will have the response message that should be sent to the user.
     * @since 1.9.0
     */
    private function handleResetRequest(&$success, &$message): void {
        $this->resetGeneralSettings();

        $message = _wpcc('General settings have been reset.');
        $success = true;
    }
}