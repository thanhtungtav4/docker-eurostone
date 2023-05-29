<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/10/2018
 * Time: 08:14
 */

namespace WPCCrawler\Objects\Traits;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use WPCCrawler\Factory;

trait ShortCodeReplacer {

    /** @var array */
    private $_predefinedShortCodeNames = [];

    /**
     * Replaces a short code in a template with a value
     *
     * @param string      $template       The template including the short code
     * @param string      $shortCode      The short code to be replaced without square brackets
     * @param string|null $value          The string to put in place of the short code
     * @param string      $openingBracket Opening bracket for the short code. Default: '['
     * @param string      $closingBracket Closing bracket for the short code. Default: ']'
     */
    protected function replaceShortCode(&$template, $shortCode, $value, $openingBracket = '[', $closingBracket = ']'): void {
        if ($value === null) {
            $value = '';
        }

        $template = str_replace($openingBracket . $shortCode . $closingBracket, $value, $template);
    }

    /**
     * Replaces short codes of a single template
     *
     * @param array       $map            See {@link replaceShortCodes()}
     * @param string      $template       See {@link replaceShortCodes()}
     * @param string|null $innerKey       See {@link replaceShortCodes()}
     * @param string      $openingBracket See {@link replaceShortCodes()}
     * @param string      $closingBracket See {@link replaceShortCodes()}
     * @return string The template with its shortcodes replaced
     * @since 1.11.1
     */
    protected function replaceShortCodesSingle(array &$map, string $template, ?string $innerKey = null, 
                                               string $openingBracket = '[', string $closingBracket = ']'): string {
        $result = $this->replaceShortCodes($map, [$template], $innerKey, $openingBracket, $closingBracket);

        // If the result is a non-empty array, return its first item. Otherwise, it means the replacement did not
        // happen. In that case, return the template back.
        return $result 
            ? array_values($result)[0] 
            : $template;
    }

    /**
     * Replaces all short codes in an array of templates using the given short code value map
     *
     * @param array        $map            Key-value pair where keys are short code names without brackets, and
     *                                     values are the values to be put in place of the short code. E.g.
     *                                     ['short_code_name' => 'Value of the short code', 'short_code_name_2' =>
     *                                     'Value 2']. The values can be a callable. In that case, when the short code
     *                                     whose value is a callable needs to be replaced, the value will be retrieved
     *                                     by calling the callable. Hence, the callable must return the value. This
     *                                     might be handy for values whose creation is costly.
     * @param array        $templates      An array of templates. If it is an array, this method makes deep
     *                                     replacement by replacing values of arrays. It goes as deep as the array is.
     * @param null|string  $innerKey       If given $templates array contains arrays as its values, then you can
     *                                     define this to point which key of the inner array contains the template.
     *                                     E.g. if a value of $templates is
     *                                     ["data" => "template", "start" => 2000, "end" => 5000],
     *                                     then you can pass "data" as $innerKey so that short codes in "template"
     *                                     are replaced.
     * @param string       $openingBracket See {@link ShortCodeReplacer::replaceShortCode()}
     * @param string       $closingBracket See {@link ShortCodeReplacer::replaceShortCode()}
     * @return array The templates with their shortcodes replaced
     */
    protected function replaceShortCodes(&$map, $templates, ?string $innerKey = null, string $openingBracket = '[', 
                                         string $closingBracket = ']'): array {
        if (!$templates) return [];

        foreach($templates as &$template) {
            $subject = $innerKey ? $template[$innerKey] : $template;

            // If the subject is array, replace its short codes, assuming it is a string array.
            if (is_array($subject)) {
                $subject = $this->replaceShortCodes($map, $subject, null, $openingBracket, $closingBracket);

            } else {
                // If the template does not contain any short codes, continue with the next one.
                if (!$this->hasShortCode($subject, $openingBracket, $closingBracket)) continue;

                // Replace each predefined short code existing in the template
                foreach($map as $scName => &$scValue) {
                    // If the value is a callable
                    if (is_callable($scValue)) {
                        // Check if the subject has this short code because retrieving the value is a costly operation.
                        // If the subject does not have the short code, no need to compute the value for the short code.
                        if(!Str::contains($subject, $openingBracket . $scName . $closingBracket)) continue;

                        // Get the value of the short code
                        $scValue = call_user_func($scValue);
                    }

                    // If the value is an array, make it a string.
                    if (is_array($scValue)) {
                        $scValue = implode('', array_values(Arr::flatten($scValue)));
                    }

                    $this->replaceShortCode($subject, $scName, $scValue, $openingBracket, $closingBracket);
                }
            }

            if ($innerKey) {
                $template[$innerKey] = $subject;
            } else {
                $template = $subject;
            }
        }

        return $templates;
    }

    /**
     * Clear a template from remaining short codes. For instance, if the template has predefined short codes in it,
     * which are not replaced, this method will remove those short codes. For predefined short codes, see
     * {@link PostService::getPredefinedShortCodes}.
     *
     * @param string $template       The template which should be cleared from remaining short codes
     * @param string $openingBracket See {@link ShortCodeReplacer::replaceShortCode()}
     * @param string $closingBracket See {@link ShortCodeReplacer::replaceShortCode()}
     */
    protected function clearRemainingPredefinedShortCodes(&$template, $openingBracket = '[', $closingBracket = ']'): void {
        $names = $this->getPredefinedShortCodeNames();

        // Remove each short code from the template.
        foreach($names as $shortCodeName) {
            $this->replaceShortCode($template, $shortCodeName, "", $openingBracket, $closingBracket);
        }

    }

    /**
     * Get predefined short code names, such as wcc-main-title, wcc-main-excerpt, etc.
     *
     * @return array An array of strings, which are short code names without brackets.
     */
    protected function getPredefinedShortCodeNames() {
        if(!$this->_predefinedShortCodeNames) {
            $names = array_map(function($name) {
                return str_replace(["[", "]"], "", $name);
            }, Factory::postService()->getPredefinedShortCodes());

            $this->_predefinedShortCodeNames = $names;
        }

        return $this->_predefinedShortCodeNames;
    }

    /**
     * Check if a string has at least one short code in it. Checks for a pattern that looks like "[short code name
     * etc... ]"
     *
     * @param string $str
     * @param string $openingBracket See {@link ShortCodeReplacer::replaceShortCode()}
     * @param string $closingBracket See {@link ShortCodeReplacer::replaceShortCode()}
     * @return bool
     */
    private function hasShortCode($str, $openingBracket = '[', $closingBracket = ']'): bool {
        return preg_match('/' . preg_quote($openingBracket) . '[^\s].*?' . preg_quote($closingBracket) .'/', $str, $matches) === 1;
    }
}