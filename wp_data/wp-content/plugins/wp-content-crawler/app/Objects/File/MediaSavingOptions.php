<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 23/01/2021
 * Time: 11:42
 *
 * @since 1.10.2
 */

namespace WPCCrawler\Objects\File;


use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingsImpl;

/**
 * Options that will be used when saving a remote file
 *
 * @since 1.10.2
 */
class MediaSavingOptions {

    /**
     * @var null|string Value of the user agent header of the request made to save a file. If null, WP's default user
     *      agent will be used.
     */
    private $userAgent = null;

    /**
     * @var null|array<string, string> Cookies that will be attached to the request. Key-value pairs where the keys are
     *                                 cookie names and the values are the cookie values.
     */
    private $cookies = null;

    /**
     * @var null|array<string, string> Request headers that will be attached to the request. Key-value pairs where the
     *                                 keys are header names and the values are the header values.
     */
    private $requestHeaders = null;

    /** @var bool True if the SSL certificate of the target site should be verified. Otherwise, false. */
    private $verifySsl = true;

    /** @var int Timeout for the request, in seconds. */
    private $timeoutSeconds = 10;

    /**
     * @return string|null See {@link userAgent}
     * @since 1.10.2
     */
    public function getUserAgent(): ?string {
        return $this->userAgent;
    }

    /**
     * @param string|null $userAgent See {@link userAgent}
     * @return $this
     * @since 1.10.2
     */
    public function setUserAgent(?string $userAgent): self {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return string[]|null See {@link $cookies}
     * @since 1.10.2
     */
    public function getCookies(): ?array {
        return $this->cookies;
    }

    /**
     * @param string[]|null $cookies See {@link $cookies}
     * @return self
     * @since 1.10.2
     */
    public function setCookies(?array $cookies): self {
        $this->cookies = $cookies;
        return $this;
    }

    /**
     * Set the value of {@link $cookies} from a cookie setting's value
     *
     * @param mixed $cookies Value of the {@link SettingKey::COOKIES}
     * @return self
     * @since 1.10.2
     */
    public function setCookiesFromCookieSetting($cookies): self {
        $this->cookies = !is_array($cookies)
            ? null
            : $this->convertKeyValueSettingToMap($cookies);
        return $this;
    }

    /**
     * @return string[]|null See {@link $requestHeaders}
     * @since 1.12.0
     */
    public function getRequestHeaders(): ?array {
        return $this->requestHeaders;
    }

    /**
     * @param string[]|null $requestHeaders See {@link $requestHeaders}
     * @return self
     * @since 1.12.0
     */
    public function setRequestHeaders(?array $requestHeaders): self {
        $this->requestHeaders = $requestHeaders;
        return $this;
    }

    /**
     * Set the value of {@link $requestHeaders} from a request headers setting's value
     *
     * @param mixed $requestHeaders Value of the {@link SettingKey::REQUEST_HEADERS}
     * @return self
     * @since 1.12.0
     */
    public function setRequestHeadersFromRequestHeadersSetting($requestHeaders): self {
        $this->requestHeaders = !is_array($requestHeaders)
            ? null
            : $this->convertKeyValueSettingToMap($requestHeaders);
        return $this;
    }

    /**
     * @return bool See {@link $verifySsl}
     * @since 1.12.0
     */
    public function isVerifySsl(): bool {
        return $this->verifySsl;
    }

    /**
     * @param bool $verifySsl See {@link $verifySsl}
     * @return self
     * @since 1.12.0
     */
    public function setVerifySsl(bool $verifySsl): self {
        $this->verifySsl = $verifySsl;
        return $this;
    }

    /**
     * @param array|null $settingValue The value of a key-value setting
     * @return array<string, string>|null The given value as a key-value array
     * @since 1.12.0
     */
    protected function convertKeyValueSettingToMap(?array $settingValue): ?array {
        // If the setting value does not exist, or it is empty, return null
        if (!$settingValue) {
            return null;
        }

        $map = [];
        foreach($settingValue as $data) {
            // Make sure the keys of the array are valid
            if (!isset($data[SettingInnerKey::KEY]) || !isset($data[SettingInnerKey::VALUE])) continue;

            // Get the key and value
            $key   = (string) $data[SettingInnerKey::KEY];
            $value = (string) $data[SettingInnerKey::VALUE];

            // A key must exist
            if ($key === '') continue;

            // Add the new pair to the map
            $map[$key] = $value;
        }

        return $map;
    }

    /**
     * @return int See {@link timeoutSeconds}
     * @since 1.10.2
     */
    public function getTimeoutSeconds(): int {
        return $this->timeoutSeconds;
    }

    /**
     * @param int $timeoutSeconds See {@link timeoutSeconds}
     * @return $this
     * @since 1.10.2
     */
    public function setTimeoutSeconds(int $timeoutSeconds): self {
        $this->timeoutSeconds = $timeoutSeconds;
        return $this;
    }

    /**
     * @param string|null $timeoutSeconds A numeric value for {@link timeoutSeconds}
     * @return $this
     * @since 1.10.2
     */
    public function setTimeoutSecondsFromString(?string $timeoutSeconds): self {
        $timeoutSeconds = $timeoutSeconds === null || !is_numeric($timeoutSeconds)
            ? 0
            : (int) $timeoutSeconds;

        return $this->setTimeoutSeconds($timeoutSeconds);
    }
    
    /*
     * STATIC METHODS
     */

    /**
     * Create {@link MediaSavingOptions} from a site's settings
     *
     * @param SettingsImpl $settings Site settings
     * @return MediaSavingOptions The options created from the given site settings
     * @since 1.10.2
     */
    public static function fromSiteSettings(SettingsImpl $settings): MediaSavingOptions {
        return (new MediaSavingOptions())
            ->setUserAgent($settings->getSetting(SettingKey::WPCC_HTTP_USER_AGENT, null))
            ->setCookiesFromCookieSetting($settings->getSetting(SettingKey::COOKIES, null))
            ->setRequestHeadersFromRequestHeadersSetting($settings->getSetting(SettingKey::REQUEST_HEADERS, null))
            ->setTimeoutSecondsFromString($settings->getSetting(SettingKey::WPCC_CONNECTION_TIMEOUT, 0))
            ->setVerifySsl(!$settings->getSettingForCheckbox(SettingKey::WPCC_DISABLE_SSL_VERIFICATION));
    }
    
}