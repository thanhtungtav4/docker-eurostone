<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 23:50
 */

namespace WPCCrawler\Objects\Crawling\Bot;

use DateTime;
use DOMDocument;
use DOMElement;
use DOMNode;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler;
use WP_Post;
use WPCCrawler\Environment;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Cache\ResponseCache;
use WPCCrawler\Objects\Enums\InformationMessage;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Events\Base\AbstractCrawlingEvent;
use WPCCrawler\Objects\Events\EventService;
use WPCCrawler\Objects\File\MediaFile;
use WPCCrawler\Objects\Filtering\Explaining\Explainers\FilterSettingExplainer;
use WPCCrawler\Objects\Filtering\Explaining\FilterExplainingService;
use WPCCrawler\Objects\Filtering\Filter\FilterList;
use WPCCrawler\Objects\Filtering\FilterDependencyProvider\FilterDependencyProvider;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\OptionsBox\OptionsBoxService;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingService;
use WPCCrawler\Objects\Traits\FindAndReplaceTrait;
use WPCCrawler\Objects\Traits\SettingsTrait;
use WPCCrawler\Objects\Traits\ShortCodeReplacer;
use WPCCrawler\Utils;
use WPCCrawler\WPCCrawler;

abstract class AbstractBot {

    use FindAndReplaceTrait;
    use SettingsTrait;
    use ShortCodeReplacer;

    // TODO: PHP DOMDocument fails if there are HTML tags inside script elements. Put in more generic way, it considers
    // strings as HTML code. So, we need to remove HTML tags existing inside script elements. Simply, find "<script" and
    // remove HTML code existing until "</script>". But, be careful about script elements existing inside script element.
    // In other words, remove HTML tags until "</script>" that closes the first found "<script", not the script elements
    // inside the script element. For example, "<script>var x = '<script></script>'</script>" should become
    // "<script>var x = ''</script>", not "<script>var x = '<script></script>'". After implementing this, add this as a
    // general setting so that the user can disable this feature if he/she wants.

    /** @var string */
    private $selectAllRegex = '^.*$';

    /** @var AppClient|null */
    protected $client;

    //

    /** @var array */
    private $generalSettings;

    /** @var array */
    private $defaultGeneralSettings;

    /** @var array */
    private $botSettings;

    //

    /** @var bool */
    private $useUtf8;

    /** @var bool */
    private $convertEncodingToUtf8;

    /** @var bool */
    private $allowCookies;

    /** @var bool */
    private $disableSslVerification;

    /** @var string */
    private $httpAccept;

    /** @var string */
    private $httpUserAgent;

    /** @var array<string, string>|null The request headers as key-value pairs */
    private $requestHeaders = null;

    /** @var int */
    private $connectionTimeout;

    //

    /** @var array */
    private $proxyList;

    /** @var array */
    public $preparedProxyList = [];

    /** @var array */
    public $httpProxies = [];

    /** @var array */
    public $httpsProxies = [];

    /** @var int Maximum number of trial counts for proxies */
    private $proxyTryLimit;

    //

    /** @var int|null */
    private $siteId;

    /** @var WP_Post|null The site (WP Content Crawler site) which is being crawled */
    private $site;

    /** @var string|null Stores the content of the latest response */
    private $latestResponseContent;

    /** @var bool Stores whether the last response has been retrieved from cache or not. */
    private $isLatestResponseFromCache = false;

    /** @var bool */
    private $isResponseCacheEnabled = false;

    /**
     * @var Response|null Stores the response of the latest request. If the response was retrieved from the cache, this
     *      will be null.
     */
    private $latestResponse = null;

    /**
     * @var Exception|null Exception thrown for the latest request. If no exception is thrown for the latest request,
     *      this is null.
     */
    private $latestRequestException = null;

    /**
     * @param array     $settings              Settings for the site to be crawled
     * @param null|int  $siteId                ID of the site.
     * @param null|bool $useUtf8               If null, settings will be used to decide whether utf8 should be used or
     *                                         not. If bool, it will be used directly without considering settings. In
     *                                         other words, bool overrides the settings.
     * @param null|bool $convertEncodingToUtf8 True if encoding of the response should be converted to UTF8 when there
     *                                         is a different encoding. If null, settings will be used to decide. If
     *                                         bool, it will be used directly without considering settings. In other
     *                                         words, bool overrides the settings. This is applicable only if $useUtf8
     *                                         is found as true.
     */
    public function __construct($settings, $siteId = null, $useUtf8 = null, $convertEncodingToUtf8 = null) {
        if($siteId) $this->siteId = $siteId;

        $this->setSettings($settings, Factory::postService()->getSingleMetaKeys());

        // Get general settings
        $this->generalSettings = SettingService::getAllGeneralSettings();

        // Get the default settings
        $this->defaultGeneralSettings = Factory::generalSettingsController()->getDefaultGeneralSettings();

        // Decide which settings we should use.
        $this->botSettings = $this->getSetting(SettingKey::DO_NOT_USE_GENERAL_SETTINGS) ? $this->getSettings() : $this->generalSettings;

        /*
         *
         */

        $this->useUtf8                  = $useUtf8 !== null                 ? (bool) $useUtf8               : $this->getSettingForCheckbox(SettingKey::WPCC_MAKE_SURE_ENCODING_UTF8);
        $this->convertEncodingToUtf8    = $convertEncodingToUtf8 !== null   ? (bool) $convertEncodingToUtf8 : $this->getSettingForCheckbox(SettingKey::WPCC_CONVERT_CHARSET_TO_UTF8);

        // Set client settings by using user's preferences.
        $this->allowCookies             = $this->getSettingForCheckbox(SettingKey::WPCC_HTTP_ALLOW_COOKIES);
        $this->disableSslVerification   = $this->getSettingForCheckbox(SettingKey::WPCC_DISABLE_SSL_VERIFICATION);

        // Set ACCEPT and USER_AGENT. If these settings do not exist, use default values.
        $this->httpAccept               = $this->getSetting(SettingKey::WPCC_HTTP_ACCEPT);
        $this->httpUserAgent            = $this->getSetting(SettingKey::WPCC_HTTP_USER_AGENT);

        $this->connectionTimeout        = $this->getSetting(SettingKey::WPCC_CONNECTION_TIMEOUT, 0, true);
        $this->connectionTimeout        = !is_numeric($this->connectionTimeout) ? 0 : (int) $this->connectionTimeout;

        $this->proxyTryLimit            = $this->getSetting(SettingKey::WPCC_PROXY_TRY_LIMIT, 0, true);
        $this->proxyTryLimit            = !is_numeric($this->proxyTryLimit) ? 0 : (int) $this->proxyTryLimit;

        // Prepare the proxies
        $this->prepareProxies();

        $this->createClient();
    }

    /**
     * @return Crawler|null
     * @since 1.11.0
     */
    public abstract function getCrawler(): ?Crawler;

    /**
     * @param Crawler|null $crawler
     * @since 1.11.0
     */
    public abstract function setCrawler(?Crawler $crawler): void;

