<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 16:24
 */

namespace WPCCrawler\Controllers;


use Illuminate\Contracts\View\View;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Crawling\Bot\CategoryBot;
use WPCCrawler\Objects\Crawling\Savers\PostSaver;
use WPCCrawler\Objects\Enums\ErrorType;
use WPCCrawler\Objects\Enums\InformationMessage;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Enums\PageSlug;
use WPCCrawler\Objects\Enums\UrlType;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Page\AbstractMenuPage;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Traits\ErrorTrait;
use WPCCrawler\Permission;
use WPCCrawler\Utils;

class ToolsController extends AbstractMenuPage {

    use ErrorTrait;

    /** @var bool This is true if a post has just been recrawled. */
    private $isRecrawled = false;

    /**
     * @return string Menu title for the page
     */
    public function getMenuTitle(): string {
        return _wpcc('Tools');
    }

    /**
     * @return string Page title
     */
    public function getPageTitle(): string {
        return _wpcc('Tools');
    }

    /**
     * @return string Slug for the page
     */
    public function getPageSlug(): string {
        return PageSlug::TOOLS;
    }

    /**
     * @return bool
     * @since 1.9.0
     */
    protected function isAllowed(): bool {
        return Permission::canViewTools();
    }

    /**
     * Get view for the page.
     *
     * @return View Not-rendered blade view for the page
     */
    public function getView() {
        // Add assets
        Factory::assetManager()->addGuides();
        Factory::assetManager()->addPostSettings();
        Factory::assetManager()->addTools();

        return Utils::view('tools/main')->with([
            'settings'      =>  [], // To prevent errors, since all form items work with $settings variable
            'sites'         =>  Utils::getSitesForSelect(),
            'categories'    =>  Utils::getCategories(null, true),
            'urlTypes'      =>  $this->getUrlTypes()
        ]);
    }

    public function handleAJAX(): void {
        parent::handleAJAX();
        $data = wp_unslash($this->getAjaxData());
        if (!is_array($data)) return;

        // TODO: Get rid of "switch" below. Use an array to define the methods related to the tool type.

        $result = null;
        switch ($data["tool_type"]) {
            // Save a post
            case 'save_post':
                $result = $this->handleSavePostRequest($data);
                break;

            // Recrawl a post
            case 'recrawl_post':
                $result = $this->handleRecrawlPostRequest($data);
                break;

            // Delete URLs
            case "delete_urls":
                $result = $this->handleDeleteUrlsRequest($data);
                break;

            // Unlock URLs
            case 'unlock_all_urls':
                $result = $this->handleUnlockAllUrlsRequest($data);
                break;

            // Get post URLs from a category URL
            case 'get_post_urls_from_category_url':
                echo $this->handleGetPostUrlsFromCategoryUrlRequest($data);
                return;

            // Add URLs to database
            case 'add_urls_to_database':
                echo $this->handleAddUrlsToDatabaseRequest($data);
                return;
        }

        // Send the response.
        echo json_encode([
            'view' => Utils::view('partials.information-message')
                ->with('message', $result)
                ->render()
        ]);
    }

    /*
     * REQUEST HANDLERS
     */

    /**
     * Handles "get post URLs from category URL" request
     *
     * @param array $data
     * @return string
     */
    private function handleGetPostUrlsFromCategoryUrlRequest($data): string {
        $emptyResult = $this->getEmptyJson();

        $categoryUrl = Utils::array_get($data, 'category_url');
        $siteId = Utils::array_get($data, 'site_id');

        // If the category URL or the site ID does not exist, return an empty result.
        if (!$categoryUrl || !$siteId) return $emptyResult;

        // Get site settings
        $settings = get_post_meta($siteId);
        if (!$settings) return $emptyResult;

        // Get category URLs
        $bot = new CategoryBot(get_post_meta($siteId), $siteId);
        $preparedUrl = Utils::prepareUrl($bot->getSiteUrl(), $categoryUrl);
        if ($preparedUrl === null) return $emptyResult;

        $categoryData = $bot->collectUrls($preparedUrl);

        $results = [];
        if ($categoryData) {
            // Prepare the response
            foreach($categoryData->getPostUrlList()->getItems() as $postUrl) {
                $item = ["url" => $postUrl->getUrl()];
                if ($thumbnail = $postUrl->getThumbnailUrl()) {
                    $item["thumbnail"] = $thumbnail;
                }

                $results[] = $item;
            }
        }

        return json_encode([
            'view' => Utils::view('partials.info-list')
                ->render(),

            // Send whether there are information that should be shown or not
            'hasInfo' => Informer::hasInfo(),

            // Send the results
            'results' => $results,
        ]) ?: $emptyResult;
    }

