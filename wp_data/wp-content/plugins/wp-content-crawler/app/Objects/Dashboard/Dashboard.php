<?php /** @noinspection SqlRedundantOrderingDirection */
/** @noinspection SqlResolve */
/** @noinspection SqlNoDataSourceInspection */

/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/02/17
 * Time: 15:21
 */

namespace WPCCrawler\Objects\Dashboard;


use WP_Post;
use WP_Query;
use WPCCrawler\Environment;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Utils;

class Dashboard {

    /** @var int Number of last posts that will be shown at max */
    private $lastPostsLimit = 10;

    /** @var string Used for formatting dates in MySQL date format. */
    private $dateFormat = "Y-m-d H:i:s";

    /*
     *
     */

    /**
     * @var array Stores CRON event keys as array's keys. The values are the descriptions of the events recurrence
     *      frequency. Such as ["wpcc_event_collect_urls" => "Every 10 minutes", ...]
     */
    private $cronIntervalDescriptions = [];

    /*
     *
     */

    /** @var DashboardPost[] Stores last crawled posts */
    private $lastCrawledPosts = [];

    /** @var DashboardPost[] Stores last recrawled posts */
    private $lastRecrawledPosts = [];

    /** @var DashboardUrlTuple[] An array of URL tuple objects, which were added to the queue last */
    private $lastUrlsInQueue = [];

    /** @var DashboardUrlTuple[] An array of URL tuple objects, which were set as deleted last */
    private $lastUrlsMarkedAsDeleted = [];

    /*
     *
     */

    /** @var int Number of URLs waiting to be saved */
    private $totalUrlsInQueue = 0;

    /** @var int Number of URLs in the queue which are added today */
    private $totalUrlsInQueueAddedToday = 0;

    /** @var int Total number of posts saved */
    private $totalSavedPosts = 0;

    /** @var int Total number of posts saved today */
    private $totalSavedPostsToday = 0;

    /** @var int Total number of posts recrawled */
    private $totalRecrawledPosts = 0;

    /** @var int Total number of posts recrawled today */
    private $totalRecrawledPostsToday = 0;

    /** @var int Total number of recrawls */
    private $totalRecrawlCount = 0;

    /** @var int Total number of deleted posts */
    private $totalDeletedPosts = 0;

    /** @var int Total number of posts deleted today */
    private $totalDeletedPostsToday = 0;

    /*
     *
     */

    /** @var null|string Date at which the last URL collection was performed */
    private $lastUrlCollectionDate = null;

    /** @var null|string Saving date of the last crawled post */
    private $lastPostCrawlDate = null;

    /** @var null|string Recrawling date of the last recrawled post */
    private $lastPostRecrawlDate = null;

    /** @var null|string The last time at which the post-deleting event run */
    private $lastPostDeleteDate = null;

    /*
     *
     */

    /** @var null|string */
    private $nextURLCollectionDate = null;

    /** @var null|string */
    private $nextPostCrawlDate = null;

    /** @var null|string */
    private $nextPostRecrawlDate = null;

    /** @var null|string */
    private $nextPostDeleteDate = null;

    /*
     *
     */

    /** @var null|WP_Post */
    private $nextUrlCollectionSite = null;

    /** @var null|WP_Post */
    private $nextPostCrawlSite = null;

    /** @var null|WP_Post */
    private $nextPostRecrawlSite = null;

    /** @var null|WP_Post */
    private $nextPostDeleteSite = null;

    /*
     *
     */

    /**
     * @var DashboardUrlTuple[] Stores the URLs currently being crawled. */
    private $urlsCurrentlyBeingCrawled = [];

    /** @var DashboardPost[] Stores the posts currently being recrawled. */
    private $postsCurrentlyBeingSaved = [];

    /*
     *
     */

    /**
     * @var DashboardSite[] Stores sites active for either scheduling or recrawling with a few other data such as last
     *      URL collection, last post crawl, last post recrawl, number of posts crawled and number of posts in the queue.
     *      Keys for these are <b>countSaved, countQueue, countRecrawled, lastCheckedAt, lastCrawledAt, lastRecrawledAt,
     *      activeScheduling, activeRecrawling, countQueueToday, countSavedToday, countRecrawledToday</b>
     */
    private $activeSites = [];

    public function __construct() {
        $this->init();
    }