    /**
     * Prepares proxies
     */
    public function prepareProxies(): void {
        // Get the proxy list if the user wants to use proxy.
        if(!$this->getSettingForCheckbox(SettingKey::WPCC_USE_PROXY)) return;

        $this->proxyList = array_filter(array_map(function($proxy) {
            return trim($proxy);
        }, explode("\n", $this->getSetting(SettingKey::WPCC_PROXIES, "", true))));

        // If there is no proxy, no need to proceed.
        if(!$this->proxyList) return;

        $tcp = "tcp://";
        $http = "http://";
        $https = "https://";

        // Prepare proxy lists
        foreach ($this->proxyList as $proxy) {
            // If the proxy is for http, add it into httpProxies.
            if (Str::startsWith($proxy, $http)) {
                $this->httpProxies[] = $proxy;

                // If the proxy is for https, add it into httpsProxies.
            } else if (Str::startsWith($proxy, $https)) {
                $this->httpsProxies[] = $proxy;

                // Otherwise, add them to both.
            } else {
                // Get the protocol string
                preg_match("/^[a-z]+:\/\//i", $proxy, $matches);

                // If no match is found, prepend tcp
                if (!$matches || empty($matches)) {
                    $proxy = $tcp . $proxy;
                }

                // Add it to the proxy lists
                $this->httpProxies[] = $proxy;
                $this->httpsProxies[] = $proxy;
            }

            $this->preparedProxyList[] = $proxy;
        }

        $this->httpProxies  = array_unique($this->httpProxies);
        $this->httpsProxies = array_unique($this->httpsProxies);

        // Shuffle prepared proxy list if the user prefers it.
        if($this->getSettingForCheckbox(SettingKey::WPCC_PROXY_RANDOMIZE)) {
            shuffle($this->preparedProxyList);

            // Make sure the indices start from 0 and goes up 1 by 1
            $this->preparedProxyList = array_values($this->preparedProxyList);
        }

        /**
         * Modify the proxy list.
         *
         * @param array $preparedProxyList Proxy list, prepared according to the settings
         * @param AbstractBot $bot         The bot itself
         *
         * @return array preparedProxyList  Modified proxy list
         * @since 1.6.3
         */
        $this->preparedProxyList = apply_filters('wpcc/bot/proxy-list', $this->preparedProxyList, $this);

    }

    /**
     * Creates a client to be used to perform browser actions
     *
     * @param null|string   $proxyUrl Proxy URL
     * @param null|string   $protocol "http" or "https"
     */
    public function createClient($proxyUrl = null, $protocol = "http"): void {
        $this->client = new AppClient();

        $config = [
            RequestOptions::COOKIES => $this->allowCookies,
            RequestOptions::VERIFY  => $this->disableSslVerification === false,
        ];

        if($this->connectionTimeout) {
            $config[RequestOptions::CONNECT_TIMEOUT]  = $this->connectionTimeout;
            $config[RequestOptions::TIMEOUT]          = $this->connectionTimeout;
        }

        // Set the proxy
        if($proxyUrl) {
            if(!$protocol) $protocol = "http";

            if(in_array($protocol, ["http", "https", "tcp"])) {
                $config[RequestOptions::PROXY] = [
                    $protocol => $proxyUrl
                ];
            }
        }

        $this->client->setClient(new Client($config));

        if($this->httpAccept)    $this->client->setServerParameter("HTTP_ACCEPT",     $this->httpAccept);
        if($this->httpUserAgent) $this->client->setServerParameter('HTTP_USER_AGENT', $this->httpUserAgent);

        // Add the request headers
        $requestHeaders = $this->getRequestHeaders();
        foreach($requestHeaders as $headerName => $headerValue) {
            $this->client->setServerParameter($headerName, $headerValue);
        }

        /**
         * Modify the client that will be used to make requests.
         *
         * @param AppClient $client The client
         * @param AbstractBot $bot The bot itself
         *
         * @return AppClient Modified client
         * @since 1.6.3
         * @since 1.12.0 Uses AppClient class instead of Goutte's Client class
         */
        $this->client = apply_filters('wpcc/bot/client', $this->client, $this);
    }

    /**
     * Creates a new Client and prepares it by adding Accept and User-Agent headers and enabling cookies.
     * Some other routines can also be done here.
     *
     * @return AppClient
     */
    public function getClient(): AppClient {
        if ($this->client === null) {
            $this->createClient();
        }

        /** @var AppClient $client */
        $client = $this->client;
        return $client;
    }

    public function getSiteUrl(): ?string {
        return $this->getSetting(SettingKey::MAIN_PAGE_URL, null);
    }

    /**
     * Set cookies of the browser client using the settings
     *
     * @param string $url Full URL for which the cookies should be set
     */
    private function setCookies($url): void {
        // Try to get the cookies specified for this site
        $cookies = $this->getSetting(SettingKey::COOKIES);
        if(!$cookies || !$this->client) return;

        // Get cookie domain
        $urlParts = parse_url($url);
        if (!is_array($urlParts)) return;

        $domain = Utils::array_get($urlParts, 'host');
        if (!$domain) return;

        $isSecure = strpos($url, "https") !== false;

        // Add each cookie to this client
        foreach($cookies as $cookieData) {
            $key   = $cookieData[SettingInnerKey::KEY]   ?? null;
            $value = $cookieData[SettingInnerKey::VALUE] ?? null;
            if ($key === null || $value === null) continue;

            $this->client->getCookieJar()->set(new Cookie(
                $key,
                urldecode($value),
                null,
                null,
                $domain,
                $isSecure
            ));
        }

    }

    /**
     * @return array<string, string> See {@link $requestHeaders}
     * @since 1.12.0
     */
    private function getRequestHeaders(): array {
        if ($this->requestHeaders === null) {
            $this->requestHeaders = $this->createRequestHeaders() ?? [];
        }

        return $this->requestHeaders;
    }

    /**
     * @return array<string, string>|null The request headers, retrieved from {@link SettingKey::REQUEST_HEADERS}
     *                                    setting, as key-value pairs.
     * @since 1.12.0
     */
    private function createRequestHeaders(): ?array {
        $headersSetting = $this->getSetting(SettingKey::REQUEST_HEADERS);
        if (!is_array($headersSetting) || !$headersSetting) return null;

        $result = [];
        foreach($headersSetting as $data) {
            $name  = $data[SettingInnerKey::KEY]   ?? null;
            $value = $data[SettingInnerKey::VALUE] ?? null;
            if ($name === null || $name === '' || $value === null) continue;

            // The HTTP headers must be prefixed with "HTTP_"
            $result['HTTP_' . $name] = $value;
        }

        return $result;
    }

