<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/12/2018
 * Time: 12:33
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects;


use GuzzleHttp\Psr7\Uri;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Utils;

class DomainValidator {

    /** @var DomainValidator */
    private static $instance = null;

    /** @var array */
    private $optionCache = [];

    /**
     * Get the instance
     *
     * @return DomainValidator
     * @since 1.8.0
     */
    public static function getInstance() {
        if (static::$instance === null) static::$instance = new DomainValidator();
        return static::$instance;
    }

    /** This is a singleton. */
    private function __construct() { }

    /**
     * Checks if a domain is valid using the domains provided by the user.
     *
     * @param string|null $domainListOptionName Name of the option that stores the domain list.
     *                                          E.g. '_wpcc_allowed_iframe_short_code_domains'
     * @param string|null $sourceUrl            See {@link validateWithRules()}
     * @return bool Returns what {@link validateWithRules()} returns
     * @uses  validateWithRules()
     * @since 1.11.0
     */
    public function validateWithOption(?string $domainListOptionName, ?string $sourceUrl): bool {
        return $this->validateWithRegexes($this->getDomainRegexesFromOption($domainListOptionName), $sourceUrl);
    }

    /**
     * Checks if a domain is valid using the domain rules
     *
     * @param string[]|null $domainRules An array of domain rules that will be converted to regexes to match valid
     *                                   domains
     * @param string|null   $sourceUrl   {@link validateWithRules()}
     * @return bool Returns what {@link validateWithRules()} returns
     * @since 1.11.0
     */
    public function validateWithRules(?array $domainRules, ?string $sourceUrl): bool {
        return $this->validateWithRegexes($this->convertRulesToRegexes($domainRules), $sourceUrl);
    }

    /**
     * Checks if a domain is valid using the domain regexes
     *
     * @param string[]|null $domainRegexes An array of domain regexes that may match the given source URL's host
     * @param string|null   $sourceUrl     Source URL to be validated against. E.g.
     *                                     "https://wordpress.org/resource/path/"
     * @return bool If the host of the source URL matches one of the domain regexes, true. Otherwise, false.
     * @since 1.11.0
     */
    public function validateWithRegexes(?array $domainRegexes, ?string $sourceUrl): bool {
        if ($sourceUrl === null || !$domainRegexes) return false;

        // Get the domain of the source URL
        $uri = new Uri($sourceUrl);
        $host = $uri->getHost();

        // Check if the host (domain) is one of the provided domains.
        foreach($domainRegexes as $regex) {
            if ($this->isDomainValid($regex, $host)) {
                return true;
            }
        }

        // The domain is not valid.
        return false;
    }

    /**
     * @param string[]|null $rules An array of domain rules
     * @return string[]|null Regex equivalents of the given rules
     * @since 1.11.0
     */
    public function convertRulesToRegexes(?array $rules): ?array {
        if ($rules === null) return null;

        $regexes = [];
        foreach($rules as $rule) {
            $regexes[] = $this->convertRuleToRegex($rule);
        }

        return $regexes;
    }

    /**
     * Clear the option cache.
     *
     * @since 1.11.0
     */
    public function clearOptionCache(): void {
        $this->optionCache = [];
    }

    /*
     *
     */

    /**
     * @param string|null $regex        A regex that matches a valid domain.
     * @param string|null $testedDomain The domain to be checked if it is valid.
     * @return bool True if the domain is valid.
     * @since 1.8.0
     */
    private function isDomainValid(?string $regex, ?string $testedDomain): bool {
        if ($regex === null || $testedDomain === null) return false;

        // Try to match
        return !!preg_match($regex, $testedDomain);
    }

    /**
     * @param string|null $domainListOptionName The option name from which the domain rules will be retrieved. See
     *                                          {@link getOptionValue()}
     * @return string[] An array of regular expressions extracted from the given option
     * @since 1.8.0
     * @since 1.11.0 Renamed from getDomainRegexes to getDomainRegexesFromOption.
     */
    private function getDomainRegexesFromOption(?string $domainListOptionName): array {
        if ($domainListOptionName === null) return [];

        $domainData = $this->getOptionValue($domainListOptionName);
        if (!$domainData) return [];

        $regexes = [];
        foreach($domainData as $data) {
            $rule = Utils::array_get($data, SettingInnerKey::DOMAIN);
            if ($rule === null || $rule === '') continue;

            $regexes[] = $this->convertRuleToRegex($rule);
        }

        return $regexes;
    }

    /**
     * Create a regular expression from a domain rule
     *
     * @param string $rule A domain rule
     * @return string The regular expression equivalent of the rule
     * @since 1.11.0
     */
    private function convertRuleToRegex(string $rule): string {
        // Prepare the regular expression
        $rule = trim($rule, '/');                        // Trim the forward slashes
        $rule = preg_quote($rule, '/');                 // Quote the regex
        $rule = str_replace('\*', '.*?', $rule);  // Replace wildcards with corresponding regex.

        // Create the final regex that matches non-case-sensitive
        return "/^{$rule}$/i";
    }

    /**
     * Get an option's value. This method caches the results.
     *
     * @param string $optionName Name of the option
     * @return mixed Value of the option
     * @since 1.8.0
     */
    private function getOptionValue($optionName) {
        if (!isset($this->optionCache[$optionName])) {
            $this->optionCache[$optionName] = get_option($optionName);
        }

        return $this->optionCache[$optionName];
    }
}