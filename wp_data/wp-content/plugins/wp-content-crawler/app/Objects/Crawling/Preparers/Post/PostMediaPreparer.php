<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/11/2018
 * Time: 14:37
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post;


use Exception;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Data\PostData;
use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostBotPreparer;
use WPCCrawler\Objects\File\FileService;
use WPCCrawler\Objects\File\MediaFile;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\OptionsBox\OptionsBoxService;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Utils;

class PostMediaPreparer extends AbstractPostBotPreparer {

    /** @var PostData|null */
    private $postData;

    /** @var array|null */
    private $findAndReplacesForImageUrls;

    /** @var bool */
    private $postSaveImagesAsMedia = false;

    /** @var bool */
    private $postSaveAllImagesInContent = false;

    /** @var bool */
    private $postSaveImagesAsGallery;

    /** @var MediaFile[] */
    private $attachmentMediaFiles = [];

    /**
     * @var array Stores the URLs of the remote files that are saved to the local environment. Keys are file URLs,
     *            and the values are MediaFile instances for the file URLs.
     */
    private $savedUrlMediaFileMap = [];

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public function prepare() {
        // Initialize instance variables
        $this->postData = $this->bot->getPostData();

        $this->findAndReplacesForImageUrls  = $this->bot->getSetting(SettingKey::POST_FIND_REPLACE_IMAGE_URLS, []);
        $this->postSaveImagesAsMedia        = $this->bot->getSettingForCheckbox(SettingKey::POST_SAVE_IMAGES_AS_MEDIA);
        $this->postSaveAllImagesInContent   = $this->bot->getSettingForCheckbox(SettingKey::POST_SAVE_ALL_IMAGES_IN_CONTENT);
        $this->postSaveImagesAsGallery      = $this->postSaveImagesAsMedia && $this->bot->getSettingForCheckbox(SettingKey::POST_SAVE_IMAGES_AS_GALLERY);

        // If the user wants to save all images in the post content, set "save images as media" to true so that the
        // script can run properly.
        if($this->postSaveAllImagesInContent) {
            $this->postSaveImagesAsMedia = true;
        }

        // Save the thumbnail URL first, because the thumbnail may be removed by gallery image selectors later.
        $this->prepareAndSaveThumbnail();

        // Prepare the attachment data.
        $this->prepareAttachmentData();
    }

    /*
     *
     */

    /**
     * Prepares the thumbnail data and sets it to {@link $postData}
     */
    private function prepareAndSaveThumbnail(): void {
        $postSaveThumbnailIfNotExist    = $this->bot->getSetting(SettingKey::POST_SAVE_THUMBNAILS_IF_NOT_EXIST);
        $findAndReplacesForThumbnailUrl = $this->bot->getSetting(SettingKey::POST_FIND_REPLACE_THUMBNAIL_URL);
        $postThumbnailSelectors         = $this->bot->getSetting(SettingKey::POST_THUMBNAIL_SELECTORS);

        // If the user does not want to save a thumbnail, stop.
        if (!$postSaveThumbnailIfNotExist) return;

        foreach($postThumbnailSelectors as $selectorData) {
            $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
            if (!$selector) continue;

            $attr = Utils::array_get($selectorData, SettingInnerKey::ATTRIBUTE);
            if (!$attr) $attr = 'src';

            // Get the thumbnail URL
            $thumbnailData = $this->bot->extractData($this->bot->getCrawler(), $selector, [$attr, "alt", "title"], false, true, true);
            if (!$thumbnailData) continue;

            // Get the source URL
            // If the image data is an array
            if (is_array($thumbnailData)) {
                // It must have an index of the given $attr.
                if (!isset($thumbnailData[$attr])) {
                    // $attr index does not exist. Hence, we do not have an image URL. Continue with the next one.
                    continue;
                }

                $src = $thumbnailData[$attr];
            } else {
                $src = $thumbnailData;
            }

            // Apply the replacements
            $originalSrc = $src;
            $src = $this->bot->findAndReplace($findAndReplacesForThumbnailUrl, $src);

            // Set it as thumbnail URL to the post data
            try {
                $src = $this->bot->resolveUrl($src);
            } catch (Exception $e) {
                Informer::addError(_wpcc('URL could not be resolved') . ' - ' . $src)->addAsLog();
            }

            if (!$src) continue;

            // Create a media file
            $mediaFile = new MediaFile($src, null);
            $mediaFile->setOriginalSourceUrl($originalSrc);

            // Get "alt" and "title" values
            if (is_array($thumbnailData)) {
                $mediaFile
                    ->setMediaAlt(Utils::array_get($thumbnailData, 'alt'))
                    ->setMediaTitle(Utils::array_get($thumbnailData, 'title'));
            }

            // Save the featured image
            $success = FileService::getInstance()->saveMediaFile($mediaFile, $this->getBot());
            if (!$success) continue;

            // Get an applier for this selector data and, if it exists, apply the options.
            $applier = OptionsBoxService::getInstance()->createApplierFromSelectorData($selectorData);
            if ($applier) $applier->apply($mediaFile);

            // We have found a thumbnail. So, no need to look for another one. Stop.
            if ($this->postData) {
                $this->postData->setThumbnailData($mediaFile);
            }

            break;
        }
    }

