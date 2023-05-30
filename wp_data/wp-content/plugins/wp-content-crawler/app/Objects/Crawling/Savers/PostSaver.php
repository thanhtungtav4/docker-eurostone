<?php /** @noinspection SqlResolve */
/** @noinspection SqlNoDataSourceInspection */

/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 22:09
 */

namespace WPCCrawler\Objects\Crawling\Savers;


use DOMElement;
use Exception;
use WP_Error;
use WPCCrawler\Exceptions\AlreadySavedPageException;
use WPCCrawler\Exceptions\CancelSavingAndDeleteException;
use WPCCrawler\Exceptions\CancelSavingException;
use WPCCrawler\Objects\Database\UrlTuple;
use WPCCrawler\Objects\Enums\PostStatus;
use WPCCrawler\Objects\File\MediaSavingOptions;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Factory\Cron\AbstractCronKeyFactory;
use WPCCrawler\Objects\Settings\Factory\Cron\PostCrawlCronKeyFactory;
use WPCCrawler\Objects\Settings\Factory\Cron\PostRecrawlCronKeyFactory;
use WP_User_Query;
use WPCCrawler\Exceptions\DuplicatePostException;
use WPCCrawler\Exceptions\StopSavingException;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Crawling\Data\PostData;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingService;
use WPCCrawler\PostDetail\PostDetailsService;
use WPCCrawler\Objects\Crawling\Data\PostSaverData;
use WPCCrawler\Objects\Enums\ErrorType;
use WPCCrawler\Objects\Enums\InformationMessage;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\File\MediaFile;
use WPCCrawler\Objects\File\MediaService;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Traits\ErrorTrait;
use WPCCrawler\Objects\Traits\SettingsTrait;
use WPCCrawler\Utils;

class PostSaver extends AbstractSaver {

    use SettingsTrait;
    use ErrorTrait;

    /** @var bool */
    private static $DEBUG = false;

    /** @var string Stores ID of the site for which the last post crawl was performed. */
    public $optionLastCrawledSiteId = '_wpcc_last_crawled_site_id';

    /** @var string Stores ID of the site for which the last post recrawl was performed */
    public $optionLastRecrawledSiteId = '_wpcc_last_recrawled_site_id';

    /** @var string Stores source URLs as an array. Each inserted post will have this meta. */
    private $postMetaSourceUrls = '_wpcc_source_urls';

    /** @var string Stores first page URL of the target post. Each inserted post will have this meta. */
    private $postMetaPostFirstPageUrl = '_wpcc_post_url';

    /*
     * DUPLICATE CHECK TYPES
     */

    const DUPLICATE_CHECK_URL       = SettingInnerKey::URL;
    const DUPLICATE_CHECK_TITLE     = SettingInnerKey::TITLE;
    const DUPLICATE_CHECK_CONTENT   = SettingInnerKey::CONTENT;

    /*
     *
     */

    /** @var PostData|null */
    private $data;

    /** @var bool Stores whether the current task is a recrawl task or not. */
    private $isRecrawl = false;

    /*
     *
     */

    /** @var string|null */
    private $nextPageUrl = null;

    /** @var array|null */
    private $nextPageUrls = null;

    /** @var bool */
    private $isFirstPage = false;

    /** @var UrlTuple|null */
    private $urlTuple = null;

    /** @var string|null */
    private $urlToCrawl = null;

    /** @var int|null */
    private $postId = null;

    /** @var int|null */
    private $draftPostId = null;

    /** @var int|null */
    private $siteIdToCheck = null;

    /** @var bool */
    private $updateLastCrawled = false;

    /** @var string|null */
    private $postUrl = null;

    /** @var PostBot|null */
    private $bot = null;

    /** @var PostSaverData|null */
    private $saverData = null;

    /** @var bool */
    private $contentExists = true;

    /**
     * Update (recrawl) a post of a URL tuple.
     *
     * @param object $urlTuple A row in wpcc_urls table
     * @return void
     */
    public function executePostRecrawl($urlTuple) {
        $this->setRequestMade(false);
        $this->clearErrors();

        // Do not proceed if the URL tuple is not found or it does not have a saved post ID.
        if(!$urlTuple || !$urlTuple->saved_post_id) return null;

        $this->isRecrawl = true;

        $siteIdToCheck = $urlTuple->post_id;

        // Get settings for the site ID
        $settings = get_post_meta($siteIdToCheck);

        $this->setSettings($settings, Factory::postService()->getSingleMetaKeys());

        $lastRecrawledUrlId = $this->getSetting($this->getCronKeyFactory()->getLastCrawledUrlIdKey());
        $nextPageUrl        = $this->getSetting($this->getCronKeyFactory()->getPostNextPageUrlKey());
        $nextPageUrls       = $this->getSetting($this->getCronKeyFactory()->getPostNextPageUrlsKey());
        $draftPostId        = $this->getSetting($this->getCronKeyFactory()->getPostDraftIdKey());

        // If the post with saved_post_id does not exist, make URL tuple's saved_post_id null, and stop.
        $post = get_post($lastRecrawledUrlId && $draftPostId ? $draftPostId : $urlTuple->saved_post_id);
        if(!$post) {
            Factory::databaseService()->updateUrlSavedPostId($lastRecrawledUrlId, null);

            // Otherwise, make variables null to continue with the URL tuple.
            $lastRecrawledUrlId = null;
            $nextPageUrl = null;
            $nextPageUrls = null;
            $draftPostId = null;
        }

        $this->savePost(
            $siteIdToCheck,
            $settings,
            // If there is a draft post ID, it means that post is not finished to be saved. So, use URL ID of the draft
            // post instead of the ID of the current URL tuple.
            $lastRecrawledUrlId && $draftPostId ? $lastRecrawledUrlId : $urlTuple->id,
            true,
            $nextPageUrl,
            $nextPageUrls,
            $lastRecrawledUrlId && $draftPostId ? $draftPostId : $urlTuple->saved_post_id
        );
    }

    /**
     * Save a post for a site. This method does two things:
     * <li>Save a post's next page if there is a post that has pages and has not yet saved completely.</li>
     * <li>Save an unsaved post.</li>
     *
     * @param int  $siteIdToCheck Site ID for which a post will be saved
     */
    public function executePostSave($siteIdToCheck): void {
        $this->setRequestMade(false);
        $this->clearErrors();

        if(!$siteIdToCheck) return;

        $this->isRecrawl = false;

        // Get settings for the site ID
        $settings = get_post_meta($siteIdToCheck);

        $this->setSettings($settings, Factory::postService()->getSingleMetaKeys());

        $lastCrawledUrlId   = $this->getSetting($this->getCronKeyFactory()->getLastCrawledUrlIdKey());
        $nextPageUrl        = $this->getSetting($this->getCronKeyFactory()->getPostNextPageUrlKey());
        $nextPageUrls       = $this->getSetting($this->getCronKeyFactory()->getPostNextPageUrlsKey());
        $draftPostId        = $this->getSetting($this->getCronKeyFactory()->getPostDraftIdKey());

        $this->savePost($siteIdToCheck, $settings, $lastCrawledUrlId, true, $nextPageUrl, $nextPageUrls, $draftPostId);
    }

    /*
     *
     */

