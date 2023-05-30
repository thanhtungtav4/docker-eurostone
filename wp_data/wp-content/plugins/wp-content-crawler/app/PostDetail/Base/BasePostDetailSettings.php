<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 30/11/2018
 * Time: 09:03
 */

namespace WPCCrawler\PostDetail\Base;


use Illuminate\Contracts\View\View;
use WPCCrawler\Objects\Settings\SettingRegistry;
use WPCCrawler\Objects\Settings\SettingsImpl;

abstract class BasePostDetailSettings {

    /** @var null|SettingRegistry Contains the settings of this post detail. */
    private $settingRegistry = null;

    /** @var null|View A view that will be rendered in Site Settings page. */
    private $settingsView = null;

    /** @var null|SettingsImpl */
    private $postSettings;

    /**
     * @param SettingsImpl|null $postSettings
     */
    public function __construct(?SettingsImpl $postSettings = null) {
        $this->postSettings = $postSettings;
    }

    /**
     * @return SettingRegistry A {@link SettingRegistry} instance that contains the settings of this post detail.
     * @since 1.9.0
     * @since 1.11.1 Does not allow to return null.
     */
    abstract protected function createSettingRegistry(): SettingRegistry;

    /**
     * Create settings view. This view will be shown in the site settings page. The view can be created by using
     * {@link Utils::view()} method. If the view is outside of the plugin, it can be created using a custom implementation
     * of {@link Utils::view()}. In that case, check the source code of the method.
     *
     * @return null|View Not-rendered blade view
     */
    abstract protected function createSettingsView();

    /**
     * @return SettingRegistry
     * @since 1.9.0
     */
    public function getSettingRegistry(): SettingRegistry {
        if ($this->settingRegistry === null) $this->settingRegistry = $this->createSettingRegistry();

        return $this->settingRegistry;
    }

    /**
     * @return array See {@link SettingRegistry::getKeys()}
     */
    public function getAllMetaKeys(): array {
        return $this->getSettingRegistry()->getKeys();
    }

    /**
     * @return array See {@link SettingRegistry::getDefaults()}
     */
    public function getMetaKeyDefaults(): array {
        return $this->getSettingRegistry()->getDefaults();
    }

    /**
     * @return array See {@link SettingRegistry::getSingleKeys()}
     */
    public function getSingleMetaKeys(): array {
        return $this->getSettingRegistry()->getSingleKeys();
    }

    /**
     * @return null|View
     */
    public function getSettingsView() {
        if ($this->settingsView === null) {
            $this->settingsView = $this->createSettingsView();
        }

        return $this->settingsView;
    }

    /**
     * @return null|SettingsImpl
     * @since 1.8.0
     */
    protected function getSettings(): ?SettingsImpl {
        return $this->postSettings;
    }

}