    /**
     * Initializes the class by preparing local variables.
     */
    private function init(): void {
        $this->initCronIntervalDescriptions();
        $this->initLastSaves();
        $this->initTotals();
        $this->initLastCronRunInfo();
        $this->initNextCronRunInfo();
        $this->initCurrentlyBeingDone();
        $this->initActiveSites();
    }

    /*
     * INITIALIZER METHODS
     */

    private function initCronIntervalDescriptions(): void {
        $cronArr = _get_cron_array() ?: [];
        $schedulingService = Factory::schedulingService();

        /**
         * Stores the target events. Each key in this array will be tried to be populated with its description.
         */
        $targetEvents = [
            $schedulingService->eventCollectUrls => null,
            $schedulingService->eventCrawlPost   => null,
            $schedulingService->eventRecrawlPost => null,
            $schedulingService->eventDeletePosts => null,
        ];

        $intervals = $schedulingService->getIntervals();
        $remaining = sizeof($targetEvents);
        $targetEventKeys = array_keys($targetEvents);

        // CRON array is a little bit deep. So let's get started.
        // Each key of CRON array is an epoch value. The values for these keys are arrays.
        foreach($cronArr as $epoch => $valArr) {
            if($remaining <= 0) break;

            // Each array stores event keys as keys, and an array as value.
            foreach($valArr as $eventKey => $eventData) {
                if($remaining <= 0) break 2;

                // Now, we can check if this event is one of the events we are looking for. If so and its description
                // was not retrieved before, let's get its description.
                if(in_array($eventKey, $targetEventKeys) && $targetEvents[$eventKey] === null) {

                    // Each data array's keys are hashes. Its values are again an array.
                    foreach($eventData as $hash => $currentHashData) {
                        if($remaining <= 0) break 3; /** @phpstan-ignore-line */

                        // The hash data stores the interval key under 'schedule' key. Let's get it.
                        $intervalKey = $currentHashData['schedule'];

                        // Now, we can get the description from the intervals defined by the plugin.
                        if(isset($intervals[$intervalKey])) {
                            $targetEvents[$eventKey] = $intervals[$intervalKey][0];

                            // Decrease the remaining count. We keep this so that we do not keep trying after all of
                            // the descriptions are retrieved.
                            $remaining--;

                            // No need to check another item of the current event data.
                            break;
                        }
                    }

                }
            }
        }

        $this->cronIntervalDescriptions = $targetEvents;
    }

    /**
     * Initialize last crawled posts, last recrawled posts and last URLs in the queue.
     */
    private function initLastSaves(): void {
        // Prepare last crawled and last recrawled posts
        $this->lastCrawledPosts     = $this->getLastPosts('saved_at', get_option(SettingKey::WPCC_DASHBOARD_COUNT_LAST_CRAWLED_POSTS, 10));
        $this->lastRecrawledPosts   = $this->getLastPosts('recrawled_at', get_option(SettingKey::WPCC_DASHBOARD_COUNT_LAST_RECRAWLED_POSTS, 10));

        // Prepare last added URLs
        $this->lastUrlsInQueue = $this->getLastAddedUrls(get_option(SettingKey::WPCC_DASHBOARD_COUNT_LAST_URLS, 10));

        // Prepare last deleted URLs
        $this->lastUrlsMarkedAsDeleted = $this->getLastDeletedUrls(get_option(SettingKey::WPCC_DASHBOARD_COUNT_LAST_DELETED_URLS, 10));
    }

