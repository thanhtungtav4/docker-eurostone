<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/10/2018
 * Time: 10:43
 */

namespace WPCCrawler\Test\General;


use Exception;
use Illuminate\Contracts\View\View;
use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Crawling\Data\PostData;
use WPCCrawler\Objects\Crawling\Data\PostSaverData;
use WPCCrawler\PostDetail\PostDetailsService;
use WPCCrawler\Test\Base\AbstractGeneralTest;
use WPCCrawler\Test\Data\GeneralTestData;
use WPCCrawler\Test\Enums\TestType;
use WPCCrawler\Utils;

class GeneralPostTest extends AbstractGeneralTest {

    /** @var PostBot|null */
    private $postBot;

    /** @var string|null */
    private $template;

    /** @var PostData|null */
    private $postData;

    /**
     * Conduct the test and return an array of results.
     *
     * @param GeneralTestData $data
     * @throws Exception
     */
    protected function createResults(GeneralTestData $data): void {
        $postData = new PostData();

        // PREPARE THE URL AND GET THE POST
        $template = '';
        if (!empty($data->getTestUrl())) {
            $bot = new PostBot($data->getSettings(), $data->getSiteId());

            $preparedUrl = Utils::prepareUrl($bot->getSiteUrl(), $data->getTestUrl());
            $saverData = new PostSaverData($bot, false, true);
            if ($postData = $bot->crawlPost($preparedUrl, $saverData)) {
                $template = $postData->getTemplate();

                // If there are errors, add them to info.
                if ($errorDescriptions = $bot->getDescriptionsWithErrorValues()) {
                    $this->addInfo(_wpcc('Errors'), $errorDescriptions);
                }
            }

            $this->postBot = $bot;
        }

        $this->template = $template;
        $this->postData = $postData;

        $this->addPredefinedInfos();
    }