    /**
     * Prepares the attachment data.
     *
     * @since 1.8.0
     */
    private function prepareAttachmentData(): void {
        $this->attachmentMediaFiles = [];

        // Prepare the gallery images. This should be called before prepareImageData. Otherwise, if there are duplicate
        // image URLs and prepareImageData finds them first, gallery images won't be saved. In other words, the images
        // that are marked as "gallery_image" will be skipped since their URLs were already saved. So, gallery image
        // data preparation is first.
        $this->prepareGalleryFileData();

        // Prepare the images
        $this->prepareFileData();

        // Set the attachment media files
        if ($this->postData) {
            $this->postData->setAttachmentData($this->attachmentMediaFiles);
        }

    }

    /**
     * Prepares gallery images
     */
    private function prepareGalleryFileData(): void {
        // If the images should not be saved, stop.
        if (!$this->postSaveImagesAsMedia || !$this->postSaveImagesAsGallery) return;

        $crawler = $this->bot->getCrawler();
        if (!$crawler) return;

        $postGalleryImageSelectors = $this->bot->getSetting(SettingKey::POST_GALLERY_IMAGE_SELECTORS, []);

        // Prepare the image data
        $this->attachmentMediaFiles = array_merge(
            $this->attachmentMediaFiles,
            $this->prepareFileDataWithSelectors($crawler, $postGalleryImageSelectors, true)
        );
    }

    /**
     * Prepares images whose CSS selectors are given in the settings
     */
    private function prepareFileData(): void {
        // If the images should not be saved, stop.
        if (!$this->postSaveImagesAsMedia) return;

        $postImageSelectors = $this->bot->getSetting(SettingKey::POST_IMAGE_SELECTORS);

        // If the user wants to save all images inside the post content, manually add "img" selector to the post image
        // selectors.
        if($this->postSaveAllImagesInContent) {
            if(!$postImageSelectors) $postImageSelectors = [];

            $postImageSelectors[] = [
                SettingInnerKey::SELECTOR  => "img",
                SettingInnerKey::ATTRIBUTE => "src"
            ];
        }

        // Get all content combined
        $allContent = $this->getAllContent();

        // If there is no content, we cannot find any images. So, stop.
        if(empty($allContent)) return;

        $combinedContent = "";
        foreach($allContent as $content) {
            $combinedContent .= $content["data"];
        }

        // Create a crawler for the combined content and search for URLs
        $sourceCrawler = $this->bot->createDummyCrawler($combinedContent);

        // Prepare the image data
        $this->attachmentMediaFiles = array_merge(
            $this->attachmentMediaFiles,
            $this->prepareFileDataWithSelectors($sourceCrawler, $postImageSelectors)
        );
    }

    /*
     *
     */

