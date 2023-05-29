<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 23:49
 */

namespace WPCCrawler\Objects\Crawling\Bot;


use Exception;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Arr;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Environment;
use WPCCrawler\Objects\Crawling\Data\CategoryData;
use WPCCrawler\Objects\Crawling\Data\Url\PostUrl;
use WPCCrawler\Objects\Crawling\Data\Url\PostUrlList;
use WPCCrawler\Objects\Crawling\Interfaces\MakesCrawlRequest;
use WPCCrawler\Objects\Events\Events\AfterCategoryCrawlerReadyEvent;
use WPCCrawler\Objects\Events\Events\AfterCategoryRequestEvent;
use WPCCrawler\Objects\Events\Events\CategoryDataReadyEvent;
use WPCCrawler\Objects\Filtering\FilterDependencyProvider\Page\CategoryPageFilterDependencyProvider;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Utils;
use WPCCrawler\WPCCrawler;

class CategoryBot extends AbstractBot implements MakesCrawlRequest {

    /** @var string */
    private $keyLastEmptySelectorEmailDate = '_last_category_empty_selector_email_sent';

    /** @var Crawler|null */
    private $crawler;

    /** @var string|null */
    private $url;

    /** @var int|null HTTP status code of the response of the crawling request */
    private $responseHttpStatusCode = null;

    /** @var CategoryData|null */
    private $categoryData;

    /** @var null|Uri */
    private $uri = null;