    /**
     * Initialize total URLs in queue, total saved posts, total recrawled posts and total recrawl count variables.
     */
    private function initTotals(): void {
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();

        $today = $this->getToday();

        // Total URLs in the queue
        $queryTotalUrlsInQueue = "SELECT COUNT(*) FROM {$tableUrls}
          WHERE saved_post_id IS NULL
            AND saved_at IS NULL
            AND recrawled_at IS NULL
            AND is_locked = FALSE
            AND is_saved = FALSE";
        $this->totalUrlsInQueue = $wpdb->get_var($queryTotalUrlsInQueue);
        $this->totalUrlsInQueueAddedToday = $wpdb->get_var($queryTotalUrlsInQueue . " AND created_at >= '{$today}'");

        // Total saved posts
        $queryTotalSavedPosts = "SELECT COUNT(*) FROM {$tableUrls}
          WHERE saved_post_id IS NOT NULL
            AND saved_at IS NOT NULL
            AND is_locked = FALSE
            AND is_saved = TRUE";
        $this->totalSavedPosts = $wpdb->get_var($queryTotalSavedPosts);
        $this->totalSavedPostsToday = $wpdb->get_var($queryTotalSavedPosts . " AND saved_at >= '{$today}'");

        // Total recrawled posts
        $queryTotalRecrawledPosts = "SELECT COUNT(*) FROM {$tableUrls}
          WHERE saved_post_id IS NOT NULL
            AND saved_at IS NOT NULL
            AND recrawled_at IS NOT NULL
            AND update_count > 0
            AND is_locked = FALSE
            AND is_saved = TRUE";
        $this->totalRecrawledPosts = $wpdb->get_var($queryTotalRecrawledPosts);
        $this->totalRecrawledPostsToday = $wpdb->get_var($queryTotalRecrawledPosts . " AND recrawled_at >= '{$today}'");

        // Total recrawl count
        $queryTotalRecrawlCount = "SELECT SUM(update_count) FROM {$tableUrls}
          WHERE update_count > 0";
        $this->totalRecrawlCount = $wpdb->get_var($queryTotalRecrawlCount);

        // Total deleted posts
        $queryTotalDeletedPosts = "SELECT COUNT(*) FROM {$tableUrls}
          WHERE saved_post_id IS NULL
            AND saved_at IS NOT NULL
            AND is_locked = FALSE
            AND is_saved = TRUE
            AND deleted_at IS NOT NULL";
        $this->totalDeletedPosts = $wpdb->get_var($queryTotalDeletedPosts);
        $this->totalDeletedPostsToday = $wpdb->get_var($queryTotalDeletedPosts . " AND deleted_at >= '{$today}'");
    }

    /**
     * Initialize last URL collection, last post crawl, last post recrawl, and last post delete date variables.
     */
    private function initLastCronRunInfo(): void {
        global $wpdb;

        $query = sprintf("SELECT last_url_collection, last_crawled, last_recrawled, last_deleted FROM
            (SELECT MAX(meta_value) as last_url_collection  FROM {$wpdb->postmeta} WHERE meta_key = '%1\$s') t_url_collection,
            (SELECT MAX(meta_value) as last_crawled         FROM {$wpdb->postmeta} WHERE meta_key = '%2\$s') t_crawl,
            (SELECT MAX(meta_value) as last_recrawled       FROM {$wpdb->postmeta} WHERE meta_key = '%3\$s') t_recrawl,
            (SELECT MAX(meta_value) as last_deleted         FROM {$wpdb->postmeta} WHERE meta_key = '%4\$s') t_deleted",
            SettingKey::CRON_LAST_CHECKED_AT,
            SettingKey::CRON_LAST_CRAWLED_AT,
            SettingKey::CRON_RECRAWL_LAST_CRAWLED_AT,
            SettingKey::CRON_LAST_DELETED_AT
        );

        $res = $wpdb->get_results($query, ARRAY_A);

        if($res && isset($res[0])) {
            $values = $res[0];

            $this->lastUrlCollectionDate    = Utils::array_get($values, 'last_url_collection');
            $this->lastPostCrawlDate        = Utils::array_get($values, 'last_crawled');
            $this->lastPostRecrawlDate      = Utils::array_get($values, 'last_recrawled');
            $this->lastPostDeleteDate       = Utils::array_get($values, 'last_deleted');
        }
    }