    /**
     * Handles "add URLs to database" request
     *
     * @param array $data Request data
     * @return string
     */
    private function handleAddUrlsToDatabaseRequest($data): string {
        $results = array_merge(
            $this->addPostUrlsToDatabase($data),
            $this->addPostUrlsToDatabaseFromCategoryUrls($data)
        );

        $insertCount = sizeof($results);

        // If there are no URLs inserted, return a single message.
        if ($insertCount === 0) {
            $message = _wpcc('No URLs have been inserted.');
            return json_encode(['view' => $message]) ?: $this->getEmptyJson();
        }

        // Return a message with the inserted URLs
        $message = $insertCount === 1 ? _wpcc('1 URL has been inserted') : sprintf(_wpcc('%1$s URLs have been inserted'), $insertCount);
        $message .= ':';

        return json_encode([
            'view' => Utils::view('partials.test-result')
                ->with('message', $message)
                ->with('results', $results)
                ->render()
        ]) ?: $this->getEmptyJson();
    }

    /**
     * Handles "save post" request
     *
     * @param array $data Request data
     * @return null|string
     */
    public function handleSavePostRequest($data): ?string {
        if (!isset($data["_wpcc_tools_post_url"])) return null;

        $postId = $this->savePostManually(
            $data[SettingKey::WPCC_TOOLS_SITE_ID],
            $data["_wpcc_tools_post_url"],
            $data[SettingKey::WPCC_TOOLS_CATEGORY_ID],
            $data["_wpcc_tools_featured_image_url"],
            Utils::array_get($data, '_wpcc_recrawl_if_duplicate', false) == '1'
        );

        if ($postId) {
            $msg = $this->isRecrawled ? _wpcc('The post has been recrawled.') : _wpcc('The post has been saved.');

            $postUrl = get_permalink($postId);
            $result = sprintf($msg . ' ' . _wpcc('You can check it here') . ': <a href="%1$s" class="post-url" target="_blank">%1$s</a>', $postUrl);

            return $result;
        }

        $result = _wpcc('The post could not be saved.');
        if($errors = $this->getErrorDescriptions()) $result .= "<br>" . implode("<br>", $errors);

        // If there is a duplicate post, show its link to the user.
        if($duplicatePostId = $this->getErrorValue(ErrorType::DUPLICATE_POST)) {
            $duplicatePost = get_post($duplicatePostId);

            if($duplicatePost) {
                $duplicatePostTitle = $duplicatePost->post_title ? $duplicatePost->post_title : _wpcc("No Title");
                $duplicatePostUrl = get_permalink($duplicatePostId);
                $result .= "<br>" . _wpcc("You should delete the duplicate post first") . ": " .
                    sprintf('<a href="%1$s" class="duplicate-post-url" target="_blank">%2$s</a> ID: <span class="duplicate-post-id">%3$s</span>', $duplicatePostUrl, $duplicatePostTitle, $duplicatePostId);
            }
        }

        return $result;
    }

    /**
     * Handles "recrawl post" requests
     *
     * @param array $data Request data
     * @return null|string
     */
    private function handleRecrawlPostRequest($data): ?string {
        if (!isset($data[SettingKey::WPCC_TOOLS_RECRAWL_POST_ID])) return null;

        $postId = $this->recrawlPostManually($data[SettingKey::WPCC_TOOLS_RECRAWL_POST_ID]);

        if ($postId) {
            $postUrl = get_permalink($postId);
            $result = sprintf(_wpcc('The post is recrawled. You can check it here') . ': <a href="%1$s" class="post-url" target="_blank">%1$s</a>', $postUrl);
            return $result;

        }

        $result = _wpcc('The post could not be found or it might not have been saved by WP Content Crawler.');
        if($errors = $this->getErrorDescriptions()) $result .= "<br>" . implode("<br>", $errors);

        return $result;
    }

    /**
     * Handles "delete URL" requests
     *
     * @param array $data Request data
     * @return false|null|string
     */
    private function handleDeleteUrlsRequest($data) {
        if(!isset($data[SettingKey::WPCC_TOOLS_SAFETY_CHECK])) {
            return _wpcc('You did not check the safety checkbox.');
        }

        if (!$data[SettingKey::WPCC_TOOLS_SAFETY_CHECK] || !isset($data[SettingKey::WPCC_TOOLS_CLEAR_URLS_SITE_ID]) || !isset($data[SettingKey::WPCC_TOOLS_URL_TYPE])) {
            return null;
        }

        $result = null;
        $siteId = $data[SettingKey::WPCC_TOOLS_CLEAR_URLS_SITE_ID];
        $resetLastCrawled = false;
        $resetLastRecrawled = false;

        switch ($data[SettingKey::WPCC_TOOLS_URL_TYPE]) {
            case "url_type_queue":
                $result = Factory::databaseService()->deleteUrlsBySiteIdAndSavedStatus($siteId, false);
                $resetLastCrawled = true;
                break;

            case "url_type_saved":
                $result = Factory::databaseService()->deleteUrlsBySiteIdAndSavedStatus($siteId, true);
                $resetLastRecrawled = true;
                break;

            case "url_type_all":
                $result = Factory::databaseService()->deleteUrlsBySiteId($siteId);
                $resetLastCrawled = true;
                $resetLastRecrawled = true;
                break;
        }

        if($resetLastCrawled) {
            Factory::postSaver()->setIsRecrawl(false);
            Factory::postSaver()->resetLastCrawled($siteId);
        }

        if($resetLastRecrawled) {
            Factory::postSaver()->setIsRecrawl(true);
            Factory::postSaver()->resetLastCrawled($siteId);
        }

        if ($result !== false) {
            $result = _wpcc("Deleted successfully.");
        }

        return $result;
    }