    /**
     * Collects URLs for a site from the given URL
     *
     * @param string $url A full URL to be used to get post URLs
     * @return CategoryData|null
     */
    public function collectUrls($url): ?CategoryData {
        $this->setUrl($url);
        $this->categoryData = new CategoryData();

        try {
            $this->doActionBeforeRetrieve();

            $findAndReplacesForRawHtml = $this->getSetting(SettingKey::CATEGORY_FIND_REPLACE_RAW_HTML, null);
            $crawlingUrl = $this->getCrawlingUrl();
            if ($crawlingUrl === null) return null;

            $this->crawler = $this->request($crawlingUrl, "GET", $findAndReplacesForRawHtml);

            $latestResponse = $this->getLatestResponse();
            $this->responseHttpStatusCode = $latestResponse ? $latestResponse->getStatusCode() : null;
            $this
                ->initializeFilters(SettingKey::CATEGORY_REQUEST_FILTERS, AfterCategoryRequestEvent::class, false, _wpcc('Category request filters'))
                ->triggerEvent(AfterCategoryRequestEvent::class);

            if(!$this->crawler) return null;

            $this
                ->doActionAfterRetrieve()
                ->applyFilterCrawlerRaw()

                ->prepareCrawler()                  // Prepare the crawler by applying HTML manipulations
                ->applyFilterCrawlerPrepared()

                ->initializeFilters(SettingKey::CATEGORY_PAGE_FILTERS, AfterCategoryCrawlerReadyEvent::class, false, _wpcc('Category page filters'))
                ->triggerEvent(AfterCategoryCrawlerReadyEvent::class)

                ->preparePostUrls()                 // Prepare post URLs
                ->prepareThumbnails();              // Prepare thumbnails

            $this
                ->maybeReverseUrlOrder()            // If the order of the URLs should be reversed, do so.
                ->prepareNextPageUrl()              // Prepare next page URL
                ->applyFilterCategoryData()

                ->initializeFilters(SettingKey::CATEGORY_DATA_FILTERS, CategoryDataReadyEvent::class, true, _wpcc('Category data filters'))
                ->triggerEvent(CategoryDataReadyEvent::class)

                // Remove invalid URLs. The URLs might be invalid after the filters are applied, because the user might have
                // made changes that invalidate the URL.
                ->removeInvalidUrls()
                ->maybeNotify();                    // Notify about empty values

        } catch (Exception $e) {
            // Catch any error here and log them.
            Informer::addInfo($e->getMessage())->setException($e)->addAsLog();
            return null;
        }

        $this->doActionAfterPrepared();
        return $this->getCategoryData();
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Prepare {@link crawler} by applying find-replace rules, removing unnecessary elements, and resolving relative
     * URLs.
     *
     * @return CategoryBot
     * @since 1.11.0
     */
    private function prepareCrawler(): self {
        if (!$this->crawler) return $this;

        // Make initial replacements
        $findAndReplacesForFirstLoad = $this->getSetting(SettingKey::CATEGORY_FIND_REPLACE_FIRST_LOAD, null);

        $this->crawler = $this->makeInitialReplacements($this->crawler, $findAndReplacesForFirstLoad);

        // Apply HTML manipulations
        $this->applyFindAndReplaceInElementAttributes($this->crawler,   SettingKey::CATEGORY_FIND_REPLACE_ELEMENT_ATTRIBUTES);
        $this->applyExchangeElementAttributeValues($this->crawler,      SettingKey::CATEGORY_EXCHANGE_ELEMENT_ATTRIBUTES);
        $this->applyRemoveElementAttributes($this->crawler,             SettingKey::CATEGORY_REMOVE_ELEMENT_ATTRIBUTES);
        $this->applyFindAndReplaceInElementHTML($this->crawler,         SettingKey::CATEGORY_FIND_REPLACE_ELEMENT_HTML);

        // Clear the crawler from unnecessary category elements
        $categoryUnnecessaryElementSelectors = $this->getSetting(SettingKey::CATEGORY_UNNECESSARY_ELEMENT_SELECTORS);
        $this->removeElementsFromCrawler($this->crawler, $categoryUnnecessaryElementSelectors);

        // Resolve relative URLs
        $this->resolveRelativeUrls($this->crawler, $this->url);

        return $this;
    }

    /**
     * Prepare post URLs
     * @return CategoryBot
     */
    private function preparePostUrls(): self {
        $postUrlData = $this->extractValuesForSelectorSetting(
            $this->crawler,
            SettingKey::CATEGORY_POST_LINK_SELECTORS,
            'href',
            'url'
        );
        if (!$postUrlData) return $this;

        // Flatten the array with depth of 1 because the return value is array of arrays of items. We need array of items.
        $postUrlData = Arr::flatten($postUrlData, 1);

        // Make relative URLs direct
        $urlList = new PostUrlList();
        foreach($postUrlData as $mPostUrl) {
            $urlItem = PostUrl::fromArray($mPostUrl);
            if (!$urlItem) continue;

            $urlList->addItem($urlItem);

            try {
                $urlItem->setUrl($this->resolveUrl($urlItem->getUrl()));

            } catch (Exception $e) {
                // Nothing to do here. This is a very unlikely situation, since $url exists when this method
                // is called.
                Informer::addError(_wpcc('URL could not be resolved') . ' - ' . $mPostUrl["data"])->addAsLog();
            }
        }

        if ($this->categoryData) {
            $this->categoryData->setPostUrlList($urlList);
        }

        return $this;
    }

    /**
     * Prepare thumbnails
     */
    private function prepareThumbnails(): void {
        $categorySaveThumbnails = $this->getSetting(SettingKey::CATEGORY_POST_SAVE_THUMBNAILS);
        if(!$categorySaveThumbnails) return;

        // Extract the thumbnail data and assign it to the category data
        $thumbnailData = $this->assignThumbnailData();
        if(!$thumbnailData || !$this->categoryData) return;

        $postUrlData = $this->categoryData->getPostUrlList()->toArray();

        // Match thumbnails with post URLs
        // Combine URL and thumbnail data and sort the combined array ascending by start position
        $postDataCombined = array_merge($thumbnailData, $postUrlData);

        // Sort the combined data and reset the array keys
        $postDataCombined = array_values(Utils::array_msort($postDataCombined, ["start" => SORT_ASC]));

        $isLinkBeforeThumb = $this->getSettingForCheckbox(SettingKey::CATEGORY_POST_IS_LINK_BEFORE_THUMBNAIL);

        $thumbnailHolder = null;
        for($i = 0; $i < sizeof($postDataCombined); $i++) {
            $thumbnailHolder = null;

            // If the type is not "url", continue with the next one.
            if ($postDataCombined[$i]["type"] != "url") continue;

            // Check if the url has a thumbnail
            // If the link comes BEFORE the thumb
            if($isLinkBeforeThumb) {
                if(isset($postDataCombined[$i + 1]) && $postDataCombined[$i + 1]["type"] == "thumbnail") {
                    $thumbnailHolder = $postDataCombined[$i + 1]["data"];
                }

            } else {
                // If the link comes AFTER the thumb
                if($i !== 0 && $postDataCombined[$i - 1]["type"] == "thumbnail") {
                    $thumbnailHolder = $postDataCombined[$i - 1]["data"];
                }
            }

            // If the thumbnail is not found, continue with the next one.
            if($thumbnailHolder === null) continue;

            // Add the thumbnail to the postUrlData
            foreach($postUrlData as &$mUrlData) {
                if(
                    $mUrlData["data"]  == $postDataCombined[$i]["data"]  &&
                    $mUrlData["start"] == $postDataCombined[$i]["start"] &&
                    $mUrlData["end"]   == $postDataCombined[$i]["end"]
                ) {
                    $mUrlData["thumbnail"] = $thumbnailHolder;
                    break;
                }
            }
        }

        $newUrlList = new PostUrlList();
        foreach($postUrlData as $data) {
            $urlItem = PostUrl::fromArray($data);
            if (!$urlItem) continue;

            $newUrlList->addItem($urlItem);
        }

        $this->categoryData->setPostUrlList($newUrlList);
    }

    /**
     * Extracts the thumbnails from the crawler and assigns them to {@link categoryData} such that the thumbnails can be
     * reached by {@link CategoryData::getThumbnails()}
     *
     * @return array The thumbnail data
     * @since 1.11.0
     */
    private function assignThumbnailData(): array {
        // Invalidate the existing thumbnails if there is any
        if ($this->categoryData) {
            $this->categoryData->setThumbnails([]);
        }

        // Get thumbnail URLs
        $categoryPostThumbnailSelectors = $this->getSetting(SettingKey::CATEGORY_POST_THUMBNAIL_SELECTORS);
        $findAndReplacesForThumbnailUrl = $this->getSetting(SettingKey::CATEGORY_FIND_REPLACE_THUMBNAIL_URL);

        $thumbnailData = null;
        foreach($categoryPostThumbnailSelectors as $selectorData) {
            $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
            if (!$selector) continue;

            $attr = Utils::array_get($selectorData, SettingInnerKey::ATTRIBUTE);
            if (!$attr) $attr = 'src';

            $thumbnailData = $this->extractData($this->crawler, $selectorData, $attr, "thumbnail", false, true);
            if (!is_array($thumbnailData)) {
                $thumbnailData = null;
                continue;
            }

            /** @var array $thumbnailData */
            // Make replacements
            if(!empty($thumbnailData) && !empty($findAndReplacesForThumbnailUrl)) {
                foreach($thumbnailData as &$mThumbnailData) {
                    $mThumbnailData["data"] = $this->findAndReplace($findAndReplacesForThumbnailUrl, $mThumbnailData["data"]);
                }
            }

            // Make relative URLs direct
            foreach($thumbnailData as &$nThumbnailData) {
                try {
                    $nThumbnailData["data"] = $this->resolveUrl($nThumbnailData["data"]);
                } catch (Exception $e) {
                    // Nothing to do here. This is a very unlikely situation, since $url exists when this method
                    // is called.
                    Informer::addError(_wpcc('URL could not be resolved') . ' - ' . $nThumbnailData["data"])->addAsLog();
                }
            }

            if ($this->categoryData) {
                $this->categoryData->setThumbnails($thumbnailData);
            }

            break;
        }

        return $thumbnailData ?: [];
    }

    /**
     * Reverse the order of collected URLs if "collect in reverse" setting is set
     *
     * @return CategoryBot
     * @since 1.11.0
     */
    private function maybeReverseUrlOrder(): self {
        $categoryUrlsInReverse = $this->getSetting(SettingKey::CATEGORY_COLLECT_IN_REVERSE_ORDER);
        if($categoryUrlsInReverse && $this->categoryData) {
            $this->categoryData->reversePostUrls();
        }

        return $this;
    }

    /**
     * Prepare next page URL
     * @return CategoryBot
     */
    private function prepareNextPageUrl(): self {
        $nextPageUrl = $this->extractValuesForSelectorSetting(
            $this->crawler,
            SettingKey::CATEGORY_NEXT_PAGE_SELECTORS,
            'href',
            false,
            true
        );
        if (!$nextPageUrl) return $this;

        try {
            $nextPageUrl = $this->resolveUrl($nextPageUrl);

        } catch (Exception $e) {
            // Nothing to do here. This is a very unlikely situation, since $url exists when this method
            // is called.
            Informer::addError(_wpcc('URL could not be resolved') . ' - ' . $nextPageUrl)->addAsLog();
        }

        if ($this->categoryData) {
            $this->categoryData->setNextPageUrl($nextPageUrl);
        }

        return $this;
    }

    /**
     * Removes the invalid URLs from the {@link categoryData}
     *
     * @return $this
     * @since 1.11.0
     */
    private function removeInvalidUrls(): self {
        $urlList = $this->categoryData ? $this->categoryData->getPostUrlList() : null;
        if (!$urlList) {
            return $this;
        }

        foreach($urlList->getItems() as $k => $postUrl) {
            $url = $postUrl->getUrl();

            // If the URL is an empty string, remove it and continue with the next one.
            if ($url === '') {
                $urlList->removeItem($k);
                continue;
            }

            // The URL is not an empty string. Try to resolve it. If it cannot be resolved, remove it.
            try {
                $postUrl->setUrl($this->resolveUrl($url));

            } catch (Exception $e) {
                $urlList->removeItem($k);
                Informer::addError(_wpcc('URL could not be resolved') . ' - ' . $url)->addAsLog();
            }

        }

        return $this;
    }

    /**
     * Notify the user about the elements with empty values if there are selectors and this is not a test
     *
     * @since 1.11.0
     */
    private function maybeNotify(): void {
        // If this is a test, do not notify.
        if (WPCCrawler::isDoingGeneralTest()) return;

        // Get the selectors. If there is no selector, stop.
        $notifyWhenEmptySelectors = $this->getSetting(SettingKey::CATEGORY_NOTIFY_EMPTY_VALUE_SELECTORS, null);
        if(!$notifyWhenEmptySelectors) return;

        $crawlingUrl = $this->getCrawlingUrl();
        $crawler     = $this->crawler;
        if ($crawlingUrl === null || $crawler === null) return;

        $this->notifyUser($crawlingUrl, $crawler, $notifyWhenEmptySelectors, $this->keyLastEmptySelectorEmailDate);
    }

    /**
     * See {@link initializeFilterSetting()}
     *
     * @param string      $settingKey               See {@link initializeFilterSetting()}
     * @param string      $defaultConditionEventCls See {@link initializeFilterSetting()}
     * @param bool        $withSourceMap            True if the source map should be included
     * @param string|null $name                     See {@link initializeFilterSetting()}
     * @return CategoryBot
     * @since 1.11.0
     * @uses  initializeFilterSetting()
     */
    private function initializeFilters(string $settingKey, string $defaultConditionEventCls, bool $withSourceMap = true,
                                       ?string $name = null): self {
        $dataSourceMap = $withSourceMap ? $this->getDataSourceMap() : [];
        $this->initializeFilterSetting(
            $settingKey,
            $defaultConditionEventCls,
            new CategoryPageFilterDependencyProvider($this, $dataSourceMap),
            $name
        );

        return $this;
    }

    /**
     * Get data source map containing category data
     *
     * @return array In the same structure as {@link FilterDependencyProvider::dataSourceMap}
     * @since 1.11.0
     */
    private function getDataSourceMap(): array {
        return [
            Environment::defaultCategoryIdentifier() => $this->getCategoryData(),
        ];
    }

    /**
     * Set current category URL
     *
     * @param string|null $url
     * @since 1.8.0
     */
    private function setUrl(?string $url): void {
        $this->url = $url;
        $this->uri = null;
    }

    /*
     *
     */

    /**
     * @return string|null Returns {@link url}, last crawled or being crawled category URL.
     * @since 1.8.0
     * @since 1.11.0 Renamed from getUrl to getCrawlingUrl
     */
    public function getCrawlingUrl(): ?string {
        return $this->url;
    }

    public function getResponseHttpStatusCode(): ?int {
        return $this->responseHttpStatusCode;
    }

    /**
     * @return CategoryData|null See {@link categoryData}
     * @since 1.11.0
     */
    public function getCategoryData(): ?CategoryData {
        return $this->categoryData;
    }

    /**
     * Resolves a URL by considering {@link url} as base URL.
     *
     * @param string $relativeUrl Relative or full URL that will be resolved against the current category URL.
     * @return string The given URL that is resolved using {@link url}
     * @throws Exception If category URL that will be used to resolve the given URL does not exist.
     * @since 1.8.0
     * @see   Utils::resolveUrl()
     */
    public function resolveUrl(string $relativeUrl): string {
        if (!$this->url) {
            throw new Exception("Category URL does not exist.");
        }

        // If there is no post URI, create it.
        if ($this->uri === null) {
            $this->uri = new Uri($this->url);
        }

        return Utils::resolveUrl($this->uri, $relativeUrl);
    }

    /*
     *
     */

    public function getCrawler(): ?Crawler {
        return $this->crawler;
    }

    public function setCrawler(?Crawler $crawler): void {
        $this->crawler = $crawler;
    }

    /*
     * ACTIONS AND FILTERS
     */

    /**
     * @return CategoryBot
     * @since 1.11.0
     */
    private function doActionBeforeRetrieve(): self {
        /**
         * Fires just before the source code of a category page is retrieved from the target site.
         *
         * @param int|null    $siteId ID of the site
         * @param string|null $url    URL of the target category page
         * @param CategoryBot $bot    The bot itself
         * @since 1.6.3
         */
        do_action('wpcc/category/source-code/before_retrieve', $this->getSiteId(), $this->getCrawlingUrl(), $this);

        return $this;
    }

    /**
     * @return CategoryBot
     * @since 1.11.0
     */
    private function doActionAfterRetrieve(): self {
        /**
         * Fires just after the source code of a category page is retrieved from the target site.
         *
         * @param int|null     $siteId  ID of the site
         * @param string|null  $url     URL of the category
         * @param CategoryBot  $bot     The bot itself
         * @param Crawler|null $crawler Crawler containing raw, non-manipulated source code of the target category
         * @since 1.6.3
         * @since 1.11.0 Change the prefix of the action's tag from "wpcc/post/" to "wpcc/category/"
         */
        do_action('wpcc/category/source-code/after_retrieve',
            $this->getSiteId(),
            $this->url,
            $this,
            $this->crawler
        );

        return $this;
    }

    /**
     * @return CategoryBot
     * @since 1.11.0
     */
    private function applyFilterCrawlerRaw(): self {
        /**
         * Modify the raw crawler that contains source code of the target category page
         *
         * @param Crawler|null $crawler Crawler containing raw, non-manipulated source code of the target category page
         * @param int|null     $siteId  ID of the site
         * @param string|null  $postUrl URL of the category page
         * @param CategoryBot  $bot     The bot itself
         *
         * @return Crawler Modified crawler
         * @since 1.6.3
         */
        $this->crawler = apply_filters('wpcc/category/crawler/raw',
            $this->crawler,
            $this->getSiteId(),
            $this->url,
            $this
        );

        return $this;
    }

    /**
     * @return CategoryBot
     * @since 1.11.0
     */
    private function applyFilterCrawlerPrepared(): self {
        /**
         * Modify the prepared crawler that contains source code of the target category page. At this point, the crawler
         * was manipulated. Unnecessary elements were removed, find-and-replace options were applied, etc.
         *
         * @param Crawler|null $crawler Crawler containing manipulated source code of the target category page
         * @param int|null     $siteId  ID of the site
         * @param string|null  $postUrl URL of the category page
         * @param CategoryBot  $bot     The bot itself
         *
         * @return Crawler Modified crawler
         * @since 1.6.3
         */
        $this->crawler = apply_filters('wpcc/category/crawler/prepared',
            $this->crawler,
            $this->getSiteId(),
            $this->getCrawlingUrl(),
            $this
        );

        return $this;
    }

    /**
     * @return CategoryBot
     * @since 1.11.0
     */
    private function applyFilterCategoryData(): self {
        /**
         * Modify the prepared CategoryData object, which stores all the required data retrieved from the target site.
         *
         * @param CategoryData|null $categoryData Prepared {@link CategoryData} object
         * @param int|null          $siteId       ID of the site
         * @param string|null       $postUrl      URL of the category page
         * @param CategoryBot       $bot          The bot itself
         * @param Crawler|null      $crawler      Crawler containing manipulated source code of the target category page
         *
         * @return CategoryData Modified CategoryData
         * @since 1.6.3
         */
        $this->categoryData = apply_filters('wpcc/category/category-data',
            $this->getCategoryData(),
            $this->getSiteId(),
            $this->getCrawlingUrl(),
            $this,
            $this->crawler
        );

        return $this;
    }

    /**
     * @return CategoryBot
     * @since 1.11.0
     */
    private function doActionAfterPrepared(): self {
        /**
         * Fires just after the category data is prepared according to the settings. All of the necessary changes were made
         * to the category data, such as removal of unnecessary elements and replacements.
         *
         * @param int|null          $siteId       ID of the site
         * @param string|null       $url          URL of the target category page
         * @param CategoryBot       $bot          The bot itself
         * @param CategoryData|null $categoryData The data retrieved from the target site by using the settings
         *                                        configured by the user.
         * @param Crawler|null      $crawler      Crawler containing the target category's source code. The crawler was
         *                                        manipulated according to the settings.
         * @since 1.6.3
         */
        do_action('wpcc/category/data/after_prepared',
            $this->getSiteId(),
            $this->getCrawlingUrl(),
            $this,
            $this->getCategoryData(),
            $this->crawler
        );

        return $this;
    }

}
