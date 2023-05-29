<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 23/11/2020
 * Time: 08:18
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\GlobalShortCodes\ShortCodes\Base;


use WPCCrawler\Objects\DomainValidator;
use WPCCrawler\Utils;

abstract class BaseElementWithSrcAttributeShortCode extends BaseGlobalShortCode {

    /**
     * @return string Name of the option that stores the allowed domains. One of the constants defined in
     *                {@link SettingKey}, starting with "WPCC_".
     * @since 1.11.0
     */
    abstract public function getDomainListOptionName(): string;

    /**
     * @return string Tag name of the element that is created by the short code. E.g. "iframe" or "script"
     * @since 1.11.0
     */
    abstract public function getElementTagName(): string;

    /**
     * @param array       $attributes The attributes passed to the short code. These are prepared by allowing only the
     *                                keys defined in {@link getDefaults()}.
     * @param string|null $content    Content of the short code, if exists.
     * @return string Output of the short code.
     * @since 1.8.0
     */
    protected function parse($attributes, $content) {
        $src = Utils::array_get($attributes, "src");
        if (!$src) return '';

        // Check the validity of the source. If it is not from a valid domain, return an empty string.
        $isValid = DomainValidator::getInstance()->validateWithOption($this->getDomainListOptionName(), $src);
        if (!$isValid) return '';

        $attrString = $this->combineAttributesAsHtmlAttributeString($attributes);

        // Create and output the element
        return sprintf('<%1$s %2$s></%1$s>', $this->getElementTagName(), $attrString);
    }

    protected function getDefaults() {
        return null;
    }

}