    /**
     * @param string $url                   Target URL
     * @param string $method                Request method
     * @param array|null $findAndReplaces   Find and replaces to be applied to raw response content. For the format of this
     *                                      value, see {@see FindAndReplaceTrait::findAndReplace}. Default: null
     * @return Crawler|null
     */
    public function request($url, $method = "GET", $findAndReplaces = null): ?Crawler {
        $proxyList = $this->preparedProxyList;
        $protocol = Str::startsWith($url, "https") ? "https" : "http";
        $proxyUrl = $proxyList && isset($proxyList[0]) ? $proxyList[0] : false;
        $tryCount = 0;

        do {
            try {
                // Make the request and get the response text. If the method succeeded, the response text will be
                // available in $this->latestResponseContent
                $responseText = $this->getResponseText($method, $url, $proxyUrl, $protocol);
                if (!$responseText) return null;

                // Assign it as the latest response content
                $this->latestResponseContent = $responseText;

                // If there are find-and-replace options that should be applied to raw response text, apply them.
                if($findAndReplaces) {
                    $this->latestResponseContent = $this->findAndReplace($findAndReplaces, $this->latestResponseContent, false);
                }

                /**
                 * Modify the response content.
                 *
                 * @param string $latestResponseContent Response content after the previously-set find-and-replace settings are applied
                 * @param string $url                   The URL that sent the response
                 * @param AbstractBot $bot              The bot itself
                 *
                 * @return string Modified response content
                 * @since 1.7.1
                 */
                $this->latestResponseContent = apply_filters('wpcc/bot/response-content', $this->latestResponseContent, $url, $this);

                // Try to get the HTML content. If this causes an error, we'll catch it and return null.
                $crawler = $this->createCrawler($this->latestResponseContent, $url);

                // Try to get the HTML from the crawler to see if it can do it. Otherwise, it will throw an
                // InvalidArgumentException, which we will catch.
                $crawler->html();

                return $crawler;

            } catch (ConnectException $e) {
                // If the URL cannot be fetched, try another proxy, if exists.
                $this->latestRequestException = $e;
                $tryCount++;

                // Break the loop if there is no proxy list or it is empty.
                // Stop if we've reached the try limit.
                // If the next proxy does not exist, break the loop.
                if(!$proxyList || ($this->proxyTryLimit > 0 && $tryCount >= $this->proxyTryLimit) || !isset($proxyList[$tryCount])) {
                    $msgProxyUrl = $proxyUrl ? (sprintf(_wpcc('Last tried proxy: %1$s'), $proxyUrl) . ', ') : '';

                    Informer::add(Information::fromInformationMessage(
                        InformationMessage::CONNECTION_ERROR,
                        $msgProxyUrl . sprintf(_wpcc('URL: %1$s, Message: %2$s'), $url, $e->getMessage()),
                        InformationType::INFO
                    )->setException($e)->addAsLog());

                    break;
                }

                // Get the next proxy
                $proxyUrl = $proxyList[$tryCount];

            } catch (RequestException $e) {
                // If the URL cannot be fetched, then just return null.
                $this->latestRequestException = $e;

                Informer::add(Information::fromInformationMessage(
                    InformationMessage::REQUEST_ERROR,
                    sprintf(_wpcc('URL: %1$s, Message: %2$s'), $url, $e->getMessage()),
                    InformationType::INFO
                )->setException($e)->addAsLog());

                break;

            } catch (InvalidArgumentException $e) {
                // If the HTML could not be retrieved, then just return null.
                $this->latestRequestException = $e;

                Informer::add(Information::fromInformationMessage(
                    InformationMessage::HTML_COULD_NOT_BE_RETRIEVED_ERROR,
                    sprintf(_wpcc('URL: %1$s, Message: %2$s'), $url, $e->getMessage()),
                    InformationType::INFO
                )->setException($e)->addAsLog());
                break;

            } catch (Exception $e) {
                // If there is an error, return null.
                $this->latestRequestException = $e;

                Informer::add(Information::fromInformationMessage(
                    InformationMessage::ERROR,
                    sprintf(_wpcc('URL: %1$s, Message: %2$s'), $url, $e->getMessage()),
                    InformationType::INFO
                )->setException($e)->addAsLog());
                break;
            }

        } while(true);

        return null;
    }

    /**
     * Enable/disable response caching
     *
     * @param bool $enabled  Enable or disable the response cache. True to enable.
     * @param bool $clearOld True if all previously-created response caches should be cleared.
     * @since 1.8.0
     */
    public function setResponseCacheEnabled(bool $enabled, bool $clearOld = false): void {
        $this->isResponseCacheEnabled = $enabled;

        // Delete all response cache if the cache is disabled
        if ($clearOld) ResponseCache::getInstance()->deleteAll();
    }

    /**
     * @return bool
     */
    public function isLatestResponseFromCache(): bool {
        return $this->isLatestResponseFromCache;
    }

    /**
     * Makes a request to the given URL with the given method considering the cookies and using given proxy. Then,
     * returns the response text.
     *
     * @param string $method HTTP request method, e.g. GET, POST, HEAD, PUT, DELETE
     * @param string $url Target URL
     * @param string|null $proxyUrl See {@link createClient()}
     * @param string|null $protocol See {@link createClient()}
     * @return false|string
     * @since 1.8.0
     */
    protected function getResponseText($method, $url, $proxyUrl, $protocol) {
        $this->latestResponse         = null;
        $this->latestRequestException = null;

        // If caching is enabled, try to get the response from cache.
        $this->isLatestResponseFromCache = false;
        if ($this->isResponseCacheEnabled) {
            $response = ResponseCache::getInstance()->get($method, $url);
            if ($response) {
                $this->isLatestResponseFromCache = true;
                return $response;
            }
        }

        // If there is a proxy, create a new client with the proxy settings.
        if($proxyUrl) $this->createClient($proxyUrl, $protocol);

        $this->setCookies($url);

        /**
         * Fires before any request is made.
         *
         * @param AbstractBot $bot
         * @param string      $url
         * @since 1.6.3
         */
        do_action('wpcc/before_request', $this, $url);

        $this->getClient()->request($method, $url);

        // Get the response and its HTTP status code
        $this->latestResponse = $this->getClient()->getInternalResponse();

        /**
         * Fires just after a request is made.
         *
         * @param AbstractBot $bot
         * @param string      $url
         * @since 1.6.3
         */
        do_action('wpcc/after_request', $this, $url, $this->latestResponse);

        $status = $this->latestResponse->getStatusCode();

        switch($status) {
            // Do not proceed if the target URL is not found.
            case 404:
                Informer::add(Information::fromInformationMessage(
                    InformationMessage::URL_NOT_FOUND,
                    "Target URL ({$url}) is not found ({$status}).",
                    InformationType::INFO)->addAsLog()
                );
                return false;
        }

        // Do not proceed if there was a server error.
        if($status >= 500 && $status < 600) {
            Informer::add(Information::fromInformationMessage(
                InformationMessage::REMOTE_SERVER_ERROR,
                "Server error for URL ({$url}). Status: {$status}",
                InformationType::INFO)->addAsLog()
            );
            return false;
        }

        $content = $this->latestResponse->getContent();

        // If caching enabled, cache the response.
        if ($this->isResponseCacheEnabled) ResponseCache::getInstance()->save($method, $url, $content);

        // Return the content of the response
        return $content;
    }

    /**
     * Throws a dummy {@link \GuzzleHttp\Exception\ConnectException}
     *
     * @noinspection PhpUnusedPrivateMethodInspection*/
    private function throwDummyConnectException(): void {
        throw new ConnectException("Dummy exception.", new Request("GET", "httpabc"));
    }

    /**
     * First, makes the replacements provided, then replaces relative URLs in a crawler's HTML with direct URLs.
     *
     * @param Crawler $crawler               Crawler for the page for which the replacements will be done
     * @param array|null $findAndReplaces    An array of arrays. Inner array should have:
     *      "regex":    bool    If this key exists, then search will be performed as regular expression. If not, a
     *      normal search will be done.
     *      "find":     string  What to find
     *      "replace":  string  Replacement for what is found
     * @param bool $applyGeneralReplacements True if you want to apply the replacements inserted in general settings
     *                                       page
     * @return Crawler A new crawler with replacements done
     */
    public function makeInitialReplacements(Crawler $crawler, ?array $findAndReplaces = null,
                                            bool $applyGeneralReplacements = false): Crawler {
        $html = $crawler->html();

        // First, apply general replacements
        if($applyGeneralReplacements) {
            $findAndReplacesGeneral = Utils::getOptionUnescaped(SettingKey::WPCC_FIND_REPLACE);
            if (is_array($findAndReplacesGeneral)) {
                $html = $this->findAndReplace($findAndReplacesGeneral, $html);
            }
        }

        // Find and replace what user wants.
        if($findAndReplaces) {
            $html = $this->findAndReplace($findAndReplaces, $html);
        }

        return new Crawler($html);
    }

