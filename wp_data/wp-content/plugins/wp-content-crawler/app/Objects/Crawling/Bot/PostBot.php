<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 23:50
 */

namespace WPCCrawler\Objects\Crawling\Bot;


use Exception;
use GuzzleHttp\Psr7\Uri;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Environment;
use WPCCrawler\Objects\Crawling\Data\PostData;
use WPCCrawler\Objects\Crawling\Data\PostSaverData;
use WPCCrawler\Objects\Crawling\Interfaces\MakesCrawlRequest;
use WPCCrawler\Objects\Crawling\Preparers\BotConvenienceFindReplacePreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostBotPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostCategoryPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostContentsPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostCreatedDatePreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostCustomPostMetaPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostCustomTaxonomyPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostDataPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostExcerptPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostListInfoPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostMediaPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostMetaAndTagInfoPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostPaginationInfoPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostShortCodeInfoPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostSlugPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostSpinningPreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostTemplatePreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostTitlePreparer;
use WPCCrawler\Objects\Crawling\Preparers\Post\PostTranslationPreparer;
use WPCCrawler\Objects\Events\Events\AfterPostCrawlerReadyEvent;
use WPCCrawler\Objects\Events\Events\AfterPostRequestEvent;
use WPCCrawler\Objects\Events\Events\AfterSpinningEvent;
use WPCCrawler\Objects\Events\Events\AfterTranslationEvent;
use WPCCrawler\Objects\Events\Events\PostDataReadyEvent;
use WPCCrawler\Objects\Filtering\FilterDependencyProvider\FilterDependencyProvider;
use WPCCrawler\Objects\Filtering\FilterDependencyProvider\Page\PostPageFilterDependencyProvider;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Traits\ErrorTrait;
use WPCCrawler\PostDetail\PostDetailsService;
use WPCCrawler\Utils;
use WPCCrawler\WPCCrawler;

class PostBot extends AbstractBot implements MakesCrawlRequest {

    use ErrorTrait;

    /** @var Crawler|null */
    private $crawler;

    /** @var PostData|null */
    private $postData;

    /*
     *
     */

    /** @var array */
    public $combinedListData = [];

    /** @var string|null */
    private $postUrl = null;

    /** @var int|null HTTP status code of the response of the crawling request */
    private $responseHttpStatusCode = null;

    /** @var null|Uri */
    private $postUri = null;

    /** @var null|PostSaverData Data to be used to make certain choices, e.g. by filters, when crawling the URL */
    private $postSaverData = null;

    /*
     *
     */

    /** @var BotConvenienceFindReplacePreparer|null */
    private $findReplacePreparer = null;

    /** @var string */
    private $keyLastEmptySelectorEmailDate = '_last_post_empty_selector_email_sent';