    /**
     * Handles "unlock all URLs" request
     *
     * @param array $data Request data
     * @return string
     */
    private function handleUnlockAllUrlsRequest($data): string {
        $res = Factory::databaseService()->unlockAllUrls();
        $result = $res ?
            ($res > 1 ? sprintf(_wpcc("%s URLs have been unlocked."), $res) : _wpcc("1 URL has been unlocked.") ) :
            _wpcc("There are no locked URLs currently.");

        return $result;
    }

    /*
     * HELPERS
     */

    /**
     * Adds requested post URLs to the database
     *
     * @param array $data Request data
     * @return array
     */
    private function addPostUrlsToDatabase($data): array {
        $results = [];

        $postUrls = Utils::array_get($data, 'post_urls');
        if (!$postUrls) return $results;

        $postUrls = json_decode($postUrls, true);
        if (!$postUrls) return $results;

        // Insert each URL into the database
        foreach($postUrls as $urlData) {
            $siteId     = Utils::array_get($urlData, '_siteId');
            $url        = Utils::array_get($urlData, '_url');
            $categoryId = Utils::array_get($urlData, '_categoryId');
            $imageUrl   = Utils::array_get($urlData, '_imageUrl');

            // If there is no site ID or URL or category ID, continue with the next one.
            if (!$siteId || !$url || !$categoryId) continue;

            $id = Factory::databaseService()->addUrl($siteId, $url, $imageUrl, $categoryId);
            if ($id) $results[] = $url;
        }

        return $results;
    }

    /**
     * Adds post URLs to the database by retrieving them from the given category URLs
     *
     * @param array $data Request data.
     * @return array
     */
    private function addPostUrlsToDatabaseFromCategoryUrls($data): array {
        $results = [];

        $categoryUrls = Utils::array_get($data, 'category_urls');
        if (!$categoryUrls) return $results;

        $categoryUrls = json_decode($categoryUrls, true);
        if (!$categoryUrls) return $results;

        $siteBotCache = [];

        foreach($categoryUrls as $urlData) {
            $categoryUrl    = Utils::array_get($urlData, '_url');
            $siteId         = Utils::array_get($urlData, '_siteId');
            $categoryId     = Utils::array_get($urlData, '_categoryId');

            // Create category bot
            // If the bot exists in cache, use it.
            if (isset($siteBotCache[$siteId])) {
                $bot = $siteBotCache[$siteId];
            } else {
                // Otherwise, create a bot and cache it.
                $bot = new CategoryBot(get_post_meta($siteId), $siteId);
                $siteBotCache[$siteId] = $bot;
            }

            $preparedUrl = Utils::prepareUrl($bot->getSiteUrl(), $categoryUrl);
            if ($preparedUrl === null) continue;

            $data = $bot->collectUrls($preparedUrl);
            if (!$data) continue;

            foreach($data->getPostUrlList()->getItems() as $item) {
                $postUrl = $item->getUrl();
                if(!$postUrl) continue;

                if(Factory::databaseService()->addUrl($siteId, $postUrl, $item->getThumbnailUrl(), $categoryId)) {
                    $results[] = $postUrl;
                }
            }

        }

        return $results;
    }