    /**
     * Save a post to the database. This method does 3 things:
     * <ul>
     * <li> If a urlId is supplied, saves its post URL to the database. This is used to save a post manually. Just pick
     * an ID from the database.</li>
     * <li> If there are only siteIdToCheck and its settings, then a URL will be found by using CRON settings and saved
     * to the database.</li>
     * <li> If there are urlId, nextPageUrl(s) and draftPostId, then a next page will be saved for the specified urlId.</li>
     * </ul>
     *
     * @param int         $siteIdToCheck     Site ID which the post belongs to, to get the settings for crawling
     * @param array       $settings          Settings for siteIdToCheck
     * @param null|int    $urlId             ID of a URL tuple from wpcc_urls table
     * @param bool        $updateLastCrawled True if you want to update CRON options about last crawled site, false
     *                                       otherwise
     * @param null|string $nextPageUrl       Next page URL for the post, if exists
     * @param null|array  $nextPageUrls      All next page URLs for the post, if exists
     * @param null|int    $draftPostId       ID of a post which is used to save content for this post, for previous
     *                                       pages
     * @return int|null Post ID, or null if the post is not saved
     */
    public function savePost($siteIdToCheck, $settings, $urlId = null, $updateLastCrawled = false,
                             $nextPageUrl = null, $nextPageUrls = null, $draftPostId = null): ?int {

        // Invalidate factory instances since new data will be received. If not invalidated, the factories will use
        // old data, if it exists. For example, if this is a second page of a post, the factories will use the data
        // coming from the first page if they are not invalidated.
        PostDetailsService::getInstance()->invalidateFactoryInstances();

        if(!$this->getSettings()) $this->setSettings($settings, Factory::postService()->getSingleMetaKeys());

        // Initialize instance variables
        $this->urlToCrawl           = false;
        $this->isFirstPage          = true;
        $this->nextPageUrls         = $nextPageUrls;
        $this->nextPageUrl          = $nextPageUrl;
        $this->draftPostId          = $draftPostId;
        $this->siteIdToCheck        = $siteIdToCheck;
        $this->updateLastCrawled    = $updateLastCrawled;

        if(self::$DEBUG) {
            var_dump('Last Crawled Url ID: ' . $urlId);
            var_dump('Next Page URL: ' . $this->nextPageUrl);
            var_dump('Next Page URLs:');
            var_dump($this->nextPageUrls);
            var_dump('Draft Post ID: ' . $this->draftPostId);
        }

        try {
            // Prepare $this->isFirstPage, $this->urlTuple, and $this->urlToCrawl
            $this->prepareUrlTupleToCrawl($urlId);

            // Lock the URL tuple so that it won't be selected as the URL to crawl again during saving process
            Factory::databaseService()->updateUrlSavedStatus(
                $this->urlTuple->getId(),
                $this->urlTuple->isSaved(),
                $this->urlTuple->getSavedPostId(),
                $this->urlTuple->getUpdateCount(),
                true
            );

            $this->initPostUrl();
            $this->initBot($settings);                              // Create a new bot
            $this->initSaverData();                                 // Create the saver data that will be used by other objects

            $this->preparePostData();                               // Prepare the post data
            $this->prepareNextPageUrl();                            // Prepare next page URL
            $this->checkAndReactToContentExistence();               // Check content existence
            $this->data->setWpPostData($this->createWPPostData());  // Prepare the post data and store it in the PostData instance
            $this->handleIfDuplicate();                             // Check if the post is duplicate and, if so, handle the situation.
            $this->insertPostData();                                // Insert the prepared post data into the database.
            $this->saveCategories();                                // Set post's category if it belongs to a custom taxonomy
            $this->maybeDeleteAttachments();                        // Delete already-existing attachments when updating a post.
            $this->saveFeaturedImage();                             // Save featured image
            $this->saveMetaKeywords();                              // Save meta keywords
            $this->saveMetaDescription();                           // Save meta description

            // Save attachments
            $galleryAttachmentIds = $this->saveAttachments();
            $this->saverData->setGalleryAttachmentIds($galleryAttachmentIds);

            // Save registered post details
            PostDetailsService::getInstance()->save($this->bot, $this->saverData);

            // Save custom meta. This should be done at last to allow the user to override some previously-set post meta values.
            $this->saveCustomMeta();

            // Save custom taxonomies. This should be done at last to allow the user to override some previously-set taxonomy values.
            $this->saveCustomTaxonomies();

            // The saving operation is complete. Trigger the save_post hook if it must be triggered.
            $this->maybeTriggerSavePostHook();

        } catch (StopSavingException $e) {
            // If the saving operation must be stopped, return null.
            return null;

        } catch (AlreadySavedPageException $e) {
            // If there is no draft post ID, stop. If this page was already saved, then there must be a draft post ID.
            // The current page is defined as another page of that draft post. So, there must be a draft post. Also,
            // we need the WP post data to properly handle this exception.
            if (!$this->draftPostId || !$e->getWpPostData()) return null;

            // Update the post so that its status is set as the user wants. Also, by this way, we set the ID of the post,
            // which is required for the rest of this method to properly tidy up the saving.
            $this->setPostId(wp_insert_post($e->getWpPostData()));

            // Do nothing. The rest of this method will handle the rest.

        } catch(DuplicatePostException $e) {
            $this->onDuplicatePostException($e);
            return null;

        } catch(CancelSavingException $e) {
            $this->onCancelSavingException($e);

            // Do nothing. The rest of this method will handle the rest.

        } catch (CancelSavingAndDeleteException $e) {
            $this->onCancelSavingAndDeleteException($e);
            return null;

        } catch (Exception $e) {
            // Log any other exception and return null
            Informer::addInfo($e->getMessage())->setException($e)->addAsLog();
            return null;
        }

        $this->onAfterSaving();
        return $this->postId;
    }

    /**
     * Tidies up the saving operation by updating the database
     *
     * @since 1.11.0
     */
    private function onAfterSaving(): void {
        // Save related meta
        if($this->updateLastCrawled)
            $this->updateLastCrawled(
                $this->siteIdToCheck,
                $this->nextPageUrl ? $this->urlTuple->getId() : null,
                $this->nextPageUrl,
                $this->nextPageUrls,
                $this->nextPageUrl ? $this->postId : ''
            );

        // Save post URL as post meta
        if($this->isFirstPage && $this->postId)
            update_post_meta($this->postId, $this->postMetaPostFirstPageUrl, $this->urlTuple->getUrl());

        // Update saved_at if this is the first page and the URL tuple does not have a saved_post_id
        if($this->isFirstPage && $this->postId && !$this->urlTuple->getSavedPostId()) {
            Factory::databaseService()->updateUrlPostSavedAt(
                $this->urlTuple->getId(),
                $this->postId,
                $this->data->getDateCreatedString()
            );
        }

        // If this is the last page, tidy up things.
        if(!$this->nextPageUrl) {
            // Set this URL as saved
            if(!$this->isRecrawl) {
                Factory::databaseService()->updateUrlSavedStatus(
                    $this->urlTuple->getId(),
                    true,
                    $this->postId ? $this->postId : null,
                    $this->urlTuple->getUpdateCount(),
                    false
                );

            // Otherwise, set this URL as recrawled
            } else {
                Factory::databaseService()->updateUrlRecrawledStatus(
                    $this->urlTuple->getId(),
                    $this->urlTuple->getUpdateCount() + 1,
                    false
                );
            }

        // Otherwise, remove the lock so that the next page can be saved. Also, make this URL not saved so that it won't
        // be selected as a URL that needs to be crawled for post crawling event.
        } else {
            Factory::databaseService()->updateUrlSavedStatus(
                $this->urlTuple->getId(),
                false,
                $this->postId ? $this->postId : null,
                $this->urlTuple->getUpdateCount(),
                false
            );
        }

        if(self::$DEBUG) {
            var_dump('Last Crawled Url ID: '    . $this->urlTuple->getId());
            var_dump('Category ID: '            . $this->urlTuple->getCategoryId());
            var_dump('Next Page URL: '          . $this->nextPageUrl);
            var_dump('Next Page URLs:');
            var_dump($this->nextPageUrls);
            var_dump('Draft Post ID: '          . ($this->nextPageUrl ? $this->postId : ''));
        }
    }

    /**
     * Handles what happens when there is a duplicate post.
     *
     * @param DuplicatePostException $e
     * @since 1.8.0
     * @uses  cancelCrawlingAndDeleteCurrentPost()
     */
    private function onDuplicatePostException(DuplicatePostException $e): void {
        $duplicateId = $e->getCode();

        /**
         * Fires just after a post is decided to be duplicate. At this point, no new post is inserted to the database
         * and the saved files are not deleted yet.
         *
         * @param int       $siteIdToCheck   ID of the site
         * @param int       $duplicatePostId Found duplicate post ID
         * @param PostData  $data            Data retrieved from the target post URL
         * @param string    $postUrl         URL of the post
         * @param PostSaver $saver           PostSaver itself
         * @since 1.6.3
         */
        do_action('wpcc/post/after_decided_duplicate', $this->siteIdToCheck, $duplicateId, $this->data, $this->postUrl, $this);

        $this->cancelCrawlingAndDeleteCurrentPost();

        // Notify the user
        $messages = [
            _wpcc('A duplicate post has been found.'),
            sprintf(
                _wpcc('Current URL: %1$s, Duplicate post ID: %2$s, Duplicate post title: %3$s, Site ID: %4$s.'),
                $this->postUrl,
                $duplicateId,
                get_the_title($duplicateId),
                $this->siteIdToCheck
            ),
            _wpcc('The URL is not saved and it is marked as saved so that it will not be tried again.')
        ];

        Informer::add(Information::fromInformationMessage(
            InformationMessage::DUPLICATE_POST,
            implode(' ', $messages),
            InformationType::INFO
        )->setException($e)->addAsLog());
    }

    /**
     * Handles the "cancel saving" exception and notifies the user by adding an information message.
     *
     * @param CancelSavingException $e
     * @since 1.12.0
     */
    private function onCancelSavingException(CancelSavingException $e): void {
        // Invalidate the next page URLs, since the crawling is cancelled.
        $this->nextPageUrl = null;
        $this->nextPageUrls = null;

        // Add an information message about the cancellation
        $messages = [
            $e->getMessage() ?: '',
            _wpcc('Crawling of the URL is stopped due to a cancel request.'),
            _wpcc('Current URL') . ': ' . $this->postUrl,
        ];

        Informer::addInfo(trim(implode(' ', $messages)))
            ->setException($e)
            ->addAsLog();
    }

    /**
     * Handles the "cancel saving and delete" exception and notifies the user by adding an information message.
     *
     * @param CancelSavingAndDeleteException $e
     * @since 1.11.0
     * @uses cancelCrawlingAndDeleteCurrentPost()
     */
    private function onCancelSavingAndDeleteException(CancelSavingAndDeleteException $e): void {
        $deletedPostIds = $this->cancelCrawlingAndDeleteCurrentPost($e->isDeleteUrl());

        $messages = [
            $e->getMessage() ?: '',
            _wpcc('The URL is not saved due to a cancel request.'),
            $e->isDeleteUrl()
                ? _wpcc('The URL is deleted from the database, which means it can be crawled again if it is found in a category page.')
                : _wpcc('It is marked as saved so that it will not be tried again.'),
            $deletedPostIds
                ? _wpcc('Deleted post ID') . ': ' . implode(', ', $deletedPostIds)
                : _wpcc('Because there was no saved post, no post is deleted. Instead, saving operation is stopped.'),
            _wpcc('Current URL') . ': ' . $this->postUrl,
        ];

        Informer::addInfo(trim(implode(' ', $messages)))->setException($e)->addAsLog();
    }