    /**
     * Resolves relative URLs
     *
     * @param Crawler|null $crawler
     * @param null|string  $fallbackBaseUrl If a base URL is not found in the crawler, this URL will be used as the base.
     */
    public function resolveRelativeUrls(?Crawler $crawler, ?string $fallbackBaseUrl = null): void {
        if (!$crawler) return;

        // If there is a base URL defined in the HTML, use that to resolve the relative URLs.
        $baseHref = $this->extractData($crawler, 'base', 'href', null, true, true);

        // If the base URL does not exist, use the fallback URL.
        if (!$baseHref || !is_string($baseHref)) $baseHref = $fallbackBaseUrl;

        // Stop if there is no base URL.
        if (!$baseHref) return;

        // Create a URI for the base URL
        $baseUri = new Uri($baseHref);

        // Define the attributes whose values will be resolved
        // https://html.spec.whatwg.org/#dynamic-changes-to-base-urls
        $attributes = ['src', 'href', 'cite', 'ping'];

        // Resolve the values of the attributes
        foreach($attributes as $attr) {
            $this->resolveRelativeUrlForAttribute($crawler, $baseUri, $attr);
        }
    }

    /*
     * HTML MANIPULATION
     */

    /**
     * Applies changes configured in "find and replace in element attributes" option.
     *
     * @param Crawler|null $crawler   The crawler on which the changes will be done
     * @param string       $optionKey The key that stores the options for "find and replace in element attributes" input's
     *                                values
     */
    public function applyFindAndReplaceInElementAttributes(?Crawler $crawler, string $optionKey): void {
        if (!$crawler) return;

        $data = $this->getSetting($optionKey);
        if(!$data) return;

        foreach($data as $item) {
            $this->findAndReplaceInElementAttribute(
                $crawler,
                [Utils::array_get($item, SettingInnerKey::SELECTOR)],
                Utils::array_get($item, SettingInnerKey::ATTRIBUTE),
                Utils::array_get($item, SettingInnerKey::FIND),
                Utils::array_get($item, SettingInnerKey::REPLACE),
                isset($item[SettingInnerKey::REGEX])
            );
        }
    }

    /**
     * Applies changes configured in "exchange element attributes" option.
     *
     * @param Crawler|null $crawler   The crawler on which the changes will be done
     * @param string       $optionKey The key that stores the options for "exchange element attributes" input's values
     */
    public function applyExchangeElementAttributeValues(?Crawler $crawler, string $optionKey): void {
        if (!$crawler) return;

        $data = $this->getSetting($optionKey);
        if(!$data) return;

        foreach($data as $item) {
            $this->exchangeElementAttributeValues(
                $crawler,
                [Utils::array_get($item, SettingInnerKey::SELECTOR)],
                Utils::array_get($item, SettingInnerKey::ATTRIBUTE_1),
                Utils::array_get($item, SettingInnerKey::ATTRIBUTE_2)
            );
        }
    }

    /**
     * Applies changes configured in "remove element attributes" option.
     *
     * @param Crawler|null $crawler   The crawler on which the changes will be done
     * @param string       $optionKey The key that stores the options for "remove element attributes" input's values
     */
    public function applyRemoveElementAttributes(?Crawler $crawler, string $optionKey): void {
        if (!$crawler) return;

        $data = $this->getSetting($optionKey);
        if(!$data) return;

        foreach($data as $item) {
            $this->removeElementAttributes(
                $crawler,
                [Utils::array_get($item, SettingInnerKey::SELECTOR)],
                Utils::array_get($item, SettingInnerKey::ATTRIBUTE)
            );
        }
    }

    /**
     * Applies changes configured in "find and replace in element HTML" option.
     *
     * @param Crawler|null $crawler   The crawler on which the changes will be done
     * @param string       $optionKey The key that stores the options for "find and replace in HTML" input's values
     */
    public function applyFindAndReplaceInElementHTML(?Crawler $crawler, string $optionKey): void {
        if (!$crawler) return;

        $data = $this->getSetting($optionKey);
        if(!$data) return;

        foreach($data as $item) {
            $this->findAndReplaceInElementHTML(
                $crawler,
                [Utils::array_get($item, SettingInnerKey::SELECTOR)],
                Utils::array_get($item, SettingInnerKey::FIND),
                Utils::array_get($item, SettingInnerKey::REPLACE),
                isset($item[SettingInnerKey::REGEX])
            );
        }
    }

    /*
     *
     */

    /**
     * Removes the items with a 'start' position less than the given pos value.
     *
     * @param array $itemsArray An array of items. Each item in the array should have 'start' key and its value.
     * @param int $pos The reference DOM position. The elements with a 'start' position less than this will be removed.
     */
    public function removeItemsBeforePos(&$itemsArray, $pos): void {
        if(!$pos) return;

        foreach($itemsArray as $key => $item) {
            if($item["start"] < $pos) {
                unset($itemsArray[$key]);
            }
        }
    }

    /**
     * @param Crawler|null $crawler   The crawler from which the elements will be removed
     * @param array|string $selectors A selector or an array of selectors for the elements to be removed. This can also
     *                                be an array of arrays, where each inner array contains the selector in "selector"
     *                                key.
     */
    public function removeElementsFromCrawler(?Crawler $crawler, $selectors = []): void {
        if (!$crawler) return;

        $results = $this->getElementsFromCrawler($crawler, $selectors);
        if (!$results) return;

        foreach($results as $node) {
            $this->removeNode($node);
        }

    }

    /**
     * Immediately apply all filters of a filter setting
     *
     * @param string                   $settingKey Key of the setting that stores filters
     * @param FilterDependencyProvider $provider   Provider that will inject the dependencies
     * @since 1.11.0
     */
    public function applyFilterSetting(string $settingKey, FilterDependencyProvider $provider): void {
        $list = FilterList::fromJson($this->getSetting($settingKey, null));
        if (!$list) return;

        $list->applyAll($provider);
    }

    /**
     * Remove a node from its document
     *
     * @param Crawler $node
     * @since 1.11.0
     */
    public function removeNode($node): void {
        try {
            foreach ($node as $child) {
                if ($child->parentNode === null) {
                    continue;
                }

                $child->parentNode->removeChild($child);
            }

        } catch(Exception $e) {
            Informer::addError($e->getMessage())->setException($e)->addAsLog();
        }
    }

