<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 07/11/2019
 * Time: 21:03
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Settings\Factory\HtmlManip;


abstract class AbstractHtmlManipKeyFactory {

    /**
     * Get the instance of the factory
     *
     * @return AbstractHtmlManipKeyFactory
     */
    abstract public static function getInstance();

    /**
     * This is a singleton
     */
    protected function __construct() { }

    /**
     * @return string The setting key storing the test code used to test the settings
     */
    abstract public function getTestFindReplaceKey(): string;

    /**
     * @return string The setting key storing raw HTML find-replace settings
     */
    abstract public function getFindReplaceRawHtmlKey(): string;

    /**
     * @return string The setting key storing find-replace settings that should be applied at first load
     */
    abstract public function getFindReplaceFirstLoadKey(): string;

    /**
     * @return string The setting key storing find-replace settings applied in element attribute
     */
    abstract public function getFindReplaceElementAttributesKey(): string;

    /**
     * @return string The setting key storing values of what attributes of HTML elements should be exchanged
     */
    abstract public function getExchangeElementAttributesKey(): string;

    /**
     * @return string The setting key storing what attributes to remove
     */
    abstract public function getRemoveElementAttributesKey(): string;

    /**
     * @return string The setting key storing find-replace rules to be applied to HTML codes of elements
     */
    abstract public function getFindReplaceElementHtmlKey(): string;

    /**
     * @return string The setting key storing what elements should be removed
     */
    abstract public function getUnnecessaryElementSelectorsKey(): string;

    /**
     * @return string The setting key storing page filters
     */
    abstract public function getPageFiltersKey(): string;

    /**
     * @return string The setting key storing request filters
     */
    abstract public function getRequestFiltersKey(): string;

}