    /**
     * Crawls a post and prepares the data as {@link PostData}. This method does not save the post to the database.
     *
     * @param string|null        $postUrl       A full URL
     * @param PostSaverData|null $postSaverData See {@link postSaverData}
     * @return PostData|null
     * @throws Exception See {@link triggerEvent()}
     * @since 1.11.0 $postSaverData parameter is added
     */
    public function crawlPost(?string $postUrl, ?PostSaverData $postSaverData): ?PostData {
        if (!$postUrl) return null;

        $this->clearErrors();

        $this->setPostUrl($postUrl);
        $this->postSaverData = $postSaverData;
        $this->postData = new PostData();

        $findAndReplacesForRawHtml          = $this->getSetting(SettingKey::POST_FIND_REPLACE_RAW_HTML);
        $postUnnecessaryElementSelectors    = $this->getSetting(SettingKey::POST_UNNECESSARY_ELEMENT_SELECTORS);

        $this->doActionBeforeRetrieve();

        $this->crawler = $this->request($postUrl, "GET", $findAndReplacesForRawHtml);

        $latestResponse = $this->getLatestResponse();
        $this->responseHttpStatusCode = $latestResponse ? $latestResponse->getStatusCode() : null;
        $this
            ->initializeFilters(SettingKey::POST_REQUEST_FILTERS, AfterPostRequestEvent::class, false, _wpcc('Post request filters'))
            ->triggerEvent(AfterPostRequestEvent::class);

        if(!$this->crawler) return null;

        /** @noinspection PhpUnhandledExceptionInspection */
        $this
            ->doActionAfterRetrieve()
            ->applyFilterCrawlerRaw()
            ->prepareCrawler()                                      // Prepare the crawler by applying HTML manipulations and resolving relative URLs
            ->applyPreparer(PostPaginationInfoPreparer::class)  // Prepare pagination info
        ;

        // Clear the crawler from unnecessary post elements
        $this->removeElementsFromCrawler($this->crawler, $postUnnecessaryElementSelectors);

        $this->initializeFilters(SettingKey::POST_PAGE_FILTERS, AfterPostCrawlerReadyEvent::class, false, _wpcc('Post page filters'));
        $this->triggerEvent(AfterPostCrawlerReadyEvent::class);

        $this->applyFilterCrawlerPrepared();

        /*
         * PREPARE
         */

        $this->initializeFilters(SettingKey::POST_DATA_FILTERS, PostDataReadyEvent::class, true, _wpcc('Post data filters'));

        /** @noinspection PhpUnhandledExceptionInspection */
        $this
            ->applyPreparer(PostTitlePreparer::class)               // Post title
            ->applyPreparer(PostSlugPreparer::class)                // Post slug
            ->applyPreparer(PostExcerptPreparer::class)             // Post excerpt
            ->applyPreparer(PostContentsPreparer::class)            // Post contents
            ->applyPreparer(PostCategoryPreparer::class)            // Post categories
            ->applyPreparer(PostCreatedDatePreparer::class)         // Post date
            ->applyPreparer(PostShortCodeInfoPreparer::class)       // Custom short code contents
            ->applyPreparer(PostListInfoPreparer::class)            // List items
            ->applyPreparer(PostMetaAndTagInfoPreparer::class)      // Post tags and meta info
            ->applyPreparer(PostCustomPostMetaPreparer::class)      // Post meta
            ->applyPreparer(PostCustomTaxonomyPreparer::class)      // Post taxonomies
            ->applyPreparer(PostMediaPreparer::class)               // Post media. This removes gallery images from the source code.
            ->preparePostDetails()                                      // Prepare the registered post details
            ->applyPreparer(PostTemplatePreparer::class)            // Post templates. Insert main data into template

            ->applyPreparer(PostDataPreparer::class)                // Changes that should be made to all parts of the post, such as removing empty HTML tags.
            ->triggerEvent(PostDataReadyEvent::class)

            ->applyPreparer(PostTranslationPreparer::class)         // Translate
            ->triggerEvent(AfterTranslationEvent::class)

            ->applyPreparer(PostSpinningPreparer::class)            // Spin
            ->triggerEvent(AfterSpinningEvent::class)
        ;

        /* END PREPARATION */

        $this
            ->applyFilterPostData()
            ->maybeNotify()                     // Notify
            ->doActionPostDataAfterPrepared()
        ;

        return $this->postData;
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * See {@link initializeFilterSetting()}
     *
     * @param string      $settingKey               See {@link initializeFilterSetting()}
     * @param string      $defaultConditionEventCls See {@link initializeFilterSetting()}
     * @param bool        $withSourceMap            True if the source map should be included
     * @param string|null $name                     See {@link initializeFilterSetting()}
     * @return PostBot
     * @since 1.11.0
     * @uses  initializeFilterSetting()
     */
    private function initializeFilters(string $settingKey, string $defaultConditionEventCls, bool $withSourceMap = true,
                                       ?string $name = null): self {
        $dataSourceMap = $withSourceMap ? $this->getDataSourceMap() : [];
        $this->initializeFilterSetting(
            $settingKey,
            $defaultConditionEventCls,
            new PostPageFilterDependencyProvider($this, $dataSourceMap),
            $name
        );

        return $this;
    }

    /**
     * Get data source map containing post data and post detail data
     *
     * @return array In the same structure as {@link FilterDependencyProvider::dataSourceMap}
     * @since 1.11.0
     */
    private function getDataSourceMap(): array {
        // Create the data source map. Initialize it with the post data.
        $dataSourceMap = [
            Environment::defaultPostIdentifier() => $this->getPostData(),
        ];

        // Add the post detail factories' data as well
        $factories = PostDetailsService::getInstance()->getTransformableFactories($this->getSettingsImpl());
        foreach($factories as $factory) {
            $dataSourceMap[$factory->getIdentifier()] = $factory->getDetailData();
        }

        return $dataSourceMap;
    }

    /**
     * @param string $cls Name of a class that extends {@link AbstractPostBotPreparer}.
     * @return PostBot
     *
     * @since 1.9.0
     * @since 1.11.0 Type declaration of $cls parameter is added
     * @throws Exception If $cls is not a child of {@link AbstractPostBotPreparer}.
     */
    private function applyPreparer(string $cls): self {
        $instance = $this->createPreparer($cls);
        if (!is_a($instance, AbstractPostBotPreparer::class)) {
            throw new Exception(sprintf('%1$s must be a child of %2$s', $cls, AbstractPostBotPreparer::class));
        }

        /** @var AbstractPostBotPreparer $instance */
        $instance->prepare();

        return $this;
    }

    /**
     * Create a new preparer instance with its class name
     *
     * @param string $cls Name of a class that extends {@link AbstractPostBotPreparer}.
     * @return AbstractPostBotPreparer|object New instance of the preparer with the specified class
     * @since 1.11.0
     */
    protected function createPreparer(string $cls) {
        return new $cls($this);
    }

    /**
     * Prepares the registered post details
     *
     * @return PostBot
     * @since 1.9.0
     */
    private function preparePostDetails(): self {
        PostDetailsService::getInstance()->preparePostDetails($this);
        return $this;
    }

    /**
     * Prepares the crawl by applying HTML manipulations and resolving relative URLs
     *
     * @return PostBot
     * @since 1.9.0
     */
    private function prepareCrawler(): self {
        if (!$this->crawler) return $this;

        $findAndReplacesForFirstLoad = $this->getSetting(SettingKey::POST_FIND_REPLACE_FIRST_LOAD, null);

        // Make initial replacements
        $this->crawler = $this->makeInitialReplacements($this->crawler, $findAndReplacesForFirstLoad, true);

        // Apply HTML manipulations
        $this->applyFindAndReplaceInElementAttributes($this->crawler,   SettingKey::POST_FIND_REPLACE_ELEMENT_ATTRIBUTES);
        $this->applyExchangeElementAttributeValues($this->crawler,      SettingKey::POST_EXCHANGE_ELEMENT_ATTRIBUTES);
        $this->applyRemoveElementAttributes($this->crawler,             SettingKey::POST_REMOVE_ELEMENT_ATTRIBUTES);
        $this->applyFindAndReplaceInElementHTML($this->crawler,         SettingKey::POST_FIND_REPLACE_ELEMENT_HTML);

        // Resolve relative URLs
        $this->resolveRelativeUrls($this->crawler, $this->getPostUrl());
        return $this;
    }

    /**
     * Sets {@link $postUrl}
     *
     * @param string|null $postUrl
     * @since 1.8.0
     * @since 1.11.0 Type declaration of $postUrl parameter is added
     */
    private function setPostUrl(?string $postUrl): void {
        $this->postUrl = $postUrl;
        $this->postUri = null;
    }

    /**
     * Notify the user for the elements that have empty values using the settings. This will notify the user only if
     * this is not called during a test.
     *
     * @return PostBot
     * @since 1.9.0
     */
    private function maybeNotify(): self {
        // Do not notify if this is a test.
        if (WPCCrawler::isDoingGeneralTest()) return $this;

        $notifyWhenEmptySelectors = $this->getSetting(SettingKey::POST_NOTIFY_EMPTY_VALUE_SELECTORS);
        if (!$notifyWhenEmptySelectors || !$this->postUrl || !$this->crawler) return $this;

        $this->notifyUser($this->postUrl, $this->crawler, $notifyWhenEmptySelectors, $this->keyLastEmptySelectorEmailDate);
        return $this;
    }

    /*
     * PUBLIC HELPERS
     */

    /**
     * Prepare find-and-replaces by adding config to the supplied find-and-replace array, such as link removal config.
     *
     * @param array|null $findAndReplaces An array of find and replace options. See
     *                                    {@link FindAndReplaceTrait::findAndReplace} to learn more about this array.
     * @return array
     * @uses BotConvenienceFindReplacePreparer::prepare()
     */
    public function prepareFindAndReplaces(?array $findAndReplaces): array {
        // If the supplied parameter is not an array, return an empty array.
        if (!is_array($findAndReplaces)) return [];

        // If the preparer does not exist, create it.
        if (!$this->findReplacePreparer) {
            $this->findReplacePreparer = new BotConvenienceFindReplacePreparer($this);
        }

        // Add the config to the given array.
        return array_merge($findAndReplaces, $this->findReplacePreparer->prepare());
    }

    /*
     * PUBLIC GETTERS AND SETTERS
     */

    /**
     * @return Crawler|null
     */
    public function getCrawler(): ?Crawler {
        return $this->crawler;
    }

    public function setCrawler(?Crawler $crawler): void {
        $this->crawler = $crawler;
    }

    /**
     * @return PostData
     */
    public function getPostData(): PostData {
        if ($this->postData === null) {
            $this->postData = new PostData();
        }

        return $this->postData;
    }

    /**
     * @param PostData $postData
     * @since 1.11.0 Type declaration of $postData is added.
     */
    public function setPostData(PostData $postData): void {
        $this->postData = $postData;
    }

    /**
     * Get the URL of latest crawled or being crawled post.
     *
     * @return string|null
     */
    public function getPostUrl(): ?string {
        return $this->postUrl;
    }

    public function getCrawlingUrl(): ?string {
        return $this->getPostUrl();
    }

    public function getResponseHttpStatusCode(): ?int {
        return $this->responseHttpStatusCode;
    }

    /**
     * @return PostSaverData|null See {@link postSaverData}
     * @since 1.11.0
     */
    public function getPostSaverData(): ?PostSaverData {
        return $this->postSaverData;
    }

    /**
     * Resolves a URL by considering {@link $postUrl} as base URL.
     *
     * @param string $relativeUrl Relative or full URL that will be resolved against the current post URL.
     * @return string The given URL that is resolved using {@link $postUrl}
     * @see   PostBot::getPostUrl()
     * @see   Utils::resolveUrl()
     * @since 1.8.0
     * @throws Exception If post URL that will be used to resolve the given URL does not exist.
     */
    public function resolveUrl($relativeUrl): string {
        if (!$this->postUrl) {
            throw new Exception("Post URL does not exist.");
        }

        // If there is no post URI, create it.
        if ($this->postUri === null) {
            $this->postUri = new Uri($this->postUrl);
        }

        return Utils::resolveUrl($this->postUri, $relativeUrl);
    }

    /*
     * ACTIONS AND FILTERS
     */

    /**
     * @return PostBot
     * @since 1.9.0
     */
    private function doActionBeforeRetrieve(): self {
        /**
         * Fires just before the source code of a post page is retrieved from the target site.
         *
         * @param int|null    $siteId  ID of the site
         * @param string|null $postUrl URL of the post
         * @param PostBot     $bot     The bot itself
         * @since 1.6.3
         */
        do_action('wpcc/post/source-code/before_retrieve', $this->getSiteId(), $this->postUrl, $this);
        return $this;
    }

    /**
     * @return PostBot
     * @since 1.9.0
     */
    private function doActionAfterRetrieve(): self {
        /**
         * Fires just after the source code of a post page is retrieved from the target site.
         *
         * @param int|null     $siteId  ID of the site
         * @param string|null  $postUrl URL of the post
         * @param PostBot      $bot     The bot itself
         * @param Crawler|null $crawler Crawler containing raw, unmanipulated source code of the target post
         * @since 1.6.3
         */
        do_action('wpcc/post/source-code/after_retrieve', $this->getSiteId(), $this->postUrl, $this, $this->crawler);
        return $this;
    }

    /**
     * @return PostBot
     * @since 1.9.0
     */
    private function applyFilterCrawlerRaw(): self {
        /**
         * Modify the raw crawler that contains source code of the target post page
         *
         * @param Crawler|null $crawler Crawler containing raw, unmanipulated source code of the target post
         * @param int|null     $siteId  ID of the site
         * @param string|null  $postUrl URL of the post
         * @param PostBot      $bot     The bot itself
         *
         * @return Crawler Modified crawler
         * @since 1.6.3
         */
        $this->crawler = apply_filters('wpcc/post/crawler/raw', $this->crawler, $this->getSiteId(), $this->postUrl, $this);
        return $this;
    }

    /**
     * @return PostBot
     * @since 1.9.0
     */
    private function applyFilterCrawlerPrepared(): self {
        /**
         * Modify the prepared crawler that contains source code of the target post page. At this point, the crawler was
         * manipulated. Unnecessary elements were removed, find-and-replace options were applied, etc.
         *
         * @param Crawler|null $crawler Crawler containing manipulated source code of the target post
         * @param int|null     $siteId  ID of the site
         * @param string|null  $postUrl URL of the post
         * @param PostBot      $bot     The bot itself
         *
         * @return Crawler Modified crawler
         * @since 1.6.3
         */
        $this->crawler = apply_filters('wpcc/post/crawler/prepared', $this->crawler, $this->getSiteId(), $this->postUrl, $this);
        return $this;
    }

    /**
     * @return PostBot
     * @since 1.9.0
     */
    private function applyFilterPostData(): self {
        /**
         * Modify the prepared PostData object, which stores all the required data retrieved from the target site.
         *
         * @param PostData|null $postData Prepared PostData object
         * @param int|null      $siteId   ID of the site
         * @param string|null   $postUrl  URL of the post
         * @param PostBot       $bot      The bot itself
         * @param Crawler|null  $crawler  Crawler containing manipulated source code of the target post
         * @return PostData Modified {@link PostData}
         * @since 1.6.3
         */
        $this->postData = apply_filters('wpcc/post/post-data', $this->postData, $this->getSiteId(), $this->postUrl, $this, $this->crawler);
        return $this;
    }

    /**
     * @return PostBot
     * @since 1.9.0
     */
    private function doActionPostDataAfterPrepared(): self {
        /**
         * Fires just after the post data is prepared according to the settings. All of the necessary changes were made
         * to the post data, such as removal of unnecessary elements and replacements.
         *
         * @param int|null      $siteId   ID of the site
         * @param string|null   $postUrl  URL of the post
         * @param PostBot       $bot      The bot itself
         * @param PostData|null $postData The data retrieved from the target site by using the settings configured by
         *                                the user.
         * @param Crawler|null  $crawler  Crawler containing the target post page's source code. The crawler was
         *                                manipulated according to the settings.
         * @since 1.6.3
         */
        do_action('wpcc/post/data/after_prepared', $this->getSiteId(), $this->postUrl, $this, $this->postData, $this->crawler);
        return $this;
    }
}
