<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 07/11/2019
 * Time: 21:11
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Settings\Factory\HtmlManip;


use WPCCrawler\Objects\Settings\Enums\SettingKey;

class PostHtmlManipKeyFactory extends AbstractHtmlManipKeyFactory {

    /** @var PostHtmlManipKeyFactory|null */
    private static $instance = null;

    public static function getInstance(): PostHtmlManipKeyFactory {
        if (static::$instance === null) {
            static::$instance = new PostHtmlManipKeyFactory();
        }

        return static::$instance;
    }
    
    /**
     * @return string The setting key storing the test code used to test the settings
     */
    public function getTestFindReplaceKey(): string {
        return SettingKey::TEST_FIND_REPLACE_FIRST_LOAD;
    }

    /**
     * @return string The setting key storing raw HTML find-replace settings
     */
    public function getFindReplaceRawHtmlKey(): string {
        return SettingKey::POST_FIND_REPLACE_RAW_HTML;
    }

    /**
     * @return string The setting key storing find-replace settings that should be applied at first load
     */
    public function getFindReplaceFirstLoadKey(): string {
        return SettingKey::POST_FIND_REPLACE_FIRST_LOAD;
    }

    /**
     * @return string The setting key storing find-replace settings applied in element attribute
     */
    public function getFindReplaceElementAttributesKey(): string {
        return SettingKey::POST_FIND_REPLACE_ELEMENT_ATTRIBUTES;
    }

    /**
     * @return string The setting key storing values of what attributes of HTML elements should be exchanged
     */
    public function getExchangeElementAttributesKey(): string {
        return SettingKey::POST_EXCHANGE_ELEMENT_ATTRIBUTES;
    }

    /**
     * @return string The setting key storing what attributes to remove
     */
    public function getRemoveElementAttributesKey(): string {
        return SettingKey::POST_REMOVE_ELEMENT_ATTRIBUTES;
    }

    /**
     * @return string The setting key storing find-replace rules to be applied to HTML codes of elements
     */
    public function getFindReplaceElementHtmlKey(): string {
        return SettingKey::POST_FIND_REPLACE_ELEMENT_HTML;
    }

    /**
     * @return string The setting key storing what elements should be removed
     */
    public function getUnnecessaryElementSelectorsKey(): string {
        return SettingKey::POST_UNNECESSARY_ELEMENT_SELECTORS;
    }

    /**
     * @inheritDoc
     */
    public function getPageFiltersKey(): string {
        return SettingKey::POST_PAGE_FILTERS;
    }

    public function getRequestFiltersKey(): string {
        return SettingKey::POST_REQUEST_FILTERS;
    }

}