    /**
     * @param Crawler|null $crawler   The crawler from which the elements will be retrieved
     * @param array|string $selectors A selector or an array of selectors for the elements to be retrieved. This can
     *                                also be an array of arrays, where each inner array contains the selector in
     *                                "selector" key.
     * @return Crawler[]|null
     */
    public function getElementsFromCrawler($crawler, $selectors = []): ?array {
        if(empty($selectors) || !$crawler) return null;

        if(!is_array($selectors)) $selectors = [$selectors];

        $results = [];
        foreach ($selectors as $selectorData) {
            if (!$selectorData) continue;

            // Get the selector
            $selector = is_array($selectorData) ? Utils::array_get($selectorData, SettingInnerKey::SELECTOR) : $selectorData;

            // If there is no selector, continue with the next one.
            if (!$selector) continue;

            // Remove each item found by the selector
            try {
                $crawler->filter($selector)->each(function ($node) use (&$results) {
                    /** @var Crawler $node */
                    $results[] = $node;
                });
            } catch(Exception $e) {
                Informer::addError($selector . " - " . $e->getMessage())->setException($e)->addAsLog();
            }
        }

        return $results ?: null;
    }

    /**
     * Replace the values of two attributes of each element found via selectors. E.g.
     * "<img src='srcVal' data-src='dataSrcVal'>" becomes "<img src='dataSrcVal' data-src='srcVal'>"
     *
     * @param Crawler|null $crawler
     * @param array|string $selectors
     * @param string|null  $firstAttrName  Name of the first attribute. E.g. "src"
     * @param string|null  $secondAttrName Name of the seconds attribute. E.g. "data-src"
     */
    public function exchangeElementAttributeValues($crawler, $selectors, $firstAttrName, $secondAttrName): void {
        if(empty($selectors) || !$crawler
            || $firstAttrName  === null || $firstAttrName  === ''
            || $secondAttrName === null || $secondAttrName === '') return;

        if(!is_array($selectors)) $selectors = [$selectors];

        foreach ($selectors as $selector) {
            if (!$selector) continue;

            try {
                $crawler->filter($selector)->each(function ($node) use (&$firstAttrName, &$secondAttrName) {
                    /** @var Crawler $node */
                    /** @var DOMElement $child */
                    $child = $node->getNode(0);

                    // Get values of the attributes
                    $firstAttrVal = $child->getAttribute($firstAttrName);
                    $secondAttrVal = $child->getAttribute($secondAttrName);

                    // Exchange the values
                    if($secondAttrVal !== "") {
                        $child->setAttribute($firstAttrName, $secondAttrVal);
                        $child->setAttribute($secondAttrName, $firstAttrVal);
                    }
                });

            } catch(Exception $e) {
                Informer::addError($selector . " - " . $e->getMessage())->setException($e)->addAsLog();
            }
        }
    }

    /**
     * Modify a node with a callback.
     *
     * @param Crawler|null  $crawler   The crawler in which the elements will be searched for
     * @param array|string  $selectors Selectors to be used to find the elements.
     * @param callable|null $callback  A callback that takes only one argument, which is the found node, e.g.
     *                                 function(Crawler $node) {}
     */
    public function modifyElementWithCallback($crawler, $selectors, $callback): void {
        if(empty($selectors) || !$crawler || !is_callable($callback)) return;

        if(!is_array($selectors)) $selectors = [$selectors];

        foreach ($selectors as $selector) {
            if (!$selector) continue;

            try {
                $crawler->filter($selector)->each(function ($node) use (&$callback) {
                    /** @var Crawler $node */
                    call_user_func($callback, $node);
                });

            } catch(Exception $e) {
                Informer::addError($selector . " - " . $e->getMessage())->setException($e)->addAsLog();
            }
        }
    }

    /**
     * Remove an attribute of the elements found via selectors.
     *
     * @param Crawler|null $crawler
     * @param array|string $selectors
     * @param string|null  $attrName Name of the attribute. E.g. "src". You can set more than one attribute by writing
     *                               the attributes comma-separated. E.g. "src,data-src,width,height"
     */
    public function removeElementAttributes($crawler, $selectors, $attrName): void {
        if(empty($selectors) || !$attrName || !$crawler) return;

        if(!is_array($selectors)) $selectors = [$selectors];

        // Prepare the attribute names
        $attrNames = array_map(function($name) {
            return trim($name);
        }, array_filter(explode(",", $attrName)));

        foreach ($selectors as $selector) {
            if (!$selector) continue;

            try {
                $crawler->filter($selector)->each(function ($node) use (&$attrNames) {
                    /** @var Crawler $node */
                    /** @var DOMElement $child */
                    $child = $node->getNode(0);

                    // Remove the attribute
                    foreach($attrNames as $attrName) $child->removeAttribute($attrName);
                });

            } catch(Exception $e) {
                Informer::addError($selector . " - " . $e->getMessage())->setException($e)->addAsLog();
            }
        }
    }

    /**
     * Find and replace in the value of an attribute of the elements found via selectors.
     *
     * @param Crawler|null $crawler
     * @param array|string $selectors
     * @param string|null  $attrName Name of the attribute. E.g. "src"
     * @param string|null  $find
     * @param string       $replace
     * @param bool         $regex    True if find and replace strings should be considered as regular expressions.
     */
    public function findAndReplaceInElementAttribute($crawler, $selectors, $attrName, $find, $replace, $regex = false): void {
        if(empty($selectors) || !$attrName || !$crawler) return;

        // If the "find" is empty, assume the user wants to find everything.
        if($find === null || (!$find && $find !== "0")) {
            $find = $this->selectAllRegex;
            $regex = true;
        }

        if(!is_array($selectors)) $selectors = [$selectors];

        foreach ($selectors as $selector) {
            if (!$selector) continue;

            try {
                $crawler->filter($selector)->each(function ($node) use (&$attrName, &$find, &$replace, &$regex) {
                    /** @var Crawler $node */
                    /** @var DOMElement $child */
                    $child = $node->getNode(0);

                    // Get value of the attribute
                    $val = $child->getAttribute($attrName);

                    // Find and replace in the attribute's value and set the new attribute value
                    $child->setAttribute($attrName, $this->findAndReplaceSingle($find, $replace, $val, $regex));
                });

            } catch(Exception $e) {
                Informer::addError("{$selector}, {$attrName} - " . $e->getMessage())->setException($e)->addAsLog();
            }
        }
    }

    /**
     * Find and replace in an element's HTML code.
     *
     * @param Crawler|null $crawler
     * @param array|string $selectors
     * @param string|null  $find
     * @param string       $replace
     * @param bool         $regex True if find and replace strings should be considered as regular expressions.
     */
    public function findAndReplaceInElementHTML($crawler, $selectors, $find, $replace, $regex = false): void {
        if(empty($selectors) || !$crawler) return;

        // If the "find" is empty, assume the user wants to find everything.
        if($find === null || (!$find && $find !== "0")) {
            $find = $this->selectAllRegex;
            $regex = true;
        }

        if(!is_array($selectors)) $selectors = [$selectors];

        foreach ($selectors as $selector) {
            if (!$selector) continue;

            try {
                $crawler->filter($selector)->each(function ($node) use (&$find, &$replace, &$regex) {
                    /** @var Crawler $node */
                    $firstHtml = Utils::getNodeHTML($node);
                    $html = $this->findAndReplaceSingle($find, $replace, $firstHtml, $regex);

                    // If there is no change, continue with the next one.
                    if ($html === $firstHtml) return;

                    if(mb_strpos($html, "<html") !== false || mb_strpos($html, "<body") !== false) return;

                    $this->replaceElement($node, $html);
                });

            } catch(Exception $e) {
                Informer::addError("{$selector} - " . $e->getMessage())->setException($e)->addAsLog();
            }
        }
    }