    /**
     * Saves a post by post URL, site ID and category ID
     *
     * @param int         $siteId       ID of a site (custom post type) to which the URL belongs
     * @param string      $postUrl      The URL for the post-to-be-saved
     * @param int         $categoryId   ID of a category in which the saved post is saved
     * @param null|string $thumbnailUrl Thumbnail (featured image) URL for the post
     * @param bool        $recrawlIfDuplicate When this is true, if there is a duplicate post found, it will be recrawled.
     * @return int|null Inserted post's ID
     */
    public function savePostManually($siteId, $postUrl, $categoryId, $thumbnailUrl = null, $recrawlIfDuplicate = false): ?int {
        $this->isRecrawled = false;
        $settings = get_post_meta($siteId);

        $postSaver = new PostSaver();
        $postSaver->setSettings($settings, Factory::postService()->getSingleMetaKeys());
        $postSaver->setIsRecrawl(false);

        // First check if it exists
        $urlTuple = Factory::databaseService()->getUrlBySiteIdAndUrl($siteId, $postUrl);

        // Check for duplicate
        if($urlTuple && ($savedPostId = $urlTuple->getSavedPostId())) {
            // Get the post
            $postData = get_post($savedPostId, ARRAY_A);

            // If the post exists, this is a duplicate. Checking this is vital. If we skip this and check it via
            // isDuplicate method, it will exclude the saved_post_id when checking. And then, it won't be able to
            // catch this duplicate post. Either check the existence of postData here, or set urlTuple's saved_post_id
            // to null before passing it to isDuplicate method.
            if($postData) {
                // If a recrawl is requested in case of a duplicate post, recrawl it.
                if ($recrawlIfDuplicate) return $this->recrawlPostManually($savedPostId);

                $this->addError(ErrorType::DUPLICATE_POST, $savedPostId);
                Informer::add(Information::fromInformationMessage(
                    InformationMessage::DUPLICATE_POST,
                    _wpcc("Post ID") . ": {$savedPostId}",
                    InformationType::ERROR
                )->addAsLog());

                return null;
            }

            // Otherwise, check for another duplicate post. This is a very unlikely case.
            if($postSaver->isDuplicate($postUrl, $postData, true, true)) {
                // If a recrawl is requested in case of a duplicate post, recrawl it.
                if ($recrawlIfDuplicate) return $this->recrawlPostManually($savedPostId);

                // Get the errors from the post saver so that we can use them later.
                $this->setErrors($postSaver->getErrors());

                return null;
            }
        }

        // If saved, delete.
        if($urlTuple) Factory::databaseService()->deleteUrl($urlTuple->getId());

        // Now, save the URL
        $urlId = Factory::databaseService()->addUrl($siteId, $postUrl, $thumbnailUrl, $categoryId) ?: null;

        // Define the required variables. These variables will be changed by savePost function.
        $nextPageUrl    = null;
        $nextPageUrls   = null;

        $postId = null;
        $finished = false;
        while(!$finished) {
            $postId = $postSaver->savePost($siteId, $settings, $urlId, false, $nextPageUrl, $nextPageUrls, $postId);

            $nextPageUrl = $postSaver->getNextPageUrl();
            $nextPageUrls = $postSaver->getNextPageUrls();

            if(!$nextPageUrl || !$postId) $finished = true;
        }
//        var_dump("Saving the post is finished. Post ID is ");
//        var_dump($postId);

        // Get the errors from the post saver so that we can use them later.
        $this->setErrors($postSaver->getErrors());

        return $postId;

    }

    /**
     * Recrawl a post manually by its post ID
     *
     * @param int $postId ID of the post to be recrawled
     * @return null|int ID of the post or null if there was something wrong
     */
    public function recrawlPostManually($postId): ?int {
        if(!$postId || $postId < 1) return null;
        $urlTuple = Factory::databaseService()->getUrlByPostId($postId);

        if(!$urlTuple) return null;

        // Define the required variables. These variables will be changed by savePost method.
        $siteId         = $urlTuple->getSiteId();
        $nextPageUrl    = null;
        $nextPageUrls   = null;

        $settings = get_post_meta($siteId);

        $postSaver = new PostSaver();
        $postSaver->setIsRecrawl(true);

        $finished = false;
        while(!$finished) {
            $postId = $postSaver->savePost($siteId, $settings, $urlTuple->getId(), false,
                $nextPageUrl, $nextPageUrls, $postId);

            $nextPageUrl = $postSaver->getNextPageUrl();
            $nextPageUrls = $postSaver->getNextPageUrls();

            if(!$nextPageUrl || !$postId) $finished = true;
        }
//        var_dump("Recrawling the post is finished. Post ID is ");
//        var_dump($postId);

        // Get the errors from the post saver so that we can use them later.
        $this->setErrors($postSaver->getErrors());

        $this->isRecrawled = true;

        return $postId;
    }

    /**
     * Get URL types to be shown as options in a select element.
     * @return array<string, string> URL types as key,value pairs
     */
    private function getUrlTypes(): array {
        return [
            UrlType::QUEUE => _wpcc("In Queue"),
            UrlType::SAVED => _wpcc("Already Saved"),
            UrlType::ALL   => _wpcc("All")
        ];
    }

    /**
     * @return string An empty JSON string
     * @since 1.11.1
     */
    private function getEmptyJson(): string {
        return '[]';
    }
}
