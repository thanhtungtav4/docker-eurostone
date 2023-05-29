<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/12/2018
 * Time: 07:54
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\GlobalShortCodes\ShortCodes\Base;


abstract class BaseGlobalShortCode {

    /**
     * Registers the short code.
     *
     * @since 1.8.0
     */
    public function register(): void {
        add_shortcode($this->getTagName(), function($attributes, $content = null) {
            // Get the defaults
            $defaults = $this->getDefaults();

            // Prepare if the defaults are available
            $prepared = $defaults !== null ? shortcode_atts($defaults, $attributes) : $attributes;

            // Parse the short code and return the output.
            return $this->parse($prepared, $content);
        });
    }

    /**
     * @return string Tag of the short code. E.g. "wpcc-iframe". Use only lower case characters.
     * @since 1.8.0
     */
    abstract public function getTagName();

    /**
     * @param array       $attributes The attributes passed to the short code. These are prepared by allowing only the
     *                                keys defined in {@link getDefaults()}.
     * @param string|null $content    Content of the short code, if exists.
     * @return string Output of the short code.
     * @since 1.8.0
     */
    abstract protected function parse($attributes, $content);

    /**
     * @return array|null A key-value pair where keys are the allowed attributes of the short code, and the values are
     *                    their default values. This array will be used to sanitize the attributes of the short code.
     *                    If this returns null, no sanitizing will be done and all attributes will be available.
     * @since 1.8.0
     */
    abstract protected function getDefaults();

    /*
     *
     */

    /**
     * Combines attributes such that it can be used as attribute string for an HTML element. For example, if the given
     * array is ["src" => "turgutsaricam.com", "type" => "text/javascript"], this creates a string as
     * 'src="turgutsaricam.com" type="text/javascript"'.
     *
     * @param array $attributes A key-value pair where keys are attribute names and the values are their values.
     * @return string
     * @since 1.8.0
     */
    protected function combineAttributesAsHtmlAttributeString($attributes) {
        if (!$attributes) return '';

        $attrArr = [];

        // Collect attributes as 'key="value"'
        foreach($attributes as $k => $v) {
            // If the key is an integer add the value directly. E.g. in <script async src="...">, async attribute is
            // added to attributes as [0 => "async"]. Here, we do not want to show '0="async"', we want to show 'async'
            // directly.
            if (is_int($k)) {
                $attrArr[] = $v;
            } else {
                // Otherwise, it is like ["src" => "..."]. So, we want to show it like 'src="..."'
                $attrArr[] = $k . ($v ? "=\"{$v}\"" : '');
            }
        }

        // Combine them with space
        return implode(' ', $attrArr);
    }

}