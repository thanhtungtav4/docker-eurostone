<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/12/2018
 * Time: 10:55
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\GlobalShortCodes\ShortCodes;


use WPCCrawler\Objects\GlobalShortCodes\ShortCodes\Base\BaseElementWithSrcAttributeShortCode;
use WPCCrawler\Objects\Settings\Enums\SettingKey;

class ScriptGlobalShortCode extends BaseElementWithSrcAttributeShortCode {

    const TAG_NAME = "wpcc-script";

    /**
     * @return string Tag of the short code. E.g. "wpcc-iframe". Use only lower case characters.
     * @since 1.8.0
     */
    public function getTagName() {
        return static::TAG_NAME;
    }

    public function getDomainListOptionName(): string {
        return SettingKey::WPCC_ALLOWED_SCRIPT_SHORT_CODE_DOMAINS;
    }

    public function getElementTagName(): string {
        return 'script';
    }
}