    /**
     * Prepares image data and adds them to {@link sourceData}
     *
     * @param Crawler $crawler        The crawler from which the data will be extracted
     * @param array   $imageSelectors An array of selectors. Each selector is an array that should contain 'selector',
     *                                and 'attr' keys whose values are strings. 'selector' is a CSS selector, and
     *                                'attr'
     *                                is the target attribute from which the content will be retrieved. Default 'attr'
     *                                is
     *                                'src'.
     * @param bool    $isForGallery   True if the found images are for gallery.
     * @param bool    $singleResult   True if only one result is enough.
     * @return MediaFile[] Found data as a MediaFile array
     * @since 1.8.0
     */
    private function prepareFileDataWithSelectors($crawler, $imageSelectors, $isForGallery = false,
                                                  $singleResult = false): array {
        // TODO: This method have a lot in common with FileService::saveFilesWithSelectors(). Find a way to keep
        //  the code DRY.
        $mediaFiles = [];

        // Prepare the image data
        foreach($imageSelectors as $selectorData) {
            $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
            if (!$selector) continue;

            $attr = Utils::array_get($selectorData, SettingInnerKey::ATTRIBUTE);
            if (!$attr) $attr = 'src';

            // Get image data
            $fileData = $this->bot->extractData($crawler, $selector, [$attr, "alt", "title"], false, $singleResult, true);
            if (!$fileData) continue;

            if ($isForGallery) {
                // Remove these elements from the source code of the page
                $this->bot->removeElementsFromCrawler($crawler, $selector);
            }

            // If the image data is not an array, make it an array.
            if (!is_array($fileData)) $fileData = [$fileData];

            // Try to get an options box applier for this selector data
            $applier = OptionsBoxService::getInstance()->createApplierFromSelectorData($selectorData);

            // Make replacements
            foreach ($fileData as $key => $mFileData) {
                // Get the source URL
                // If the image data is an array
                if (is_array($mFileData)) {
                    // It must have an index of the given $attr.
                    if (!isset($mFileData[$attr])) {
                        // $attr index does not exist. Hence, we do not have an image URL. Continue with the next one.
                        continue;
                    }

                    $src = $mFileData[$attr];
                } else {
                    $src = $mFileData;
                }

                // Store the original source URL
                $original = $src;

                // Make the replacements for the image URL
                if ($src && $this->findAndReplacesForImageUrls) {
                    $src = $this->bot->findAndReplace($this->findAndReplacesForImageUrls, $src);
                }

                // If there is no URL, continue with the next one.
                if (!$src) continue;

                // Prepare the media URL
                try {
                    $src = $this->bot->resolveUrl($src);

                } catch (Exception $e) {
                    Informer::addError(_wpcc('URL could not be resolved.') . ' - ' . $src)->addAsLog();
                }

                // Create a media file for this file
                $mediaFile = (new MediaFile($src, null))
                    ->setOriginalSourceUrl($original)
                    ->setIsGalleryImage($isForGallery);

                // Get "alt" and "title" values
                if (is_array($mFileData)) {
                    $mediaFile
                        ->setMediaAlt(Utils::array_get($mFileData, 'alt'))
                        ->setMediaTitle(Utils::array_get($mFileData, 'title'));
                }

                // If this is a duplicate, continue with the next one.
                if(isset($this->savedUrlMediaFileMap[$mediaFile->getSourceUrl()])) {
                    continue;
                }

                // Cache this so that we can check for duplicate source URLs. By this way, we eliminate redundant file
                // save operations.
                $this->savedUrlMediaFileMap[$mediaFile->getSourceUrl()] = $mediaFile;

                // Save the media file
                $success = FileService::getInstance()->saveMediaFile($mediaFile, $this->getBot());
                if (!$success) continue;

                // Apply file options box options
                if ($applier) $applier->apply($mediaFile);

                // Add it among others
                $mediaFiles[] = $mediaFile;

                // Stop if there should only be a single result.
                if ($singleResult) break;
            }

        }

        return $mediaFiles;
    }

    /**
     * Get an array containing contents, list contents, and short code contents
     *
     * @return array
     */
    private function getAllContent(): array {
        $allContent = [];

        // Get all of the contents
        $postData = $this->postData;
        if (!$postData) return [];

        if($postData->getContents())      $allContent = array_merge($allContent, $postData->getContents() ?: []);
        if($postData->getListContents())  $allContent = array_merge($allContent, $postData->getListContents() ?: []);
        if($postData->getShortCodeData()) $allContent = array_merge($allContent, $postData->getShortCodeData() ?: []);

        return $allContent;
    }
}