    /**
     * This deletes everything related to the current post, i.e. makes the post detail factories delete what they have
     * saved, deletes the attachments, deletes the saved post (if exists), deletes gallery attachments (if exist). Then,
     * calls {@link resetLastCrawled()} and updates the current URL's saved status so that the URL will not be tried to
     * be crawled again in the future.
     *
     * @param bool $deleteUrl True if the URL should be removed from the database. False if the URL should remain in the
     *                        database and be marked as "saved" so that it will not be crawled again.
     * @return int[] IDs of the deleted posts. If no ID exists, this is an empty array.
     * @since 1.11.0
     */
    private function cancelCrawlingAndDeleteCurrentPost(bool $deleteUrl = false): array {
        // Make the factories delete the things they are concerned with. Make them delete only if there is a
        // saver data. If saver data does not exist, it means they did not save anything, since their savers were
        // not called.
        if ($this->saverData) {
            PostDetailsService::getInstance()->delete($this->bot->getSettingsImpl(), $this->saverData);
        }

        // If there is a PostData, delete the attachments.
        if ($this->data) $this->data->deleteAttachments();

        // If there is a post saved, delete it from the database. If there is a different draft post ID, delete it as well.
        $postIds = array_filter(array_unique([$this->postId, $this->draftPostId]));
        foreach($postIds as $postId) $this->deletePost($postId);

        // If there are gallery attachment IDs, delete them as well.
        if ($this->saverData && $this->saverData->getGalleryAttachmentIds()) {
            foreach($this->saverData->getGalleryAttachmentIds() as $mediaId) wp_delete_post($mediaId, true);
        }

        $this->resetLastCrawled($this->siteIdToCheck);

        // If the URL should be deleted, delete it.
        if ($deleteUrl) {
            Factory::databaseService()->deleteUrl($this->urlTuple->getId());

        } else {
            // Set this URL as saved so that this won't be tried to be saved again and unlock it.
            Factory::databaseService()->updateUrlSavedStatus(
                $this->urlTuple->getId(),
                true,
                null,
                $this->urlTuple->getUpdateCount(),
                false
            );
        }

        return $postIds;
    }

    /**
     * Delete post media, thumbnail and the post itself with ID
     *
     * @param int $postId ID of the post to be deleted
     * @since 1.8.0
     */
    private function deletePost($postId): void {
        if (!$postId) return;

        $this->deleteAttachments($postId);

        // Delete the post without sending it to trash.
        wp_delete_post($postId, true);
    }

    /**
     * Assigns {@link urlToCrawl}, {@link isFirstPage} and {@link urlTuple} instance variables, considering whether
     * this is a recrawl or not.
     *
     * @param int|null $lastCrawledUrlId
     * @throws StopSavingException
     */
    private function prepareUrlTupleToCrawl($lastCrawledUrlId): void {
        global $wpdb;

        // Decide what we're doing. Crawling a next page for the same post, or a new post?
        if($this->nextPageUrl && $lastCrawledUrlId) {
            // We're getting a next page for a post.
            $this->isFirstPage = false;

            $query = "SELECT * FROM " . Factory::databaseService()->getDbTableUrlsName() . " WHERE id = %d";
            $results = $wpdb->get_results($wpdb->prepare($query, $lastCrawledUrlId));

            // If the URL is not found, then reset the cron options for this site and stop.
            if (empty($results)) {
                error_log(
                    "WPCC - There are a next page URL and a last crawled URL ID, but the URL does not exist in database."
                    . " URL ID: " . $lastCrawledUrlId
                    . ", Next Page URL: " . $this->nextPageUrl
                );

                if($this->updateLastCrawled) {
                    $this->resetLastCrawled($this->siteIdToCheck);

                } else {
                    error_log("WPCC - CRON settings for last-crawled are not reset. This may cause a loop where no post will be saved.");
                }

                $this->addError(ErrorType::URL_TUPLE_NOT_EXIST);
                Informer::add(Information::fromInformationMessage(
                    InformationMessage::URL_TUPLE_NOT_EXIST,
                    null,
                    InformationType::ERROR
                )->addAsLog());

                // Stop crawling
                throw new StopSavingException();
            }

            // Get the URL tuple we will work on
            $this->urlTuple = new UrlTuple($results[0]);

            // Set the page url we should crawl
            $this->urlToCrawl = $this->nextPageUrl;

        } else {
            // We're getting a specified post or a random-ish one
            $this->urlTuple = null;
            if ($lastCrawledUrlId) {
                $urlTupleObject = Factory::databaseService()->getUrlById($lastCrawledUrlId);

                if ($urlTupleObject) {
                    $this->urlTuple = new UrlTuple($urlTupleObject);
                }
            }

            if(!$this->urlTuple || (!$this->isRecrawl && $this->urlTuple->isSaved())) {
                // We're getting a new post. Let's find a URL tuple to save.
                $this->urlTuple = $this->getUrlTupleToCrawl($this->siteIdToCheck, $lastCrawledUrlId);

                // If no URL is found, then reset the cron options for this site and stop.
                if($this->urlTuple === null) {
                    error_log("WPCC - No URL is found in the database."
                        . " Site ID to check: " . ($this->siteIdToCheck ? $this->siteIdToCheck : 'does not exist')
                        . ", Last Crawled URL ID: " . ($lastCrawledUrlId ? $lastCrawledUrlId : 'does not exist')
                    );

                    if($this->updateLastCrawled) {
                        $this->resetLastCrawled($this->siteIdToCheck);

                    } else {
                        error_log("WPCC - CRON settings for last-crawled are not reset. This may cause a loop where no post will be saved.");
                    }

                    $this->addError(ErrorType::URL_TUPLE_NOT_EXIST);
                    Informer::add(Information::fromInformationMessage(
                        InformationMessage::URL_TUPLE_NOT_EXIST,
                        null,
                        InformationType::ERROR
                    )->addAsLog());

                    // Stop crawling
                    throw new StopSavingException();
                }
            }

            // Set the page url we should crawl
            $this->urlToCrawl = $this->urlTuple->getUrl();

        }

        if(self::$DEBUG) var_dump($this->urlTuple);

        // Do not proceed if this URL tuple is locked.
        if($this->urlTuple->isLocked()) {
            $this->addError(ErrorType::URL_LOCKED);
            Informer::add(Information::fromInformationMessage(
                InformationMessage::URL_LOCKED,
                null,
                InformationType::ERROR
            )->addAsLog());

            // Stop crawling
            throw new StopSavingException();
        }
    }

