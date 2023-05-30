<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 03/12/2018
 * Time: 15:29
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\Settings;


use WPCCrawler\Objects\Traits\SettingsTrait;

/**
 * Implementation of {@link SettingsTrait}
 *
 * @package WPCCrawler\objects
 * @since   1.8.0
 */
class SettingsImpl {

    use SettingsTrait;

    /**
     * SettingsImpl constructor.
     *
     * @param array $settings Post settings array.
     * @param array $singleKeys Single meta keys. A flat array.
     * @param bool  $prepare  True if the settings should be prepared. Otherwise, false.
     */
    public function __construct($settings, $singleKeys = [], $prepare = true) {
        $this->setSettings($settings, $singleKeys, $prepare);
        $this->setSettingsImpl($this);
    }
}