    /**
     * Initialize variables storing next CRON event dates.
     */
    private function initNextCronRunInfo(): void {
        $offsetInSeconds = $this->getGMTOffset();

        // Next CRON event dates
        $nextCollectUrls    = wp_next_scheduled(Factory::schedulingService()->eventCollectUrls);
        $nextCrawlPost      = wp_next_scheduled(Factory::schedulingService()->eventCrawlPost);
        $nextRecrawlPost    = wp_next_scheduled(Factory::schedulingService()->eventRecrawlPost);
        $nextDeletePosts    = wp_next_scheduled(Factory::schedulingService()->eventDeletePosts);

        if($nextCollectUrls)    $this->nextURLCollectionDate    = date($this->dateFormat, $nextCollectUrls + $offsetInSeconds);
        if($nextCrawlPost)      $this->nextPostCrawlDate        = date($this->dateFormat, $nextCrawlPost + $offsetInSeconds);
        if($nextRecrawlPost)    $this->nextPostRecrawlDate      = date($this->dateFormat, $nextRecrawlPost + $offsetInSeconds);
        if($nextDeletePosts)    $this->nextPostDeleteDate       = date($this->dateFormat, $nextDeletePosts + $offsetInSeconds);

        /*
         * Next sites
         */

        // Get last site IDs
        $keySiteIdLastUrlCollection = Factory::urlSaver()->optionLastCheckedSiteId;
        $keySiteIdLastPostCrawl     = Factory::postSaver()->optionLastCrawledSiteId;
        $keySiteIdLastPostRecrawl   = Factory::postSaver()->optionLastRecrawledSiteId;
        $keySiteIdLastPostDelete    = Factory::schedulingService()->optionKeyLastPostDeletedSiteId;

        // Get next site IDs using the last site IDs
        $nextSiteIdUrlCollection    = Factory::schedulingService()->getSiteIdForEvent($keySiteIdLastUrlCollection);
        $nextSiteIdPostCrawl        = Factory::schedulingService()->getSiteIdForEvent($keySiteIdLastPostCrawl);
        $nextSiteIdPostRecrawl      = Factory::schedulingService()->getSiteIdForEvent($keySiteIdLastPostRecrawl);
        $nextSiteIdPostDelete       = Factory::schedulingService()->getSiteIdForEvent($keySiteIdLastPostDelete);

        // Get the sites as WP_Post
        $sites = $this->getPosts([$nextSiteIdUrlCollection, $nextSiteIdPostCrawl, $nextSiteIdPostRecrawl, $nextSiteIdPostDelete], Environment::postType());

        // Assign the related class variables
        foreach($sites as $site) {
            if($site->ID == $nextSiteIdUrlCollection)   $this->nextUrlCollectionSite    = $site;
            if($site->ID == $nextSiteIdPostCrawl)       $this->nextPostCrawlSite        = $site;
            if($site->ID == $nextSiteIdPostRecrawl)     $this->nextPostRecrawlSite      = $site;
            if($site->ID == $nextSiteIdPostDelete)      $this->nextPostDeleteSite       = $site;
        }
    }