    /**
     * Sends a request to the target URL, retrieves a PostData, and assigns it to {@link data}.
     *
     * @throws CancelSavingException
     * @throws CancelSavingAndDeleteException
     * @throws StopSavingException
     * @throws Exception
     * @noinspection PhpDocRedundantThrowsInspection
     */
    private function preparePostData(): void {
        $this->setData($this->bot->crawlPost($this->postUrl, $this->saverData));
        $this->setRequestMade(true);

        // If there is an error with the connection, reset last crawled and set this URL as saved. By this way,
        // this URL won't be tried again in the future.
        if($this->data === null) {
            $this->resetLastCrawled($this->siteIdToCheck);

            $this->addError(ErrorType::URL_COULD_NOT_BE_FETCHED);
            Informer::add(Information::fromInformationMessage(
                InformationMessage::URL_COULD_NOT_BE_FETCHED,
                $this->postUrl,
                InformationType::ERROR
            )->addAsLog());

            // If the URL tuple does not have a post, delete it.
            if(!$this->urlTuple->getSavedPostId()) {
                Factory::databaseService()->deleteUrl($this->urlTuple->getId());

                // Write an error
                error_log("WPCC - The URL cannot be fetched (" . $this->postUrl . "). There was a connection error. The URL is
                    deleted.");

                // Stop saving
                throw new StopSavingException();
            }

            // Set this URL as saved
            Factory::databaseService()->updateUrlSavedStatus(
                $this->urlTuple->getId(),
                true,
                $this->urlTuple->getSavedPostId(),
                $this->urlTuple->getUpdateCount(),
                false
            );

            // If this is a recrawl, mark this URL as recrawled so that it won't be tried again and again.
            if($this->isRecrawl) {
                Factory::databaseService()->updateUrlRecrawledStatus(
                    $this->urlTuple->getId(),
                    $this->urlTuple->getUpdateCount() + 1,
                    false
                );
            }

            // Write an error
            error_log("WPCC - The URL cannot be fetched (" . $this->postUrl . "). There was a connection error. The URL is
                marked as saved now. Last crawled settings are reset.");

            // Stop saving
            throw new StopSavingException();
        }

    }

    /**
     * Prepares {@link nextPageUrl} and {@link nextPageUrls}
     */
    private function prepareNextPageUrl(): void {
        // Reset next page variables and assign them according to the data.
        $this->nextPageUrl = '';

        // If the post should not be paginated, stop.
        if(!$this->data->isPaginate()) return;

        // Get the next page's URL (or URLs) and store it as option
        if($this->data->getNextPageUrl()) {
            // The post has a next page URL on each page.
            $this->nextPageUrl = $this->data->getNextPageUrl();

        } else if($this->data->getAllPageUrls()) {

            if(self::$DEBUG) var_dump("All page URLs are found.");

            // If there is no next page URLs, then this is the first time we crawl this post.
            // First, save all page URLs.
            if(!$this->nextPageUrls || empty($this->nextPageUrls)) {
                if(self::$DEBUG) var_dump('Next Page URLs is false or empty. Get them from the data.');
                // The post has all URLs for pages in a page.
                $this->nextPageUrls = $this->data->getAllPageUrls();

                // Check if the urls array contains the current page. If so, remove the current page.
                foreach ($this->nextPageUrls as $key => &$mUrl) {
                    if ($mUrl["data"] == $this->postUrl) {
                        unset($this->nextPageUrls[$key]);
                        if(self::$DEBUG) var_dump("Unset " . $mUrl);
                    }
                }

                // Reset the keys of the array
                $this->nextPageUrls = array_values(array_map(function($url) {
                    return $url["data"];
                }, $this->nextPageUrls));
            }

            if(self::$DEBUG) var_dump("Next Page URLs: ");
            if(self::$DEBUG) var_dump($this->nextPageUrls);

            // Get the next page URL.
            if(!empty($this->nextPageUrls)) {
                if(self::$DEBUG) var_dump("Next page URLs is not empty. Find next page URL.");
                if(self::$DEBUG) var_dump("Current URL is: " . $this->urlToCrawl);

                // We have next page URLs. Find the next page URL.
                $currentUrlPos = false;
                foreach ($this->nextPageUrls as $key => $url) {
                    if(self::$DEBUG) var_dump("Possible Current URL: " . $url);

                    if ($url == $this->urlToCrawl) {
                        $currentUrlPos = $key;

                        if(self::$DEBUG) var_dump("Current URL pos is found as " . $currentUrlPos . ", which is " . $url);

                        break;
                    }
                }

                // If current URL is found among next page URLs, and it is not the last URL, then we can get the next
                // URL as next page URL.
                if ($currentUrlPos !== false && $currentUrlPos < sizeof($this->nextPageUrls) - 1) {
                    if(self::$DEBUG) var_dump("Current URL position is valid: " . $currentUrlPos . ". Get the next item in the list.");
                    $this->nextPageUrl = $this->nextPageUrls[$currentUrlPos + 1];

                    // If current URL is not found among next page URLs, then get the first URL as next page URL.
                } else if($currentUrlPos === false) {
                    if(self::$DEBUG) var_dump("Current URL Position is false. Get the first URL in the list.");
                    $this->nextPageUrl = $this->nextPageUrls[0];
                }

                // Otherwise, next page URL will be empty, since it is not assigned.

                // Also, since there is no next page, reset all next pages.
                if(!$this->nextPageUrl) $this->nextPageUrls = [];
            }
        }

    }

    /**
     * Checks the content existence and, if it does not exist, sets next page URLs as null. Sets the value of
     * {@link contentExists}.
     */
    private function checkAndReactToContentExistence(): void {
        // Sometimes, next pages may be empty due to a malfunction of the site. Scenario is that the post does not have
        // content on the next page, but there is a link on the page indicating there is a next page. In this case,
        // the crawler cannot find any content in the next page. If this is the case, do not continue to next pages.
        $this->contentExists = true;

        // Get main post template
        $templateMain = $this->getSetting(SettingKey::POST_TEMPLATE_MAIN);
        $clearedTemplateMain = $templateMain;

        // Remove short codes
        // First get predefined short codes
        $allShortCodes = Factory::postService()->getPredefinedShortCodes();

        // Now get user-defined short codes
        $shortCodeSelectors = $this->getSetting(SettingKey::POST_CUSTOM_CONTENT_SHORTCODE_SELECTORS);
        if($shortCodeSelectors) {
            foreach ($shortCodeSelectors as $selector) {
                if (isset($selector["short_code"]) && $selector["short_code"]) {
                    $allShortCodes[] = "[" . $selector["short_code"] . "]";
                }
            }
        }

        // Now remove them from the original raw template
        foreach($allShortCodes as $shortCode) {
            $clearedTemplateMain = str_replace($shortCode, "", $clearedTemplateMain);
        }

        if(self::$DEBUG) var_dump("Cleared Template Main:" . $clearedTemplateMain);
        if(self::$DEBUG) var_dump("Original Template Main: " . $templateMain);
        if(self::$DEBUG) var_dump($allShortCodes);
        if(self::$DEBUG) var_dump(mb_strlen($this->data->getTemplate()) <= mb_strlen($clearedTemplateMain));

        // Now, check if the prepared template's length is greater than that of short-codes-removed template. So, if
        // the prepared template's length is less, it means the page is empty. Hence, we do not have any variables in
        // the page.
        if (!$this->data->getTemplate() || mb_strlen($this->data->getTemplate()) <= mb_strlen($clearedTemplateMain)) {
            $this->nextPageUrl = null;
            $this->nextPageUrls = null;
            $this->contentExists = false;
        }
    }

    /**
     * Prepares post data array that contains the required WordPress post variables and their values, using {@link data}.
     *
     * @return array Prepared post data array
     * @throws AlreadySavedPageException
     */
    private function createWPPostData() {
        // Get the author and post status defined in the post data. If these exist, we will use them.
        $postAuthor = $this->data->getAuthorId();
        $postStatus = $this->data->getPostStatus();

        // Get general settings
        // If this site has different settings, then use them.
        if($this->getSetting(SettingKey::DO_NOT_USE_GENERAL_SETTINGS)) {
            $allowComments  = $this->getSetting(SettingKey::WPCC_ALLOW_COMMENTS);
            $postStatus     = $postStatus ?: $this->getSetting(SettingKey::WPCC_POST_STATUS);
            $postType       = $this->getSetting(SettingKey::WPCC_POST_TYPE);
            $postAuthor     = $postAuthor ?: $this->getSetting(SettingKey::WPCC_POST_AUTHOR);
            $tagLimit       = $this->getSetting(SettingKey::WPCC_POST_TAG_LIMIT);
            $postPassword   = $this->getSetting(SettingKey::WPCC_POST_PASSWORD);

            // Otherwise, go on with general settings.
        } else {
            $allowComments  = get_option(SettingKey::WPCC_ALLOW_COMMENTS);
            $postStatus     = $postStatus ?: get_option(SettingKey::WPCC_POST_STATUS);
            $postType       = get_option(SettingKey::WPCC_POST_TYPE);
            $postAuthor     = $postAuthor ?: get_option(SettingKey::WPCC_POST_AUTHOR);
            $tagLimit       = get_option(SettingKey::WPCC_POST_TAG_LIMIT, 0);
            $postPassword   = get_option(SettingKey::WPCC_POST_PASSWORD);
        }

        // Prepare the data
        if($this->data->getPreparedTags() && $tagLimit && ((int) $tagLimit) > 0 && sizeof($this->data->getPreparedTags()) > $tagLimit) {
            $this->data->setPreparedTags(array_slice($this->data->getPreparedTags(), 0, $tagLimit));
        }

        // Check if we have a draft post ID to edit
        $content = '';
        $sourceUrls = [];
        $post = null;

        if($this->draftPostId && $post = get_post($this->draftPostId)) {

            if(!$this->isFirstPage) {

                $content = $post->post_content;
                if(!empty($content) && !$this->getSettingForCheckbox(SettingKey::POST_SAVE_AS_SINGLE_PAGE)) {
                    $content = $content . "<!-- wp:nextpage -->\n<!--nextpage-->\n<!-- /wp:nextpage -->";
                }

                // Get source URLs
                $sourceUrls = get_post_meta($this->draftPostId, $this->postMetaSourceUrls, true);

                if(!$sourceUrls) $sourceUrls = [];
            }
        }
        
        // Stop if the current URL was already crawled. If this is not the first page of this post, the same URL might
        // have been crawled due to incorrect settings. For example, users sometimes cannot properly configure the next
        // page selectors such that the next page points to the first page of the post in the last page of the post,
        // creating an infinite loop. In some cases, it is not even possible to correctly retrieve the next page URL
        // because of the target site's design. Here, we prevent crawling the same page more than once. By this way,
        // the plugin is able to stop an infinite loop as well as crawl the pages correctly in cases that making the
        // plugin not find the next page URL in the last page of the posts.
        /** @var null|AlreadySavedPageException $exception */
        $exception = null;
        if (in_array($this->postUrl, $sourceUrls)) {
            // Stop crawling the next pages
            $this->nextPageUrl = null;
            $this->nextPageUrls = null;
            $this->contentExists = false;

            // Notify the user
            Informer::addInfo(sprintf(
                _wpcc('Current URL (%1$s) was already crawled. Crawling of this post has been stopped without saving the data retrieved from the current URL to prevent an infinite loop. Already saved URLs: %2$s'),
                $this->postUrl,
                implode(', ', $sourceUrls)
            ))->addAsLog();
            
            // Define an exception that will be thrown later in this method
            $exception = new AlreadySavedPageException();

        } else {
            // Append current source URL
            $sourceUrls[] = $this->postUrl;
        }

        /*
         * PREPARE POST DATA
         */

        // If post author is not set, then set the first administrator as post author.
        if(!$postAuthor) {
            $userQuery = new WP_User_Query([
                'role'      => 'Administrator',
                'fields'    =>  'ID',
                'number'    =>  1
            ]);
            $postAuthor = $userQuery->get_results()[0];
        }

        $postData = [
            'ID' => $this->draftPostId ? $this->draftPostId : 0,
            // If there is a next page to append to this post, then make this post's status draft no matter what.
            // Otherwise, go on with the settings.
            'post_status'       => $this->nextPageUrl           ? PostStatus::DRAFT       : ($postStatus ? $postStatus : PostStatus::DRAFT),
            'post_type'         => post_type_exists($postType)  ? $postType     : 'post',
            'post_password'     => $postPassword                ? $postPassword : '',
            'post_category'     => [$this->urlTuple->getCategoryId()],
            'meta_input'        => [
                $this->postMetaSourceUrls => $sourceUrls
            ],
        ];

        // If this is the first page of the newly created post.
        if(!$this->isRecrawl && $this->isFirstPage) {
            // Set the date
            $postDate = $this->data->getDateCreatedString();
            $postData["post_date"] = $postDate;

            // Set the slug if there exists one
            if ($this->data->getSlug()) $postData['post_name'] = $this->data->getSlug();
        }

        // If content exists, append it to the content of the original post
        if($this->contentExists) {
            $postData['post_content'] = $content . $this->data->getTemplate();

            // Otherwise, do not change the content.
        } else if($post) {
            $postData['post_content'] = $post->post_content;
        }

        // If this is the first page, set other required data
        if($this->isFirstPage || !$post) {
            $postData = array_merge($postData, [
                'post_author'       => $postAuthor,
                'post_title'        => $this->data->getTitle()        ? $this->data->getTitle() : '',
                'post_excerpt'      => $this->data->getExcerpt()      ? $this->data->getExcerpt()["data"] : '',
                'comment_status'    => $allowComments                 ? 'open' : 'closed',
                'tags_input'        => $this->data->getPreparedTags() ? $this->data->getPreparedTags() : ''
            ]);

            if($post) {
                $postData = array_merge($postData, [
                    'post_date'         => $post->post_date,
                    'post_date_gmt'     => $post->post_date_gmt,
                    'post_name'         => $post->post_name,
                    'guid'              => $post->guid,
                ]);
            }

        // Set everything from the current found post. Even if this is an update, WP requires some variables again.
        } else if($post) {
            $postData = array_merge($postData, [
                'post_author'       => $post->post_author,
                'post_title'        => $post->post_title,
                'post_excerpt'      => $post->post_excerpt,
                'comment_status'    => $post->comment_status,
                'post_date'         => $post->post_date,
                'post_date_gmt'     => $post->post_date_gmt,
                'post_name'         => $post->post_name,
                'guid'              => $post->guid,
            ]);
        }

        // If there is an exception, throw it.
        if ($exception) {
            throw $exception->setWpPostData($postData);
        }

        return $postData;
    }

    /**
     * Checks if the post is duplicate. If it is, deletes its attachments, deletes the draft post, resets last-crawled
     * CRON metas, marks the URL tuple as saved.
     *
     * @throws DuplicatePostException If the post is duplicate and saving process should no longer continue
     */
    private function handleIfDuplicate(): void {
        // No need to do this when recrawling.
        if ($this->isRecrawl) return;

        // Try to find a duplicate post
        $duplicatePostId = $this->isDuplicate($this->urlTuple->getUrl(), $this->data->getWpPostData(), $this->isFirstPage, !$this->nextPageUrl);

        // If none, stop.
        if (!$duplicatePostId) return;

        // This is a duplicate post. Throw a duplicate post exception.
        throw new DuplicatePostException(_wpcc("A duplicate post is found."), $duplicatePostId);
    }

    /**
     * Inserts given post data into the database. This also sets {@link postId} as the inserted post's ID.
     *
     * @throws StopSavingException
     */
    private function insertPostData(): void {
        // Get the post data
        $postData = $this->data->getWpPostData();

        /**
         * Modify post data before it is saved to the database.
         *
         * @param array         $postData      The data that will be passed to wp_insert_post function.
         * @param PostData      $data          Data retrieved from the target post page's source code
         * @param PostBot       $bot           PostBot object used to retrieve the data from the target page
         * @param PostSaver     $saver         PostSaver itself
         * @param int           $siteIdToCheck ID of the site that stores the settings
         * @param string        $postUrl       URL of the post
         * @param UrlTuple|null $urlTuple      A {@link UrlTuple} containing info about the URL.
         * @param bool          $isRecrawl     True if this is fired for a recrawl.
         *
         * @return array|null $postData Modified post data. Return null if you do not want to save the post.
         * @since 1.6.3
         */
        $postData = apply_filters('wpcc/post/wp-post', $postData, $this->data, $this->bot, $this, $this->siteIdToCheck, $this->postUrl, $this->urlTuple, $this->isRecrawl);

        // If the post data is null, do not save the post.
        if($postData === null) throw new StopSavingException();

        /**
         * Fires just before a post is inserted/updated.
         *
         * @param array         $postData      Data that will be used to create/update a post in the database. If 'ID'
         *                                     key has a valid integer value, this means this is fired for an update.
         * @param PostData      $data          Data retrieved from the target site according to the site settings
         * @param PostSaver     $saver         PostSaver itself
         * @param int           $siteIdToCheck ID of the site for which the post is retrieved
         * @param string        $postUrl       URL of the post
         * @param UrlTuple|null $urlTuple      A {@link UrlTuple} containing info about the URL.
         * @param bool          $isRecrawl     True if this is a recrawl.
         * @param bool          $isFirstPage   True if this is the first page of the post
         * @since 1.6.3
         */
        do_action('wpcc/post/before_save', $postData, $this->data, $this, $this->siteIdToCheck, $this->postUrl, $this->urlTuple, $this->isRecrawl, $this->isFirstPage);

        //

        $this->setPostId(wp_insert_post($postData));

        //

        /**
         * Fires just after a post is inserted/updated.
         *
         * @param array     $postData      Data that was used to create/update a post in the database. If 'ID' key has
         *                                 a valid integer value, this means this is fired for an update.
         * @param PostData  $data          Data retrieved from the target site according to the site settings
         * @param PostSaver $saver         PostSaver itself
         * @param int       $siteIdToCheck ID of the site for which the post is retrieved
         * @param string    $postUrl       URL of the post
         * @param array     $urlTuple      An array containing the URL data. The keys are columns of the DB table storing the URLs.
         * @param bool      $isRecrawl     True if this is a recrawl.
         * @param int       $postId        ID of the saved post
         * @param bool      $isFirstPage   True if this is the first page of the post
         * @since 1.6.3
         */
        do_action('wpcc/post/after_save', $postData, $this->data, $this, $this->siteIdToCheck, $this->postUrl, $this->urlTuple, $this->isRecrawl, $this->postId, $this->isFirstPage);

        if($this->draftPostId && $this->postId != $this->draftPostId) {
            error_log("Draft post ID ({$this->draftPostId}) and inserted post ID ({$this->postId}) are different.");
        }

        if(self::$DEBUG) var_dump("Inserted Post ID: " . $this->postId);

        // Set the WP post data to PostData, since $postData might have been modified
        $this->data->setWpPostData($postData);
    }

    /**
     * Sets the custom post taxonomy if the post's category belongs to a custom category taxonomy.
     *
     * @since 1.8.0
     */
    private function saveCategories(): void {
        // Do this only in the first page
        if (!$this->isFirstPage) return;

        // Get the categories
        $categories = Utils::getCategories($this->getSettingsImpl());

        // Find the selected category's taxonomy
        $taxonomy = null;
        foreach($categories as $categoryItem) {
            $id = Utils::array_get($categoryItem, 'id');
            if (!$id) continue;

            if ($id == $this->urlTuple->getCategoryId()) {
                $taxonomy = Utils::array_get($categoryItem, 'taxonomy');
                break;
            }
        }

        // If a taxonomy is not found, use the default WP category taxonomy
        if (!$taxonomy) $taxonomy = 'category';

        // Set the categories under the defined taxonomy
        $this->insertAndSetPostCategories($taxonomy);
    }

    /**
     * Sets the category of the post
     *
     * @param string $catTaxonomy Category taxonomy
     * @since 1.8.0
     */
    private function insertAndSetPostCategories($catTaxonomy = 'category'): void {
        // If this is a recrawl, remove already-existing categories.
        if ($this->isRecrawl) {
            wp_set_post_terms($this->postId, [], $catTaxonomy, false);
        }

        // Define the category taxonomy and get the category names that should be added as the post's categories.
        $categoryNames = $this->data->getCategoryNames();

        // Get the post category defined in the category map
        $term = get_term_by('id', $this->urlTuple->getCategoryId(), $catTaxonomy);
        $mainCatTermId = $term && isset($term->term_id) ? $term->term_id : null;

        // If there is no category name, set the main category ID as the category ID specified in the category map and
        // stop.
        if (!$categoryNames) {
            if($mainCatTermId !== null) {
                wp_set_post_terms($this->postId, (int) $this->urlTuple->getCategoryId(), $catTaxonomy, false);
            }

            return;
        }

        // Get whether the user wants to use the category ID defined in the category map or not
        $doNotAddCategoryDefinedInMap = $this->getSettingForCheckbox(SettingKey::POST_CATEGORY_DO_NOT_ADD_CATEGORY_IN_MAP);

        // Insert/retrieve the category term IDs.
        $categoryIds = $this->insertPostCategories($categoryNames, $catTaxonomy, $doNotAddCategoryDefinedInMap ? null : $mainCatTermId);

        // If there is no category, stop.
        if (!$categoryIds) return;

        // Set the category IDs of the post
        $result = wp_set_post_terms($this->postId, $categoryIds, $catTaxonomy, false);
        if (is_wp_error($result)) {
            $info = Informer::addError(_wpcc('Category IDs could not be assigned to the post.'));
            if (is_a($result, WP_Error::class)) {
                /** @var WP_Error $result */
                $info->setDetails($info->getDetails() . ' ' . $result->get_error_message());
            }

            $info->addAsLog();
        }
    }

    /**
     * Inserts/retrieves product categories considering the settings.
     *
     * @param array    $categoryNames Category names to be set as product's category, possibly retrieved from
     *                                {@link WooCommerceDetailData::getCategoryNames()}. See
     *                                {@link WooCommerceDetailData::getCategoryNames()} for details.
     * @param string   $catTaxonomy   Taxonomy name to which the categories inserted. Possible 'product_cat'
     * @param int|null $mainCatTermId Category ID that will be the parent of the inserted categories. Null if you do
     *                                not want to set a parent to the to-be-inserted categories.
     * @return array Array of taxonomy IDs that can be assigned to the product
     * @since 1.8.0
     */
    private function insertPostCategories($categoryNames, $catTaxonomy, $mainCatTermId) {
        // Insert/retrieve the category taxonomies
        $categoryIds = [];
        foreach($categoryNames as $catNameValue) {
            // If the category name value is not an array, make it an array to keep the algorithm simple.
            if (!is_array($catNameValue)) $catNameValue = [$catNameValue];

            // We need to add all categories hierarchically.

            // Store the parent term ID.
            $parentTermId = $mainCatTermId;

            $isError = false;
            $hierarchicalCatIds = [];

            // Add the categories one by one
            foreach($catNameValue as $catName) {
                $args = $parentTermId !== null ? ['parent' => $parentTermId] : [];
                $termId = Utils::insertTerm($catName, $catTaxonomy, $args);

                // If a term ID could not be retrieved, stop.
                if ($termId === null) {
                    $isError = true;
                    break;
                }

                // Add the term ID to the hierarchical category IDs
                $hierarchicalCatIds[] = $termId;

                // Set this term ID as the previous term ID so that it can be set as the next category's parent.
                $parentTermId = $termId;
            }

            // If there was an error, it means at least one of the categories could not be inserted. In this case,
            // do not set successfully-retrieved category IDs as the category of the post, since the user wants
            // all of the categories.
            if ($isError) continue;

            if ($hierarchicalCatIds) $categoryIds = array_merge($categoryIds, $hierarchicalCatIds);

        }

        return $categoryIds;
    }

    /*
     *
     */

    /**
     * Deletes already-existing attachments when updating the post, and when this is the first page of the post.
     */
    private function maybeDeleteAttachments(): void {
        // Do this only when this is the first page, we are updating the post, and a post ID exists
        if(!$this->isFirstPage || !$this->isRecrawl || !$this->postId) return;

        $this->deleteAttachments($this->postId);
    }

    /**
     * Deletes the attached images of the post, including the featured image. The image files are deleted as well.
     *
     * @param int|null $postId The ID of the post whose attachments will be deleted
     * @since 1.12.0
     */
    private function deleteAttachments(?int $postId): void {
        if (!$postId) return;

        $protectedIds = SettingService::getProtectedAttachmentIds();

        // Delete the attachments
        $alreadyAttachedMedia = get_attached_media('image', $postId);
        foreach($alreadyAttachedMedia as $mediaPost) {
            $attachmentId = $mediaPost->ID;

            // Do not delete this attachment if it is protected
            if (in_array($attachmentId, $protectedIds)) {
                continue;
            }

            wp_delete_post($attachmentId);
        }

        // Delete the thumbnail. Do not delete it, if it is protected.
        Utils::deletePostThumbnail($postId, function(int $attachmentId) use ($protectedIds) {
            return !in_array($attachmentId, $protectedIds);
        });
    }

    /**
     * Saves featured image of the post
     */
    private function saveFeaturedImage(): void {
        // If this is not the first page or the post ID does not exist, stop.
        if(!$this->isFirstPage || !$this->postId) return;

        // If there is a featured image ID, use it.
        $imageId = $this->data ? $this->data->getFeaturedImageId() : null;
        if ($imageId !== null) {
            set_post_thumbnail($this->postId, $imageId);
            return;
        }

        // Get the thumbnail image file path
        $mediaFile = null;
        if($this->urlTuple->getThumbnailUrl()) {
            $thumbnailUrl = $this->urlTuple->getThumbnailUrl();

            // If there is no thumbnail image URL, stop.
            if (!$thumbnailUrl) return;

            // Prepare the thumbnail URL
            try {
                $thumbnailUrl = $this->bot->resolveUrl($thumbnailUrl);
            } catch (Exception $e) {
                Informer::addError(_wpcc('URL could not be resolved') . ' - ' . $thumbnailUrl)->addAsLog();
            }

            // Save the featured image
            $file = MediaService::getInstance()
                ->saveMedia($thumbnailUrl, MediaSavingOptions::fromSiteSettings($this->getSettingsImpl()));
            if (!$file) return;

            $mediaFile = new MediaFile($thumbnailUrl, $file['file']);

        } else if($this->data->getThumbnailData()) {
            $mediaFile = $this->data->getThumbnailData();
        }

        // If there is no file, stop.
        if (!$mediaFile) return;

        // Save as attachment and get the attachment id.
        try {
            $thumbnailAttachmentId = MediaService::getInstance()->insertMedia($this->postId, $mediaFile);
        } catch (Exception $e) {
            Informer::addError(_wpcc('Media file does not have a local path.'))->addAsLog();
            return;
        }

        // Set the media ID
        $mediaFile->setMediaId($thumbnailAttachmentId);

        // Set this attachment as post thumbnail
        set_post_thumbnail($this->postId, $thumbnailAttachmentId);
    }

    /**
     * Saves meta keywords
     */
    private function saveMetaKeywords(): void {
        // If this is not the first page or the post ID does not exist, stop.
        if(!$this->isFirstPage || !$this->postId) return;

        if(!$this->data->getMetaKeywords()) return;

        $key = get_option(SettingKey::WPCC_META_KEYWORDS_META_KEY);
        if (!$key) return;

        Utils::savePostMeta($this->postId, $key, $this->data->getMetaKeywords(), true);
    }

    /**
     * Saves meta description
     */
    private function saveMetaDescription(): void {
        // If this is not the first page or the post ID does not exist, stop.
        if(!$this->isFirstPage || !$this->postId) return;

        if(!$this->data->getMetaDescription()) return;

        $key = get_option(SettingKey::WPCC_META_DESCRIPTION_META_KEY);
        if(!$key) return;

        Utils::savePostMeta($this->postId, $key, $this->data->getMetaDescription(), true);
    }

    /**
     * Saves attachments
     *
     * @return array Gallery attachment IDs
     */
    private function saveAttachments() {
        if(!$this->postId || !$this->data->getAttachmentData()) return [];

        $galleryAttachmentIds = [];

        foreach($this->data->getAttachmentData() as $mediaFile) {
            // Insert the media
            try {
                $attachmentId = MediaService::getInstance()->insertMedia($this->postId, $mediaFile);
            } catch (Exception $e) {
                Informer::addError(_wpcc('Media file does not have a local path.'))->addAsLog();
                continue;
            }

            // Set the media ID
            $mediaFile->setMediaId($attachmentId);

            if($mediaFile->isGalleryImage()) {
                $galleryAttachmentIds[] = $attachmentId;
            }

        }

        // Add srcset attributes to media elements in the content if the user wants it.
        if ($this->bot->getSettingForCheckbox(SettingKey::WPCC_POST_SET_SRCSET)) {
            $this->setMediaSrcSetsInContent();
        }

        return $galleryAttachmentIds;
    }

    /**
     * Updates the post content such that media elements in the content have srcset attributes.
     *
     * @since 1.8.0
     */
    private function setMediaSrcSetsInContent(): void {
        // Change the template by adding srcset attributes.
        $oldTemplate = $this->addMediaSrcSetsToTemplate();

        // If there was no change, no need to continue.
        if ($oldTemplate === false) return;

        // Update the post content
        $this->updatePostContentForCurrentTemplate($oldTemplate);
    }

    /**
     * Modifies the current template of {@link $data} by adding srcset attributes to media elements.
     *
     * @return string|false The old template if there is a change in the template. Otherwise, false.
     * @since 1.8.0
     */
    private function addMediaSrcSetsToTemplate() {
        // If the function that creates srcset does not exist, stop.
        if (!function_exists('wp_get_attachment_image_srcset')) return false;

        // If there is no attachment data, stop.
        if (!$this->data->getAttachmentData()) return false;

        // Get the template
        $template = $this->data->getTemplate();
        if (!$template) return false;

        // Create a dummy crawler for the post template
        $dummyTemplateCrawler = $this->bot->createDummyCrawler($template);

        foreach($this->data->getAttachmentData() as $mediaFile) {
            // If the media does not have an ID, continue with the next one.
            if (!$mediaFile->getMediaId()) continue;

            // Get the srcset
            $srcSet = wp_get_attachment_image_srcset($mediaFile->getMediaId());
            if (!$srcSet) continue;

            // Add the srcset to the corresponding media element
            $this->bot->modifyMediaElement($dummyTemplateCrawler, $mediaFile, function(MediaFile $mediaFile, DOMElement $element) use (&$srcSet) {
                $element->setAttribute('srcset', $srcSet);
            });
        }

        // Get the modified content
        $newTemplate = $this->bot->getContentFromDummyCrawler($dummyTemplateCrawler);

        // If there is no change, stop.
        if ($newTemplate === $template) return false;

        // Update the post content
        $this->data->setTemplate($newTemplate);

        return $template;
    }

    /**
     * Updates the post content to reflect changes made to the current template which will be retrieved from
     * {@link $data} ({@link PostData::getTemplate()}).
     *
     * @param string $oldTemplate Old template that will be changed with the new one which will be retrieved from
     *                            {@link $data}
     * @since 1.8.0
     */
    private function updatePostContentForCurrentTemplate($oldTemplate): void {
        // If there is no post ID, we cannot update the content.
        if (!$this->postId) return;

        $wpPostData = $this->data->getWpPostData();
        $newPostContent = $this->data->getTemplate();

        // If this is not the first page, it means the template was appended to the content of the previous pages.
        if (!$this->isFirstPage) {
            // Get the existing content
            $existingContent = Utils::array_get($wpPostData, 'post_content', null);

            // If there is an existing content
            if ($existingContent) {
                // Replace the unmodified template with the modified one in the existing content. By this way,
                // previous content will not be changed and the changes will be reflected properly.
                $newPostContent = str_replace($oldTemplate, $this->data->getTemplate(), $existingContent);
            }

        }

        // Update the post's content with new post content
        wp_update_post([
            'ID'           => $this->postId,
            'post_content' => $newPostContent
        ]);

        // Update content of WP post data in the PostData
        $wpPostData['post_content'] = $newPostContent;
        $this->data->setWpPostData($wpPostData);
    }

    /**
     * Saves custom post meta
     */
    private function saveCustomMeta(): void {
        if(!$this->postId || !$this->data->getCustomMeta()) return;

        $alreadyDeletedKeys = [];
        foreach($this->data->getCustomMeta() as $metaData) {
            $metaValue  = $metaData->getData();
            $metaKey    = $metaData->getKey();

            // Delete old meta values first when updating. Do this only when the first page is being crawled, and the
            // meta key is not deleted previously.
            if($this->isFirstPage && $this->isRecrawl && !in_array($metaKey, $alreadyDeletedKeys)) {
                delete_post_meta($this->postId, $metaKey);
                $alreadyDeletedKeys[] = $metaKey;
            }

            // If it must be saved as multiple
            if($metaData->isMultiple()) {

                // If the value is array
                if(is_array($metaValue)) {
                    if(empty($metaValue)) continue;

                    // Add each value
                    foreach($metaValue as $value) {
                        add_post_meta($this->postId, $metaKey, $value, false);
                    }

                } else {
                    // Otherwise, add it directly
                    add_post_meta($this->postId, $metaKey, $metaValue, false);
                }

            } else {
                // Otherwise, save it as a single post meta.
                update_post_meta($this->postId, $metaKey, $metaValue);
            }
        }

    }

    /**
     * Saves custom post taxonomies
     * @since 1.8.0
     */
    private function saveCustomTaxonomies(): void {
        if(!$this->postId || !$this->data->getCustomTaxonomies()) return;

        // Delete old taxonomy values first when updating. Do this only when the first page is being crawled.
        if($this->data->getCustomTaxonomies() && $this->isFirstPage && $this->isRecrawl) {
            $taxNames = array_unique(array_map(function($v) {
                return $v->getTaxonomy();
            }, $this->data->getCustomTaxonomies()));

            // Taxonomies saved via wp_insert_post are removed here. They must not be removed. For example, post_tag
            // taxonomy is saved via wp_insert_post prior to calling this method. Hence, already-saved tags are removed
            // here when recrawling a post. This must not be the case. Exclude already-saved taxonomy names such as
            // "post_tag". These values are already overwritten.
            $excluded = ['post_tag', 'category'];
            $taxNames = array_values(array_diff($taxNames, $excluded));

            if($taxNames) wp_delete_object_term_relationships($this->postId, $taxNames);
        }

        foreach($this->data->getCustomTaxonomies() as $item) {
            $taxValue = $item->getData();
            $taxName  = $item->getTaxonomy();
            $isAppend = $item->isAppend();

            // Make sure the value is an array.
            if (!is_array($taxValue)) $taxValue = [$taxValue];

            // Save them as terms
            $termIds = [];
            foreach($taxValue as $tv) {
                $termId = Utils::insertTerm($tv, $taxName);
                if (!$termId) continue;

                $termIds[] = $termId;
            }

            // If there is no term ID, continue with the next one.
            if (!$termIds) continue;

            wp_set_post_terms($this->postId, $termIds, $taxName, $isAppend);
        }
    }

    /**
     * Triggers the `save_post` hook of WordPress, if the settings are configured accordingly. Normally, this hook is
     * called when a post is created/updated. In this class, after a post is saved, more information is added to the
     * post, such as post meta, taxonomies, and attachments. Since this is done after the `save_post` hook is called,
     * the other plugins/themes that listen to this hook does not get notified. In this case, the `save_post` hook needs
     * to be triggered so that these plugins/themes can work with the additional information. This method lets the users
     * trigger the hook.
     *
     * @since 1.12.0
     */
    private function maybeTriggerSavePostHook(): void {
        if ($this->postId === null) return;

        // If the hook must not be triggered, stop.
        $trigger = $this->getSettingForCheckbox(SettingKey::POST_TRIGGER_SAVE_POST_HOOK);
        if (!$trigger) return;

        // We could directly trigger the save_post hook, but there might be other hooks that are normally triggered when
        // a post is saved and that the other plugins listen to. To be on the safe side, call the update function which
        // will trigger the save_post hook and others.
        wp_update_post(['ID' => $this->postId]);
    }

    /**
     * Does the updates for post crawling/recrawling event
     *
     * @param int         $siteId           Last updated site ID
     * @param int         $lastCrawledUrlId ID of the URL from the urls table which is crawled
     * @param string|null $nextPageUrl      Next page URL
     * @param array|null  $nextPageUrls     Next page URLs
     * @param int|null    $draftPostId      Draft post ID
     */
    private function updateLastCrawled($siteId, $lastCrawledUrlId, $nextPageUrl, $nextPageUrls, $draftPostId): void {
        Utils::savePostMeta($siteId, $this->getCronKeyFactory()->getLastCrawledUrlIdKey(),  $lastCrawledUrlId,                     true);
        Utils::savePostMeta($siteId, $this->getCronKeyFactory()->getPostNextPageUrlKey(),   $nextPageUrl,                          true);
        Utils::savePostMeta($siteId, $this->getCronKeyFactory()->getPostNextPageUrlsKey(),  $nextPageUrls,                         true);
        Utils::savePostMeta($siteId, $this->getCronKeyFactory()->getPostDraftIdKey(),       $draftPostId ? $draftPostId : '',   true);
        Utils::savePostMeta($siteId, $this->getCronKeyFactory()->getLastCrawledAtKey(),     current_time('mysql'),           true);

        // Set last crawled site id if there is no draft post ID. By this way, if there is a paged post crawling in progress,
        // before we get a post from another site, we finish crawling all pages of current post.
        if(!$draftPostId) update_option($this->isRecrawl ? $this->optionLastRecrawledSiteId : $this->optionLastCrawledSiteId, $siteId, false);
    }

    /**
     * Reset CRON metas about last-crawled URL
     *
     * @param int $siteId ID of the site
     */
    public function resetLastCrawled($siteId): void {
        $this->updateLastCrawled($siteId, null, null, null, null);
    }

    /**
     * Get a URL tuple to crawl. This method is good for crawling URLs uniformly, by getting a URL from a different
     * category.
     *
     * @param int $siteId Site ID for which a URL tuple will be retrieved
     * @param int $lastCrawledUrlId Last crawled URL id from urls table
     * @return null|UrlTuple Null or found URL tuple as object
     */
    public function getUrlTupleToCrawl($siteId, $lastCrawledUrlId): ?UrlTuple {
        global $wpdb;
        $tableName = Factory::databaseService()->getDbTableUrlsName();

        // If last crawled URL id is null, then get the first URL that needs to be saved.
        if($lastCrawledUrlId === null) {
            // Get the last crawled URL ID instead of getting the first found URL ID that needs saving.
            $query = "SELECT * FROM $tableName WHERE is_saved = TRUE AND is_locked = FALSE AND saved_post_id IS NOT NULL AND post_id = %d ORDER BY saved_at DESC LIMIT 1";
            $results = $wpdb->get_results($wpdb->prepare($query, $siteId));

            // Then, if a URL is found, call this method with that URL ID so that another URL ID from a different
            // category can be found.
            if(!empty($results)) return $this->getUrlTupleToCrawl($siteId, $results[0]->id);

            // Otherwise, if there is no last crawled URL, get the first URL that needs to be saved.
            $query = "SELECT * FROM $tableName WHERE is_saved = FALSE AND is_locked = FALSE AND saved_post_id IS NULL AND post_id = %d LIMIT 1";
            $results = $wpdb->get_results($wpdb->prepare($query, $siteId));

            return empty($results) ? null : new UrlTuple($results[0]);
        }

        // Get the last crawled URL as object from the table
        $query = "SELECT * FROM $tableName WHERE id = %d";
        $results = $wpdb->get_results($wpdb->prepare($query, $lastCrawledUrlId));

        // If the URL is not found in the table, then get the first URL that needs to be saved or return null.
        // Recalling this method with a null lastCrawledSiteId will do the job.
        if(empty($results)) {
            return $this->getUrlTupleToCrawl($siteId, null);
        }

        // Get the tuple as object
        $lastCrawledUrlTuple = $results[0];

        // Get reference category ID and try to get a URL for the next category.
        $referenceCategoryId = $lastCrawledUrlTuple->category_id;

        // Find all categories with an unsaved URL for the target site ID.
        $query = "SELECT DISTINCT category_id FROM $tableName  WHERE is_saved = FALSE AND is_locked = FALSE AND saved_post_id IS NULL AND post_id = %d";
        $categoryIds = $wpdb->get_results($wpdb->prepare($query, $siteId));

        // If there is no category, it means there is no URL to be saved. Return null.
        if(empty($categoryIds)) return null;

        // Try to find a URL with a category different than the reference category. If there is no other category, then
        // find a URL with the reference category ID.
        $referenceCategoryPos = null;
        foreach($categoryIds as $key => $categoryIdObject) {
            if($categoryIdObject->category_id == $referenceCategoryId) {
                $referenceCategoryPos = $key;
                break;
            }
        }

        // If the reference category is not found, get the first category in the list.
        // If the reference category is the last item in the list, get the first category in the list.
        // Otherwise, get the category next to the reference category.
        $targetCategoryId = null;
        if($referenceCategoryPos === null || $referenceCategoryPos == sizeof($categoryIds) - 1) {
            $targetCategoryId = $categoryIds[0]->category_id;
        } else {
            $targetCategoryId = $categoryIds[$referenceCategoryPos + 1]->category_id;
        }

        // Now, get a URL that needs to be saved and belongs to the target site ID and target category ID.
        $query = "SELECT * FROM $tableName WHERE post_id = %d AND category_id = %d AND is_saved = FALSE AND is_locked = FALSE AND saved_post_id IS NULL ORDER BY id ASC LIMIT 1";
        $results = $wpdb->get_results($wpdb->prepare($query, [$siteId, $targetCategoryId]));

        // The results cannot be empty according to the logic. Return the first found URL tuple.
        return new UrlTuple($results[0]);
    }

    /**
     * Check if a post is duplicate considering the current settings set by {@link SettingsTrait::setSettings}.
     *
     * @param string     $url         URL of the post
     * @param array|null $postData    An array having keys named as columns in wp_posts table. And their values, of
     *                                course.
     * @param bool       $isFirstPage True if this check is done for the first page of the post.
     * @param bool       $isLastPage  True if this check is done for the last page of the post.
     * @return false|int Previously saved post ID if this is a duplicate. Otherwise, false.
     */
    public function isDuplicate($url, $postData, $isFirstPage, $isLastPage) {
        // If this is not the first and the last page, no need to check for duplicate.
        if(!$isFirstPage && !$isLastPage) return false;

        // Get the current post ID
        $currentPostId = Utils::array_get($postData, "ID");
        if(!$currentPostId) $currentPostId = 0;

        // Get the settings for duplicate checking
        $duplicateCheckSettingValues = $this->getSetting(SettingKey::DUPLICATE_CHECK_TYPES);

        // The values are stored under 0 key. So, make sure 0 key exists.
        if(!$duplicateCheckSettingValues || !isset($duplicateCheckSettingValues[0])) return false;

        $values = $duplicateCheckSettingValues[0];
        $checkUrl = isset($values[PostSaver::DUPLICATE_CHECK_URL]);
        $checkTitle = isset($values[PostSaver::DUPLICATE_CHECK_TITLE]);
        $checkContent = isset($values[PostSaver::DUPLICATE_CHECK_CONTENT]);

        global $wpdb;

        $id = null;

        // If this is the first page, check URL and title
        if($isFirstPage) {
            // Check the URL
            if($checkUrl && $url) {
                // Check the URL with and without a trailing slash
                $query = "SELECT post_id
                    FROM {$wpdb->postmeta}
                    WHERE meta_key = '{$this->postMetaPostFirstPageUrl}'
                      AND (meta_value = %s OR meta_value = %s)
                      AND post_id <> %d;
                ";
                $id = $wpdb->get_var($wpdb->prepare($query, trailingslashit($url), untrailingslashit($url), $currentPostId));
            }

            // Check the title
            if(!$id && $checkTitle && $postData) {
                $postTitle = Utils::array_get($postData, "post_title");
                $postType = Utils::array_get($postData, "post_type");

                $query = "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = %s AND ID <> %d";
                $id = $wpdb->get_var($wpdb->prepare($query, $postTitle, $postType, $currentPostId));
            }
        }

        // If this is the last page, check the content
        if(!$id && $isLastPage && $checkContent && $postData) {
            $postContent = Utils::array_get($postData, "post_content");
            $postType = Utils::array_get($postData, "post_type");

            $query = "SELECT ID FROM {$wpdb->posts} WHERE post_content = %s AND post_type = %s AND ID <> %d";
            $id = $wpdb->get_var($wpdb->prepare($query, $postContent, $postType, $currentPostId));
        }

        // If a duplicate post is found, add an error.
        if($id) {
            $this->addError(ErrorType::DUPLICATE_POST, $id);
            Informer::add(Information::fromInformationMessage(
                InformationMessage::DUPLICATE_POST,
                _wpcc("Post ID") . ": {$id}",
                InformationType::ERROR
            )->addAsLog());
        }

        return $id ? $id : false;
    }

    /**
     * Get CRON post meta key factory that is used to retrieve the correct post meta key for the current crawling type.
     * For example, if a recrawling is being done currently, a factory providing keys for recrawling CRON job is
     * provided. If this is regular crawling, a factory providing keys for regular crawling is provided.
     *
     * @return AbstractCronKeyFactory
     */
    private function getCronKeyFactory(): AbstractCronKeyFactory {
        return $this->isRecrawl 
            ? PostRecrawlCronKeyFactory::getInstance() 
            : PostCrawlCronKeyFactory::getInstance();
    }

    /**
     * @param bool $isRecrawl See {@link isRecrawl}
     */
    public function setIsRecrawl($isRecrawl): void {
        $this->isRecrawl = $isRecrawl;
    }

    /**
     * Assigns the given post ID to {@link postId} field
     *
     * @param int|null $postId
     * @since 1.11.0
     */
    private function setPostId(?int $postId): void {
        $this->postId = $postId;

        if ($this->saverData) {
            $this->saverData->setPostId($this->postId);
        }
    }

    /**
     * Assigns the given data to {@link data} field
     *
     * @param PostData|null $data
     * @since 1.11.0
     */
    private function setData(?PostData $data): void {
        $this->data = $data;

        if ($this->saverData) {
            $this->saverData->setPostData($this->data);
        }
    }

    /**
     * Initializes the value of {@link postUrl}
     *
     * @since 1.11.0
     */
    private function initPostUrl(): void {
        $mainSiteUrl   = $this->getSetting(SettingKey::MAIN_PAGE_URL);
        $this->postUrl = Utils::prepareUrl($mainSiteUrl, $this->urlToCrawl);
    }

    /**
     * Initializes the value of {@link bot}
     *
     * @param array $settings
     * @since 1.11.0
     */
    private function initBot(array $settings): void {
        $this->bot = $this->createBot($settings, $this->siteIdToCheck);
    }

    /**
     * @param array    $settings The settings of the site the bot is for
     * @param int|null $siteId   ID of the site
     * @return PostBot
     * @since 1.12.0
     */
    protected function createBot(array $settings, ?int $siteId): PostBot {
        return new PostBot($settings, $siteId);
    }

    /**
     * Initializes the value of {@link saverData}
     *
     * @since 1.11.0
     */
    private function initSaverData(): void {
        $this->saverData = new PostSaverData(
            $this->bot,
            $this->isRecrawl,
            $this->isFirstPage,
            $this->urlTuple
        );
    }

    /*
     * STATIC METHODS
     */

    /**
     * Get duplicate check types prepared to be shown in a select element.
     *
     * @param array $settings Post settings
     * @return array Returns an array with "values" and "defaults" keys, both of which has an array value. The
     *               key-description pairs are stored under "values" key. "defaults" stores key-defaultValue pairs.
     */
    public static function getDuplicateCheckOptionsForSelect($settings) {
        $result = [
            "values" => [
                PostSaver::DUPLICATE_CHECK_URL     => _wpcc("URL"),
                PostSaver::DUPLICATE_CHECK_TITLE   => _wpcc("Title"),
                PostSaver::DUPLICATE_CHECK_CONTENT => _wpcc("Content"),
            ],
            "defaults" => [
                PostSaver::DUPLICATE_CHECK_URL     => 1,
                PostSaver::DUPLICATE_CHECK_TITLE   => 1,
                PostSaver::DUPLICATE_CHECK_CONTENT => 0,
            ]
        ];

        // Get the duplicate check options from the post details
        $postSettings = new SettingsImpl($settings, Factory::postService()->getSingleMetaKeys());
        $options = PostDetailsService::getInstance()->getDuplicateOptions($postSettings);
        if ($options) {
            $result["values"] = array_merge($result["values"], $options["values"]);
            $result["defaults"] = array_merge($result["defaults"], $options["defaults"]);
        }

        return $result;
    }

    /*
     * GETTERS
     */

    /**
     * Get the next page URL that is found in {@link savePost()} method.
     *
     * @return null|string
     */
    public function getNextPageUrl() {
        return $this->nextPageUrl;
    }

    /**
     * Get the next page URLs that are found in {@link savePost()} method. This returns a non-null value only if the post
     * has all page URLs in a single page.
     *
     * @return array|null
     */
    public function getNextPageUrls() {
        return $this->nextPageUrls;
    }

}