    /**
     * Replaces an element with another element
     *
     * @param Crawler $node The element that will be replaced
     * @param string  $html The HTML code of the new element
     * @return DOMNode|null If a replacement is made, returns the new element. Otherwise, null.
     * @since 1.12.0
     */
    public function replaceElement(Crawler $node, string $html): ?DOMNode {
        $child = $node->getNode(0);
        if (!$child) return null;

        // Get parent tag name of the new HTML. The tag name will be used to retrieve the manipulated HTML from
        // a dummy crawler.
        $tagName = null;
        if(preg_match('/^<([^\s>]+)/', $html, $matches)) {
            $tagName = $matches[1];
        }

        // Create a dummy crawler so that we can get the manipulated HTML as DOMElement. We are able to add
        // a DOMElement to the document, but not an HTML string directly.
        $dummyCrawler = $this->createDummyCrawler($html);

        // Get the child element as DOMElement from the dummy crawler.
        $newChild = $dummyCrawler->filter('body > div' . ($tagName ? ' > ' . $tagName : ''))->first()
            ->getNode(0);
        if (!$newChild) return null;

        // Import the new child element to the main crawler's document. This is vital, because DOMElement::replaceChild
        // requires the new child to be in the same document.
        /** @var DOMNode|null $parentNode */
        $parentNode = $child->parentNode;
        if (!$parentNode) return null;

        /** @var DOMDocument|null $doc */
        $doc = $parentNode->ownerDocument;
        if (!$doc) return null;

        $newChild = $doc->importNode($newChild, true);
        if (!$newChild) return null; // @phpstan-ignore-line

        // Now, we can replace the current child with the new child.
        $parentNode->replaceChild($newChild, $child);
        return $newChild;
    }

    /**
     * Get values for a selector setting. This applies the options box configurations as well.
     *
     * @param Crawler|null      $crawler      See {@link AbstractBot::extractValuesWithSelectorData}
     * @param string            $settingName  Name of the setting from which the selector data will be retrieved
     * @param string            $defaultAttr  See {@link AbstractBot::extractValuesWithSelectorData}
     * @param false|null|string $contentType  See {@link AbstractBot::extractData}
     * @param bool              $singleResult See {@link AbstractBot::extractData}
     * @param bool              $trim         See {@link AbstractBot::extractData}
     * @return array|mixed|null If there are no results, returns null. If $singleResult is true, returns a single
     *                          result. Otherwise, returns an array. If $singleResult is false, returns an array of
     *                          arrays, where each inner array is the result of a single selector data.
     */
    public function extractValuesForSelectorSetting(?Crawler $crawler, string $settingName, $defaultAttr, 
                                                    $contentType = false, $singleResult = false, $trim = true) {
        if (!$crawler) return null;

        $selectors = $this->getSetting($settingName);
        if (!$selectors) return null;

        $results = [];

        // TODO: If there is no selector but options box options, they might be applied. For example, if there are
        // templates, the user might want to define something, without using a selector. If this is done, it must be
        // applicable in every setting having an options box, not just here.

        foreach($selectors as $data) {
            // Get the result for this selector data
            $result = $this->extractValuesWithSelectorData($crawler, $data, $defaultAttr, $contentType, $singleResult, $trim);
            if (!$result) continue;

            $results[] = $result;

            // One match is enough
            if ($singleResult) break;
        }

        if (!$results) return null;
        return $singleResult ? $results[0] : $results;
    }

    /**
     * Extract values from the crawler using selector data.
     *
     * @param Crawler|null      $crawler      The crawler from which the data should be extracted
     * @param array|null        $data         Selector data that have these keys: "selector" (optional), "attr"
     *                                        (optional), "options_box" (optional).
     * @param string            $defaultAttr  Attribute value that will be used if the attribute is not found in the
     *                                        settings
     * @param false|null|string $contentType  See {@link AbstractBot::extractData}
     * @param bool              $singleResult See {@link AbstractBot::extractData}
     * @param bool              $trim         See {@link AbstractBot::extractData}
     * @return array|null|string See {@link AbstractBot::extractData}
     * @since 1.8.0
     */
    public function extractValuesWithSelectorData(?Crawler $crawler, $data, $defaultAttr, $contentType = false,
                                                  $singleResult = false, $trim = true) {
        if (!$crawler || $data === null) return null;

        $selector = Utils::array_get($data, SettingInnerKey::SELECTOR);
        $attr     = Utils::array_get($data, SettingInnerKey::ATTRIBUTE);
        if (!$attr) $attr = $defaultAttr;

        $result = $this->extractData($crawler, $selector, $attr, $contentType, $singleResult, $trim);
        if (!$result) return null;

        // Apply options box settings
        $optionsBoxApplier = OptionsBoxService::getInstance()->createApplierFromSelectorData($data);
        if ($optionsBoxApplier) {
            $result = is_array($result) ?
                $optionsBoxApplier->applyToArray($result, $contentType ? 'data' : null) :
                $optionsBoxApplier->apply($result);
        }

        return $result;
    }

    /**
     * Extracts specified data from the crawler
     *
     * @param Crawler|null      $crawler
     * @param array|string      $selectors    A single selector as string or more than one selectors as array
     * @param string|string[]   $dataType     "text", "html", "href" or attribute of the element (e.g. "content")
     * @param string|null|false $contentType  Type of found content. This will be included as "type" in resultant
     *                                        array.
     * @param bool              $singleResult True if you want a single result, false if you want all matches. If true,
     *                                        the first match will be returned.
     * @param bool              $trim         True if you want each match trimmed, false otherwise.
     * @return array|null|string              If found, the result. Otherwise, null. If there is a valid content
     *                                        type, then the result will include an array including the position of
     *                                        the found value in the crawler HTML. If the content type is null or
     *                                        false, then just the found value will be included. <p><p> If there are
     *                                        more than one dataType:
     *                                        <li>If more than one match is found, then the "data" value will be an
     *                                        array.</li>
     *                                        <li>If only one match is found, then the data will be a string.</li>
     */
    public function extractData(?Crawler $crawler, $selectors, $dataType, $contentType, bool $singleResult, bool $trim) {
        // Check if the selectors are empty. If so, do not bother.
        if(empty($selectors) || !$crawler) return null;

        // If the selectors is not an array, make it one.
        if(!is_array($selectors)) $selectors = [$selectors];

        // If the data type is not an array, make it one.
        if(!is_array($dataType)) {
            $dataType = [$dataType];

        } else {
            // Make sure each type in the data type array is unique
            $dataType = array_unique($dataType);
        }

        $crawlerHtml = $crawler->html();
        $results = [];
        foreach($selectors as $selector) {
            if(!$selector) continue;
            if($singleResult && !empty($results)) break;

            $offset = 0;
            try {
                $crawler->filter($selector)->each(function($node) use ($dataType,
                    $singleResult, $trim, $contentType, &$results, &$offset, &$crawlerHtml) {
                    /** @var Crawler $node */

                    // If single result is needed and we have found one, then do not continue.
                    if($singleResult && !empty($results)) return;

                    $value = null;
                    foreach ($dataType as $dt) {
                        try {
                            $val = null;
                            switch ($dt) {
                                case "text":
                                    $val = $node->text();
                                    break;
                                case "html":
                                    $val = Utils::getNodeHTML($node);
                                    break;
                                default:
                                    $val = $node->attr($dt);
                                    break;
                            }

                            if($val) {
                                if($trim) $val = trim($val);
                                if($val) {
                                    if(!$value) $value = [];
                                    $value[$dt] = $val;
                                }
                            }

                        } catch (InvalidArgumentException $e) { }
                    }

                    try {
                        if($value) {
                            if ($contentType) {
                                $html = Utils::getNodeHTML($node);
                                $start = mb_strpos($crawlerHtml, $html, $offset);
                                $results[] = [
                                    "type"  =>  $contentType,
                                    "data"  =>  sizeof($value) == 1 ? array_values($value)[0] : $value,
                                    "start" =>  $start,
                                    "end"   =>  $start + mb_strlen($html)
                                ];
                                $offset = $start + 1;
                            } else {
                                $results[] = sizeof($value) == 1 ? array_values($value)[0] : $value;
                            }
                        }

                    } catch(InvalidArgumentException $e) { }
                });

            } catch(Exception $e) {
                Informer::addError("{$selector} - " . $e->getMessage())->setException($e)->addAsLog();
            }
        }

        // Return the results
        if($singleResult && !empty($results)) {
            return $results[0];

        } else if(!empty($results)) {
            return $results;
        }

        return null;
    }