    /**
     * Initialize variables that store things currently being done.
     */
    private function initCurrentlyBeingDone(): void {
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();

        // URLs currently being crawled
        $resultsBeingCrawled = $wpdb->get_results("SELECT * FROM {$tableUrls}
          WHERE is_locked = TRUE
            AND saved_at IS NULL
            AND saved_post_id IS NULL
            AND recrawled_at IS NULL
          ORDER BY created_at ASC
        ");

        $this->urlsCurrentlyBeingCrawled = $this
            ->addSiteDataToUrlTuples(is_array($resultsBeingCrawled) ? $resultsBeingCrawled : [$resultsBeingCrawled]);

        // Posts currently being recrawled
        $resultsBeingRecrawled = $wpdb->get_results("SELECT * FROM {$tableUrls}
          WHERE is_locked = TRUE
            AND saved_at IS NOT NULL
            AND saved_post_id IS NOT NULL
          ORDER BY updated_at ASC
        ");

        $this->postsCurrentlyBeingSaved = $this
            ->getPostsFromUrlTuples(is_array($resultsBeingRecrawled) ? $resultsBeingRecrawled : [$resultsBeingRecrawled]);
    }

    /**
     * Initialize {@link activeSites} variable
     */
    private function initActiveSites(): void {
        // If there is no active site, do not proceed.
        $activeSites = $this->getCurrentlyActiveSitePosts();
        if(empty($activeSites)) return;

        $activeSiteIds = [];
        foreach($activeSites as $activeSite) $activeSiteIds[] = $activeSite->ID;
        $activeSiteIdsStr = implode(", ", $activeSiteIds);

        // Get today's counts
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();

        $today = $this->getToday();

        $queryTotalUrlsInQueueToday = "SELECT post_id, COUNT(*) as count FROM {$tableUrls}
          WHERE saved_post_id IS NULL
            AND saved_at IS NULL
            AND recrawled_at IS NULL
            AND is_locked = FALSE
            AND is_saved = FALSE
            AND created_at >= '{$today}'
            AND post_id IN ({$activeSiteIdsStr})
        GROUP BY post_id";

        $queryTotalCrawledPostsToday = "SELECT post_id, COUNT(*) as count FROM {$tableUrls}
          WHERE saved_post_id IS NOT NULL
            AND saved_at IS NOT NULL
            AND is_locked = FALSE
            AND is_saved = TRUE
            AND saved_at >= '{$today}'
            AND post_id IN ({$activeSiteIdsStr})
          GROUP BY post_id";

        $queryTotalRecrawledPosts = "SELECT post_id, COUNT(*) as count FROM {$tableUrls}
          WHERE saved_post_id IS NOT NULL
            AND saved_at IS NOT NULL
            AND recrawled_at IS NOT NULL
            AND update_count > 0
            AND is_locked = FALSE
            AND is_saved = TRUE
            AND post_id IN ({$activeSiteIdsStr})
            %s
          GROUP BY post_id";

        $queryTotalDeletedPostsToday = "SELECT post_id, COUNT(*) as count FROM {$tableUrls}
          WHERE saved_post_id IS NULL
            AND is_locked = FALSE
            AND deleted_at >= '{$today}'
            AND post_id IN ({$activeSiteIdsStr})
          GROUP BY post_id";

        $resultsTotalUrlsInQueueToday       = $this->preparePostIdCountDbResults($wpdb->get_results($queryTotalUrlsInQueueToday));
        $resultsTotalCrawledPostsToday      = $this->preparePostIdCountDbResults($wpdb->get_results($queryTotalCrawledPostsToday));
        $resultsTotalRecrawledPosts         = $this->preparePostIdCountDbResults($wpdb->get_results(sprintf($queryTotalRecrawledPosts, "")));
        $resultsTotalRecrawledPostsToday    = $this->preparePostIdCountDbResults($wpdb->get_results(sprintf($queryTotalRecrawledPosts, "AND recrawled_at >= '{$today}'")));
        $resultsTotalDeletedPostsToday      = $this->preparePostIdCountDbResults($wpdb->get_results($queryTotalDeletedPostsToday));

        /** @var DashboardSite[] $dashboardSites */
        $dashboardSites = [];

        // Add a few meta values and counts to each active site
        $counts = Factory::postService()->getUrlTableCounts();
        foreach($activeSites as $activeSite) {
            $postId = $activeSite->ID;
            $dashboardSites[] = (new DashboardSite($activeSite))
                ->setCountSaved(isset($counts[$postId]) && isset($counts[$postId]["count_saved"]) ? (int) $counts[$postId]["count_saved"] : 0)
                ->setCountQueue(isset($counts[$postId]) && isset($counts[$postId]["count_queue"]) ? (int) $counts[$postId]["count_queue"] : 0)
                ->setCountDeleted(isset($counts[$postId]) && isset($counts[$postId]["count_deleted"]) ? (int) $counts[$postId]["count_deleted"] : 0)
                ->setCountRecrawled(isset($resultsTotalRecrawledPosts[$postId]) && $resultsTotalRecrawledPosts[$postId] ? (int) $resultsTotalRecrawledPosts[$postId] : 0)

                ->setLastCheckedAt(get_post_meta($postId, SettingKey::CRON_LAST_CHECKED_AT, true))
                ->setLastCrawledAt(get_post_meta($postId, SettingKey::CRON_LAST_CRAWLED_AT, true))
                ->setLastRecrawledAt(get_post_meta($postId, SettingKey::CRON_RECRAWL_LAST_CRAWLED_AT, true))
                ->setLastDeletedAt(get_post_meta($postId, SettingKey::CRON_LAST_DELETED_AT, true))

                ->setActiveScheduling((bool) get_post_meta($postId, SettingKey::ACTIVE, true))
                ->setActiveRecrawling((bool) get_post_meta($postId, SettingKey::ACTIVE_RECRAWLING, true))
                ->setActiveDeleting((bool) get_post_meta($postId, SettingKey::ACTIVE_POST_DELETING, true))

                ->setCountQueueToday($resultsTotalUrlsInQueueToday[$postId]    ?? 0)
                ->setCountSavedToday($resultsTotalCrawledPostsToday[$postId]   ?? 0)
                ->setCountRecrawledToday($resultsTotalRecrawledPostsToday[$postId] ?? 0)
                ->setCountDeletedToday($resultsTotalDeletedPostsToday[$postId]   ?? 0);
        }

        $this->activeSites = $dashboardSites;
    }

    /*
     * GETTERS
     */

    /**
     * @return DashboardPost[] See {@link lastCrawledPosts}
     */
    public function getLastCrawledPosts(): array {
        return $this->lastCrawledPosts;
    }

    /**
     * @return DashboardPost[] See {@link lastRecrawledPosts}
     */
    public function getLastRecrawledPosts(): array {
        return $this->lastRecrawledPosts;
    }

    /**
     * @return DashboardUrlTuple[] See {@link lastUrlsInQueue}
     */
    public function getLastUrlsInQueue(): array {
        return $this->lastUrlsInQueue;
    }

    /**
     * @return DashboardUrlTuple[] See {@link lastUrlsMarkedAsDeleted}
     */
    public function getLastUrlsMarkedAsDeleted(): array {
        return $this->lastUrlsMarkedAsDeleted;
    }

    /**
     * @return int See {@link totalUrlsInQueue}
     */
    public function getTotalUrlsInQueue(): int {
        return $this->totalUrlsInQueue;
    }

    /**
     * @return int See {@link totalUrlsInQueueAddedToday}
     */
    public function getTotalUrlsInQueueAddedToday(): int {
        return $this->totalUrlsInQueueAddedToday;
    }

    /**
     * @return int See {@link totalSavedPosts}
     */
    public function getTotalSavedPosts(): int {
        return $this->totalSavedPosts;
    }

    /**
     * @return int See {@link totalSavedPostsToday}
     */
    public function getTotalSavedPostsToday(): int {
        return $this->totalSavedPostsToday;
    }

    /**
     * @return int See {@link totalRecrawledPosts}
     */
    public function getTotalRecrawledPosts(): int {
        return $this->totalRecrawledPosts;
    }

    /**
     * @return int See {@link totalRecrawledPostsToday}
     */
    public function getTotalRecrawledPostsToday(): int {
        return $this->totalRecrawledPostsToday;
    }

    /**
     * @return int See {@link totalRecrawlCount}
     * @noinspection PhpUnused
     */
    public function getTotalRecrawlCount(): int {
        return $this->totalRecrawlCount;
    }

    /**
     * @return int See {@link totalDeletedPosts}
     */
    public function getTotalDeletedPosts(): int {
        return $this->totalDeletedPosts;
    }

    /**
     * @return int See {@link totalDeletedPostsToday}
     */
    public function getTotalDeletedPostsToday(): int {
        return $this->totalDeletedPostsToday;
    }

    /**
     * @return null|string See {@link lastUrlCollectionDate}
     */
    public function getLastUrlCollectionDate(): ?string {
        return $this->lastUrlCollectionDate;
    }

    /**
     * @return null|string See {@link lastPostCrawlDate}
     */
    public function getLastPostCrawlDate(): ?string {
        return $this->lastPostCrawlDate;
    }

    /**
     * @return null|string See {@link lastPostRecrawlDate}
     */
    public function getLastPostRecrawlDate(): ?string {
        return $this->lastPostRecrawlDate;
    }

    /**
     * @return null|string See {@link lastPostDeleteDate}
     */
    public function getLastPostDeleteDate(): ?string {
        return $this->lastPostDeleteDate;
    }

    /**
     * @return null|string See {@link nextURLCollectionDate}
     */
    public function getNextURLCollectionDate(): ?string {
        return $this->nextURLCollectionDate;
    }

    /**
     * @return null|string See {@link nextPostCrawlDate}
     */
    public function getNextPostCrawlDate(): ?string {
        return $this->nextPostCrawlDate;
    }

    /**
     * @return null|string See {@link nextPostRecrawlDate}
     */
    public function getNextPostRecrawlDate(): ?string {
        return $this->nextPostRecrawlDate;
    }

    /**
     * @return null|string See {@link nextPostDeleteDate}
     */
    public function getNextPostDeleteDate(): ?string {
        return $this->nextPostDeleteDate;
    }

    /**
     * @return null|WP_Post See {@link nextUrlCollectionSite}
     */
    public function getNextUrlCollectionSite(): ?WP_Post {
        return $this->nextUrlCollectionSite;
    }

    /**
     * @return null|WP_Post See {@link nextPostCrawlSite}
     */
    public function getNextPostCrawlSite(): ?WP_Post {
        return $this->nextPostCrawlSite;
    }

    /**
     * @return null|WP_Post See {@link nextPostRecrawlSite}
     */
    public function getNextPostRecrawlSite(): ?WP_Post {
        return $this->nextPostRecrawlSite;
    }

    /**
     * @return null|WP_Post See {@link nextPostDeleteSite}
     */
    public function getNextPostDeleteSite(): ?WP_Post {
        return $this->nextPostDeleteSite;
    }

    /**
     * @return DashboardUrlTuple[] See {@link urlsCurrentlyBeingCrawled}
     */
    public function getUrlsCurrentlyBeingCrawled(): array {
        return $this->urlsCurrentlyBeingCrawled;
    }

    /**
     * @return DashboardPost[] See {@link postsCurrentlyBeingSaved}
     */
    public function getPostsCurrentlyBeingSaved(): array {
        return $this->postsCurrentlyBeingSaved;
    }

    /**
     * @return DashboardSite[] See {@link activeSites}
     */
    public function getActiveSites(): array {
        return $this->activeSites;
    }

    /**
     * Get interval description of a scheduled CRON event.
     *
     * @param string $eventKey Key of a CRON event defined by the plugin. E.g. "wpcc_event_collect_urls"
     * @return string E.g. "Every 10 minutes"
     * @noinspection PhpUnused
     */
    public function getCronEventIntervalDescription(string $eventKey): string {
        $description = Utils::array_get($this->cronIntervalDescriptions, $eventKey);
        return $description ?: "-";
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * Get last posts from URLs table.
     *
     * @param string   $orderBy A column name, e.g. 'saved_at' or 'recrawled_at'
     * @param null|int $limit   Max number of items that should be retrieved. If this is null, {@link lastPostsLimit}
     *                          will be used.
     * @return DashboardPost[]
     */
    private function getLastPosts(string $orderBy, $limit = null): array {
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();
        $query = "SELECT * FROM {$tableUrls}
          WHERE saved_post_id IS NOT NULL
            AND {$orderBy} IS NOT NULL
            AND is_saved = TRUE
            AND is_locked = FALSE
          ORDER BY {$orderBy} DESC
          LIMIT %d";

        // Get the results
        $results = $wpdb->get_results($wpdb->prepare($query, $limit != null ? (int) $limit : $this->lastPostsLimit));

        // Get the posts
        return $this->getPostsFromUrlTuples(is_array($results) ? $results : [$results]);
    }

    /**
     * Adds site data to each URL tuple, under 'wpcc' key.
     *
     * @param object[] $urlTuples An array of URL tuple objects.
     * @return DashboardPost[] Posts for URL tuples, with each post having 'wpcc' key that stores the URL tuple.
     */
    private function getPostsFromUrlTuples(array $urlTuples = []): array {
        if(!$urlTuples || empty($urlTuples)) return [];

        /** @var array<int, object> $preparedResults Stores "saved_post_id"s as key and tuple object as value. */
        $preparedResults = [];
        foreach($urlTuples as $key => $value) {
            if(!isset($value->saved_post_id)) continue;

            $preparedResults[$value->saved_post_id] = $value;
        }

        // Get the posts with the "saved_post_id"s
        $posts = $this->getPosts(array_keys($preparedResults));

        /** @var DashboardPost[] $results */
        $results = [];

        // Add each post the corresponding tuple from URLs table.
        foreach($posts as $post) {
            $urlTupleObj = $preparedResults[$post->ID] ?? null;
            if($urlTupleObj === null) continue;

            $dashboardUrlTuples = $this->addSiteDataToUrlTuples([$urlTupleObj]);
            if (!$dashboardUrlTuples) continue;

            $results[] = new DashboardPost($post, $dashboardUrlTuples[0]);
        }

        return $results;
    }

    /**
     * Get the latest URLs added to the queue.
     *
     * @param null|int $limit   Max number of items that should be retrieved. If this is null, {@link lastPostsLimit}
     *                          will be used.
     * @return DashboardUrlTuple[] An array of rows from URLs table
     */
    private function getLastAddedUrls($limit = null): array {
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();
        $query = "SELECT * FROM {$tableUrls}
          WHERE saved_post_id IS NULL
            AND saved_at IS NULL
            AND is_locked = FALSE
            AND is_saved = FALSE
          ORDER BY created_at DESC LIMIT %d;";

        // Get the URLs
        $results = $wpdb->get_results($wpdb->prepare($query, $limit != null ? $limit : $this->lastPostsLimit));
        if(!$results) return [];

        // Now, get the site for each URL tuple so that we can show site info with the URL.
        $results = $this->addSiteDataToUrlTuples(is_array($results) ? $results : [$results]);

        return $results;
    }

    /**
     * Get the latest URLs deleted from the database.
     *
     * @param null|int $limit   Max number of items that should be retrieved. If this is null, {@link lastPostsLimit}
     *                          will be used.
     * @return DashboardUrlTuple[] An array of rows from URLs table
     */
    private function getLastDeletedUrls($limit = null): array {
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();
        $query = "SELECT * FROM {$tableUrls}
          WHERE saved_post_id IS NULL
            AND is_locked = FALSE
            AND deleted_at IS NOT NULL
          ORDER BY deleted_at DESC LIMIT %d;";

        // Get the URLs
        $results = $wpdb->get_results($wpdb->prepare($query, $limit != null ? $limit : $this->lastPostsLimit));
        if(!$results) return [];

        // Now, get the site for each URL tuple so that we can show site info with the URL.
        $results = $this->addSiteDataToUrlTuples(is_array($results) ? $results : [$results]);

        return $results;
    }

    /**
     * Adds 'site' data to each URL tuple.
     *
     * @param object[] $urlTuples An array of URL tuple objects retrieved from the database table
     * @return DashboardUrlTuple[] An array of URL tuple objects with each having a 'site' key, which stores a site as
     *                             {@link WP_Post}.
     */
    private function addSiteDataToUrlTuples(array $urlTuples): array {
        if(!$urlTuples) return [];

        // First, get the site IDs.
        $siteIds = [];
        foreach($urlTuples as $urlTuple) {
            if (!isset($urlTuple->post_id)) continue;
            $siteIds[] = $urlTuple->post_id;
        }

        // Get the sites.
        $sites = $this->getPosts($siteIds, Environment::postType());

        // Add each URL its site data.
        return array_map(function($urlTuple) use (&$sites) {
            $ownerSite = null;

            /** @var int|null $urlTupleSiteId */
            $urlTupleSiteId = isset($urlTuple->post_id) ? (int) $urlTuple->post_id : null;

            if ($urlTupleSiteId !== null) {
                foreach($sites as $site) {
                    if($site->ID == $urlTupleSiteId) {
                        $ownerSite = $site;
                        break;
                    }
                }

            }

            return new DashboardUrlTuple($ownerSite, $urlTuple);

        }, $urlTuples);
    }

    /**
     * @param array  $ids IDs of the posts
     * @param string $postType Post type
     * @return WP_Post[]
     */
    private function getPosts($ids = [], $postType = 'any'): array {
        if(!$ids) return [];

        /** @var WP_Post[] $result */
        $result = get_posts([
            'numberposts' => -1,
            'orderby'     => 'post__in',
            'post_type'   => $postType,
            'include'     => $ids,
            'post_status' => 'any',
        ]);
        return $result;
    }

    /**
     * Get today's date at midnight.
     *
     * @return string Date formatted as <b>Y-m-d H:i:s</b> (MySQL)
     */
    private function getToday(): string {
        $now = time() + $this->getGMTOffset();
        return date("Y-m-d", $now) . " 00:00:00";
    }

    /**
     * Get GMT offset in seconds
     *
     * @return int GMT offset in seconds.
     */
    private function getGMTOffset() {
        $utcOffset = get_option('gmt_offset', null);
        return $utcOffset != null ? ($utcOffset * 60 * 60) : 0;
    }

    /**
     * Prepares the results structured as [post_id => '', 'count' => '' ] as [post_id => count]
     *
     * @param array $results An array of objects with each object having 'post_id' and 'count' keys.
     * @return array<int, int> Prepared results
     */
    private function preparePostIdCountDbResults($results): array {
        $prepared = [];
        foreach($results as $result) {
            $postId = isset($result->post_id) ? (int) $result->post_id : null;
            if ($postId === null) continue;

            $prepared[$postId] = isset($result->count) ? (int) $result->count : 0;
        }

        return $prepared;
    }

    /**
     * @return WP_Post[] All currently active "site" posts
     * @since 1.11.1
     */
    private function getCurrentlyActiveSitePosts(): array {
        $query = new WP_Query([
            'post_type'   => Environment::postType(),
            'meta_query'  => [
                'relation' => 'OR',
                [
                    'key'     => SettingKey::ACTIVE,
                    'value'   => ['on', 1],
                    'compare' => 'in',
                ],
                [
                    'key'     => SettingKey::ACTIVE_RECRAWLING,
                    'value'   => ['on', 1],
                    'compare' => 'in',
                ],
                [
                    'key'     => SettingKey::ACTIVE_POST_DELETING,
                    'value'   => ['on', 1],
                    'compare' => 'in',
                ]
            ],
            'post_status' => 'publish',
            'nopaging'    => true,
        ]);

        /** @var WP_Post[] $result */
        $result = $query->get_posts();
        return $result;
    }

    /*
     * STATIC METHODS
     */

    /**
     * @return int Dashboard's minimum refresh interval
     * @since 1.9.0
     */
    public static function getMinRefreshInterval(): int {
        return Environment::isDemo() ? 60 : 5;
    }
}
