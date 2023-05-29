<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/11/2018
 * Time: 09:05
 */

namespace WPCCrawler\Objects\Crawling\Data;


use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Database\UrlTuple;

class PostSaverData {

    /** @var PostBot The post bot that is used to crawl the post. */
    private $postBot;

    /** @var int|null ID of the target WordPress post */
    private $postId;

    /** @var boolean True if this a recrawl operation. Otherwise, false. */
    private $isRecrawl;

    /** @var boolean True if saving should be done for the first page of the post. */
    private $isFirstPage;

    /** @var UrlTuple|null URL tuple that is used to save the current post */
    private $urlTuple;

    /** @var PostData|null PostData instance created by the post saver. */
    private $postData;

    /** @var int[] Attachment IDs that should be set as product gallery images */
    private $galleryAttachmentIds;

    /**
     * @param PostBot       $postBot              See {@link $postBot}
     * @param bool          $isRecrawl            See {@link $isRecrawl}
     * @param bool          $isFirstPage          See {@link $isFirstPage}
     * @param UrlTuple|null $urlTuple             See {@link $urlTuple}
     * @param int|null      $postId               See {@link $postId}
     * @param PostData|null $postData             See {@link $postData}
     * @param int[]         $galleryAttachmentIds See {@link $galleryAttachmentIds}
     *
     * @since 1.11.0 - $urlTuple is now a UrlTuple instance. Previously, it was just a regular object. Also, it is now
     *                 nullable. It has a default value of null.
     *               - $postId parameter  is moved after $urlTuple. It is made nullable. It has a default value of null.
     *               - PostData parameter is moved after $postId.   It is made nullable. It has a default value of null.
     *               - PostSaver parameter is removed. PostBot parameter is added instead.
     */
    public function __construct(PostBot $postBot, bool $isRecrawl, bool $isFirstPage, ?UrlTuple $urlTuple = null,
                                ?int $postId = null, ?PostData $postData = null, array $galleryAttachmentIds = []) {
        $this->postBot              = $postBot;
        $this->postId               = $postId;
        $this->isRecrawl            = $isRecrawl;
        $this->isFirstPage          = $isFirstPage;
        $this->urlTuple             = $urlTuple;
        $this->postData             = $postData;
        $this->galleryAttachmentIds = $galleryAttachmentIds;
    }

    /**
     * @return PostBot See {@link postBot}
     * @since 1.11.0
     */
    public function getPostBot(): PostBot {
        return $this->postBot;
    }

    /**
     * @return int|null See {@link postId}
     */
    public function getPostId(): ?int {
        return $this->postId;
    }

    /**
     * @param int|null $postId See {@link postId}
     * @return PostSaverData
     * @since 1.11.0
     */
    public function setPostId(?int $postId): self {
        $this->postId = $postId;
        return $this;
    }

    /**
     * @return PostData See {@link postData}
     */
    public function getPostData(): PostData {
        return $this->postData ?: new PostData();
    }

    /**
     * @param PostData|null $postData See {@link postData}
     * @return PostSaverData
     * @since 1.11.0
     */
    public function setPostData(?PostData $postData): self {
        $this->postData = $postData;
        return $this;
    }

    /**
     * @return bool See {@link isRecrawl}
     */
    public function isRecrawl(): bool {
        return $this->isRecrawl;
    }

    /**
     * @return bool See {@link isFirstPage}
     */
    public function isFirstPage(): bool {
        return $this->isFirstPage;
    }

    /**
     * @return UrlTuple|null See {@link urlTuple}
     */
    public function getUrlTuple(): ?UrlTuple {
        return $this->urlTuple;
    }

    /**
     * @return string|null URL of the post
     */
    public function getUrl(): ?string {
        return $this->urlTuple ? $this->urlTuple->getUrl() : null;
    }

    /**
     * @return array See {@link PostData::getWpPostData()}
     */
    public function getWpPostData(): array {
        return $this->getPostData()->getWpPostData();
    }

    /**
     * @return array See {@link galleryAttachmentIds}
     */
    public function getGalleryAttachmentIds(): array {
        return $this->galleryAttachmentIds ?: [];
    }

    /**
     * @param int[] $galleryAttachmentIds See {@link galleryAttachmentIds}
     * @return PostSaverData
     * @since 1.11.0
     */
    public function setGalleryAttachmentIds(array $galleryAttachmentIds): self {
        $this->galleryAttachmentIds = $galleryAttachmentIds;
        return $this;
    }
}