    /**
     * Create a view from the results found in {@link createResults} method.
     *
     * @return View|null
     */
    protected function createView() {
        $postData = $this->postData;
        
        $viewVars = [
            'template'          => $this->template,
            'info'              => $this->getInfo(),
            'data'              => (array) $postData,
            'showSourceCode'    => true,
            'templateMessage'   => _wpcc('Styling can be different on front page depending on your theme.')
        ];

        // Add views defined for the custom post details
        $postBot = $this->postBot;
        if ($postData && $postBot) {
            $viewVars['postDetailViews'] = PostDetailsService::getInstance()
                ->getTestViews($postBot, $postData, $viewVars);
        }

        return Utils::view('site-tester/test-results')->with($viewVars);
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Prepares and sets the test info using predefined infos
     */
    private function addPredefinedInfos(): void {
        $postData = $this->postData;
        if (!$postData) return;

        $this->addNextPageUrlInfo($postData);
        $this->setAllPagesInfo();
        $this->addInfo(_wpcc("Title"),      $postData->getTitle(),    true);

        $this->addInfo(_wpcc("Slug"),       $postData->getSlug(),     true);

        $this->addInfo(_wpcc('Categories'), $this->getCategoryNames(),      true);

        if ($date = $postData->getDateCreatedString()) {
            $this->addInfo(_wpcc("Date"), Utils::getDateFormatted($date) . " ($date)");
        }

        $this->addInfo(_wpcc("Meta Keywords"),          $postData->getMetaKeywords(),         true);
        $this->addInfo(_wpcc("Meta Keywords As Tags"),  $postData->getMetaKeywordsAsTags(),   true);
        $this->addInfo(_wpcc("Meta Description"),       $postData->getMetaDescription(),      true);

        $excerpt = $postData->getExcerpt();
        if ($excerpt && $excerptData = $excerpt["data"]) {
            $this->addInfo(_wpcc("Excerpt"), $excerptData);
        }

        $this->addInfo(_wpcc("Tags"), $postData->getTags(), true);
        $this->addInfo(_wpcc("Prepared Post Tags"), $postData->getPreparedTags(), true);

        $this->setCustomMetaInfo();
        $this->setCustomTaxonomyInfo();
        $this->setThumbnailInfo();
        $this->setAssignedFeaturedImageInfo();
        $this->setAttachmentInfo();
    }

    /**
     * Sets the custom meta info
     */
    private function setCustomMetaInfo(): void {
        $postData = $this->postData;
        if (!$postData) return;
        
        $customPostMeta = $postData->getCustomMeta();
        if (!$customPostMeta) return;

        $preparedCustomPostMeta = [];

        foreach ($customPostMeta as $item) {
            $preparedCustomPostMeta[] = Utils::view('site-tester.partial.custom-post-meta-item')->with([
                'item' => $item
            ])->render();
        }

        $this->addInfo(_wpcc("Custom Post Meta"), $preparedCustomPostMeta);
    }

    /**
     * Sets the custom taxonomy info
     */
    private function setCustomTaxonomyInfo(): void {
        $postData = $this->postData;
        if (!$postData) return;
        
        $customPostTaxonomies = $postData->getCustomTaxonomies();
        if (!$customPostTaxonomies) return;

        $preparedCustomPostTaxonomies = [];

        foreach ($customPostTaxonomies as $item) {
            $preparedCustomPostTaxonomies[] = Utils::view('site-tester.partial.custom-post-taxonomy-item')->with([
                'item' => $item
            ])->render();
        }

        $this->addInfo(_wpcc("Custom Post Taxonomies"), $preparedCustomPostTaxonomies);
    }

    /**
     * Sets the thumbnail info
     */
    private function setThumbnailInfo(): void {
        $postData = $this->postData;
        if (!$postData) return;
        
        // Show featured image link as a real link, and add a preview to be displayed in tooltip.
        if ($mediaFile = $postData->getThumbnailData())
            $this->addInfo(_wpcc("Featured Image"),
                Utils::view('site-tester.partial.attachment-item')->with([
                    'item'      => $mediaFile,
                    'tooltip'   => true
                ])->render()
            );
    }

    /**
     * Adds the details about the assigned featured image, if there is one.
     * @since 1.12.0
     */
    private function setAssignedFeaturedImageInfo(): void {
        $postData = $this->postData;
        if (!$postData) return;

        // If there is no a featured image ID, stop.
        $featuredImageId = $postData->getFeaturedImageId();
        if (!$featuredImageId) return;

        // Add an info to show the assigned featured image
        $imageUrl = wp_get_attachment_thumb_url($featuredImageId);
        $editUrl  = get_edit_post_link($featuredImageId, '');
        $this->addInfo(_wpcc('Assigned Featured Image'),
            Utils::view('site-tester.partial.media-item')->with([
                'url'     => $imageUrl,
                'editUrl' => $editUrl,
                'imageId' => $featuredImageId,
            ])->render()
        );
    }

    /**
     * Sets the attachment info
     */
    private function setAttachmentInfo(): void {
        $postData = $this->postData;
        if (!$postData) return;
        
        // Get the attachments
        $attachmentData = $postData->getAttachmentData();

        // Stop if there are none.
        if (!$attachmentData) return;

        // Show attachment links as real links. Add a preview tooltip if the attachment is an image.
        $attachmentData = array_map(function ($mediaFile) {
            $tooltip = (bool) preg_match('/\.(jpg|JPG|png|PNG|gif|GIF|jpeg|JPEG)/', $mediaFile->getLocalUrl());

            return Utils::view('site-tester.partial.attachment-item')->with([
                'item'      => $mediaFile,
                'tooltip'   => $tooltip
            ])->render();

        }, $attachmentData);

        $this->addInfo(_wpcc("Attachments"), $attachmentData);
    }

    /**
     * Sets "all pages" info
     */
    private function setAllPagesInfo(): void {
        $postData = $this->postData;
        if (!$postData) return;
        
        // Show all next page URLs as a list with test buttons, so that the user can just click the button to test the next page.
        if ($allPageUrls = $postData->getAllPageUrls()) {
            $this->addInfo(
                _wpcc("Next Page URLs"),
                Utils::view('site-tester/urls-with-test')->with([
                    'urls'           => $allPageUrls,
                    'testType'       => TestType::POST,
                    'hideThumbnails' => true
                ])->render()
            );
        }
    }

    /**
     * Prepares category names for presentation.
     *
     * @return array|null
     * @since 1.8.0
     */
    private function getCategoryNames(): ?array {
        $postData = $this->postData;
        if (!$postData) return null;
        
        $categoryNames = $postData->getCategoryNames();
        if (!$categoryNames) return null;

        return array_map(function($v) {
            if (!$v) return null;
            if (!is_array($v)) $v = [$v];

            $v = array_map(function($category) {
                return sprintf('<span class="category">%1$s</span>', $category);
            }, $v);

            return $v ? implode('<span class="category-separator">›</span>', $v) : null; // @phpstan-ignore-line
        }, $categoryNames);
    }

}