    /**
     * Modify media elements in the crawler. This method finds the elements that belongs to the given media file and
     * modifies those elements with the given callback. In fact, the modification is done by the callback itself. This
     * method only finds the elements.
     *
     * @param Crawler   $crawler   The crawler in which the media file will be searched for
     * @param MediaFile $mediaFile The media file
     * @param callable  $callback  A callback that takes a MediaFile and a DOMElement instance and returns void. E.g.
     *                             function(MediaFile $mediaFile, DOMElement $domElement) {}
     * @since 1.8.0
     */
    public function modifyMediaElement($crawler, $mediaFile, $callback): void {
        // Set media alt and title in the elements having this media's local URL as their 'src' value
        $this->modifyElementWithCallback($crawler, '[src^="' . $mediaFile->getLocalUrl() . '"]',
            function($node) use (&$mediaFile, &$callback) {
                /** @var Crawler $node */
                /** @var DOMElement $child */
                $child = $node->getNode(0);

                call_user_func($callback, $mediaFile, $child);
            }
        );
    }

    /**
     * Notify the users via email if no value is found via one of the supplied CSS selectors.
     *
     * @param string  $url                         The URL
     * @param Crawler $crawler                     The crawler in which selectors will be looked for
     * @param array   $selectors                   CSS selectors. Each inner array should have <b>selector</b> and
     *                                             <b>attr</b> keys.
     * @param string  $lastEmailDateMetaKey        Post meta key that stores the last time a similar email sent.
     * @param bool    $bypassInactiveNotifications True if you want to run this method even if notifications are not
     *                                             activated in settings.
     */
    protected function notifyUser($url, $crawler, $selectors, $lastEmailDateMetaKey, $bypassInactiveNotifications = false): void {
        if(!$bypassInactiveNotifications && !SettingService::isNotificationActive()) return;

        // Check if the defined interval has passed.
        $this->addSingleKey($lastEmailDateMetaKey);
        $lastEmailDate = $this->getSetting($lastEmailDateMetaKey);
        $emailIntervalInSeconds = SettingService::getEmailNotificationInterval() * 60;

        if($lastEmailDate) {
            $lastEmailDate = strtotime($lastEmailDate);
            if(time() - $lastEmailDate < $emailIntervalInSeconds) return;
        }

        $this->loadSiteIfPossible();

        // Get the email addresses that can be sent notifications
        $emailAddresses = SettingService::getNotificationEmails();
        if(!$emailAddresses) return;

        $messagesEmptyValue = [];

        // Check each selector for existence.
        foreach($selectors as $selectorData) {
            $selector = Utils::getValueFromArray($selectorData, SettingInnerKey::SELECTOR, false);
            if(!$selector) continue;

            $attr = Utils::getValueFromArray($selectorData, SettingInnerKey::ATTRIBUTE, "text");

            $data = $this->extractData($crawler, $selector, $attr, null, false, true);

            // If no value is found by the selector, add a new message string including selector's details.
            if(!$data) {
                $messagesEmptyValue[] = $selector . " | " . $attr;
            }
        }

        // If there are messages, send them to the email addresses.
        if(!empty($messagesEmptyValue)) {
            // We will send HTML.
            add_filter('wp_mail_content_type', function() {
                return 'text/html';
            });

            $siteName = $this->site ? " (" . $this->site->post_title . ") " : '';

            $subject = _wpcc("Empty CSS selectors found") . $siteName . " - " . _wpcc("WP Content Crawler");

            // Prepare the body
            $body = Utils::view('emails.notification-empty-value')->with([
                'url'                   =>  $url,
                'messagesEmptyValue'    =>  $messagesEmptyValue,
                'site'                  =>  $this->site
            ])->render();

            /**
             * Fires just before notification emails are sent
             *
             * @param AbstractBot $bot                  The bot itself
             * @param string      $url                  URL of the page in which at least a value is found to be empty
             * @param Crawler     $crawler              The crawler in which selectors will be looked for
             * @param array       $selectors            CSS selectors that were used to find empty-valued elements
             * @param string      $lastEmailDateMetaKey Post meta key that stores the last time a similar email sent.
             * @param array       $emailAddresses       Email addresses to which a notification email should be sent
             * @param string      $subject              Subject of the notification email
             * @param string      $body                 Body of the notification email
             * @since 1.6.3
             */
            do_action('wpcc/notification/before_notify', $this, $url, $crawler, $selectors, $lastEmailDateMetaKey, $emailAddresses, $subject, $body);

            // Send emails
            foreach($emailAddresses as $to) {
                wp_mail($to, $subject, $body);
            }

            /**
             * Fires just after notification emails are sent
             *
             * @param AbstractBot $bot                  The bot itself
             * @param string      $url                  URL of the page in which at least a value is found to be empty
             * @param Crawler     $crawler              The crawler in which selectors will be looked for
             * @param array       $selectors            CSS selectors that were used to find empty-valued elements
             * @param string      $lastEmailDateMetaKey Post meta key that stores the last time a similar email sent.
             * @param array       $emailAddresses       Email addresses to which a notification email should be sent
             * @param string      $subject              Subject of the notification email
             * @param string      $body                 Body of the notification email
             * @since 1.6.3
             */
            do_action('wpcc/notification/after_notify', $this, $url, $crawler, $selectors, $lastEmailDateMetaKey, $emailAddresses, $subject, $body);
        }

        // Update last email sending date as now.
        if($this->siteId) Utils::savePostMeta($this->siteId, $lastEmailDateMetaKey, (new DateTime())->format(Environment::mysqlDateFormat()));
    }

    /*
     *
     */

    /**
     * Creates a crawler with the right encoding.
     *
     * @param string $html
     * @param string $url
     * @return Crawler
     */
    public function createCrawler($html, $url): Crawler {
        if($this->useUtf8) {
            // Check if charset is defined as meta Content-Type. If so, replace it.
            // The regex below is taken from Symfony\Component\DomCrawler\Crawler::addContent
            $regexCharset = '/\<meta[^\>]+charset *= *["\']?([a-zA-Z\-0-9_:.]+)/i';
            if(preg_match($regexCharset, $html, $matches)) {
                // Change only if it is not already utf-8
                $charset = $matches[1];
                if(strtolower($charset) !== "utf-8") {

                    // Convert the encoding from the defined charset to UTF-8 if it is required
                    if ($this->convertEncodingToUtf8) {
                        // Get available encodings
                        $availableEncodings = array_map('strtolower', mb_list_encodings());

                        // Make sure the encoding exists in available encodings.
                        if (in_array(strtolower($charset), $availableEncodings)) {
                            $html = mb_convert_encoding($html, "UTF-8", $charset);

                            // Now match again to get the right positions after converting the encoding. I'm not sure if the
                            // positions might change after converting the encoding. Hence, to be on the safe side, we're
                            // matching again.
                            preg_match($regexCharset, $html, $matches);

                        // Otherwise, we cannot convert the encoding. Inform the user.
                        } else {
                            Informer::addError(sprintf(_wpcc('Encoding %1$s does not exist in available encodings.'), $charset))
                                ->addAsLog();
                        }
                    }

                    if ($matches) {
                        $pos0 = stripos($html, $matches[0]);
                        $pos1 = $pos0 + stripos($matches[0], $matches[1]);

                        $html = substr_replace($html, "UTF-8", $pos1, strlen($matches[1]));
                    }
                }

            // Otherwise
            } else {
                // Make sure the charset is UTF-8
                /** @noinspection HtmlRequiredTitleElement */
                $html = $this->findAndReplaceSingle(
                    '(<head>|<head\s[^>]+>)',
                    '$1 <meta charset="UTF-8" />',
                    $html,
                    true
                );
            }
        }

        /*
         * PREPARE THE HTML
         */

        // Remove chars that come before the first "<"
        $posFirstLessThanChar = mb_strpos($html, "<");
        if (is_int($posFirstLessThanChar)) {
            $html = mb_substr($html, $posFirstLessThanChar);
        }

        // Remove chars that come after the last ">"
        $posLastGreaterThanChar = mb_strrpos($html, ">");
        if (is_int($posLastGreaterThanChar)) {
            $html = mb_substr($html, 0, $posLastGreaterThanChar + 1);
        }

        /*
         * CREATE THE CRAWLER
         */

        $crawler = new Crawler(null, $url);
        $crawler->addContent($html);

        return $crawler;
    }

    /**
     * Creates a dummy Crawler from an HTML.
     *
     * @param string|null $html
     * @return Crawler
     */
    public function createDummyCrawler(?string $html): Crawler {
        $html = $html !== null ? $html : '';
        /** @noinspection HtmlRequiredTitleElement */
        /** @noinspection HtmlRequiredLangAttribute */
        $html = "<html><head><meta charset='utf-8'></head><body><div>" . $html . "</div></body></html>";
        return new Crawler($html);
    }

    /**
     * Gets the content from a dummy crawler created by {@link createDummyCrawler}
     *
     * @param Crawler $dummyCrawler
     * @return string
     */
    public function getContentFromDummyCrawler($dummyCrawler): string {
        $divWrappedHtml = Utils::getNodeHTML($dummyCrawler->filter('body > div')->first());
        return mb_substr($divWrappedHtml, 5, mb_strlen($divWrappedHtml) - 11);
    }

    /**
     * @return int|null Site ID for which this bot is created
     */
    public function getSiteId(): ?int {
        return $this->siteId;
    }

    /**
     * @return WP_Post|null See {@link $site}
     * @since 1.11.0
     */
    public function getSite(): ?WP_Post {
        $this->loadSiteIfPossible();
        return $this->site;
    }

    /**
     * @return string|null See {@link $latestResponseContent}
     */
    public function getLatestResponseContent(): ?string {
        return $this->latestResponseContent;
    }

    /**
     * @return Response|null See {@link $latestResponse}
     * @since 1.11.0
     */
    public function getLatestResponse(): ?Response {
        return $this->latestResponse;
    }

    /**
     * @return Exception|null See {@link $latestRequestException}
     * @since 1.11.0
     */
    public function getLatestRequestException(): ?Exception {
        return $this->latestRequestException;
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * Trigger an {@link AbstractCrawlingEvent}
     *
     * @param string $eventClass Class name of an {@link AbstractCrawlingEvent}
     * @return $this
     * @throws Exception See {@link AbstractCrawlingEvent::notify()}
     * @since 1.11.0
     */
    protected function triggerEvent(string $eventClass): self {
        $event = EventService::getInstance()->getEvent($eventClass);
        if ($event instanceof AbstractCrawlingEvent) {
            $event->notify();
        }

        return $this;
    }

    /**
     * Initialize filters defined in a filter setting. This creates the filters and registers them to their events so
     * that they will be executed when the events are triggered.
     *
     * @param string                   $settingKey               Key of the setting that stores the filter details. One
     *                                                           of the constants defined in {@link SettingKey}.
     * @param string                   $defaultConditionEventCls Name of an {@link AbstractEvent} class that is
     *                                                           registered in {@link EventService}. This will be
     *                                                           provided to {@link FilterList::subscribeAll()}.
     * @param FilterDependencyProvider $provider                 Provider that will inject the dependencies
     * @param string|null              $name                     Name of the setting. This will be used when explaining
     *                                                           the filter setting.
     * @since 1.11.0
     */
    protected function initializeFilterSetting(string $settingKey, string $defaultConditionEventCls,
                                               FilterDependencyProvider $provider, ?string $name = null): void {
        $list = FilterList::fromJson($this->getSetting($settingKey, null));
        if (!$list) return;

        // If this is a test, add the filter list to the filter explaining service so that the explanations of the
        // filters will be added to the response.
        if (WPCCrawler::isDoingGeneralTest()) {
            FilterExplainingService::getInstance()->addFilterSettingExplainer(new FilterSettingExplainer(
                $name ?: _wpcc('(No name)'),
                $list
            ));
        }

        $defaultConditionEvent = EventService::getInstance()->getEvent($defaultConditionEventCls);
        if (!$defaultConditionEvent) {
            Informer::addError(_wpcc('Filters could not be registered because the default event does not exist.'))
                ->addAsLog();
            return;
        }

        $list->subscribeAll($provider, $defaultConditionEvent);
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Sets {@link $site} variable if there is a valid {@link $siteId}.
     */
    private function loadSiteIfPossible(): void {
        if(!$this->site && $this->siteId) {
            $this->site = get_post($this->siteId);
        }
    }

    /**
     * @param Crawler|null $crawler The crawler in which the changes will be applied
     * @param Uri          $baseUri Base URI that is retrieved by using <base> tag's href attribute
     * @param string       $attr    Target attribute. E.g. 'href', or 'cite', or 'ping', or 'src'
     */
    private function resolveRelativeUrlForAttribute(?Crawler $crawler, Uri $baseUri, string $attr): void {
        if (!$crawler) return;

        $crawler->filter('[' . $attr . ']')->each(function ($node) use (&$attr, &$baseUri) {
            /** @var Crawler $node */
            /** @var DOMElement $child */
            $child = $node->getNode(0);

            // Get value of the attribute
            $val = $child->getAttribute($attr);

            // If there is no value, stop.
            if (!$val) return;

            $resolved = Utils::resolveUrl($baseUri, $val);
            if (!$resolved) return;

            // Set the new attribute value as the resolved URI
            $child->setAttribute($attr, $resolved);
        });
    }
}
