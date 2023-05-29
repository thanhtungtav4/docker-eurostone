<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/08/16
 * Time: 11:33
 */

namespace WPCCrawler\Objects\Crawling\Data;


use DateTime;
use WPCCrawler\Environment;
use WPCCrawler\Objects\Crawling\Data\Meta\PostMeta;
use WPCCrawler\Objects\Crawling\Data\Taxonomy\TaxonomyItem;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Events\Enums\EventGroupKey;
use WPCCrawler\Objects\File\MediaFile;
use WPCCrawler\Objects\Filtering\Objects\FieldConfig;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;
use WPCCrawler\Objects\Transformation\Objects\Special\DateTimeTransformableField;
use WPCCrawler\Objects\Transformation\Objects\Special\SpecialTransformableField;
use WPCCrawler\Objects\Transformation\Objects\TransformableField;
use WPCCrawler\Objects\Transformation\Objects\TransformableFieldList;
use WPCCrawler\Objects\Value\ValueExtractorOptions;
use WPCCrawler\Utils;

class PostData implements Transformable {

    const FIELD_TITLE                   = 'title';
    const FIELD_EXCERPT                 = 'excerpt.data';
    const FIELD_CATEGORY_NAMES          = 'categoryNames';
    const FIELD_NEXT_PAGE_URL           = 'nextPageUrl';
    const FIELD_ALL_PAGE_URLS           = 'allPageUrls.data';
    const FIELD_SLUG                    = 'slug';
    const FIELD_TEMPLATE                = 'template';
    const FIELD_SHORT_CODE_DATA         = 'shortCodeData.data';
    const FIELD_PREPARED_TAGS           = 'preparedTags';
    const FIELD_META_KEYWORDS           = 'metaKeywords';
    const FIELD_META_DESCRIPTION        = 'metaDescription';
    const FIELD_CUSTOM_META             = 'customMeta.data';
    const FIELD_ATTACHMENT              = 'attachmentData';
    const FIELD_ATTACHMENT_TITLE        = 'attachmentData.mediaTitle';
    const FIELD_ATTACHMENT_DESCRIPTION  = 'attachmentData.mediaDescription';
    const FIELD_ATTACHMENT_CAPTION      = 'attachmentData.mediaCaption';
    const FIELD_ATTACHMENT_ALT          = 'attachmentData.mediaAlt';
    const FIELD_THUMBNAIL               = 'thumbnailData';
    const FIELD_THUMBNAIL_TITLE         = 'thumbnailData.mediaTitle';
    const FIELD_THUMBNAIL_DESCRIPTION   = 'thumbnailData.mediaDescription';
    const FIELD_THUMBNAIL_CAPTION       = 'thumbnailData.mediaCaption';
    const FIELD_THUMBNAIL_ALT           = 'thumbnailData.mediaAlt';
    const FIELD_CUSTOM_TAXONOMIES       = 'customTaxonomies.data';
    const FIELD_DATE_CREATED            = 'dateCreated';

    /**
     * @var null|array An array of names of the post categories. Each item is a string or array. If the item is a
     *                 string, then it is one of the main categories of the post. If it is an array, it represents
     *                 a category hierarchy. Each previous category name in the array is the parent category name of the
     *                 item. E.g. ['cat1', 'cat2', 'cat3'] represents 'cat1 > cat2 > cat3' hierarchy.
     */
    private $categoryNames;

    /** @var bool */
    private $paginate = false;

    /** @var string|null */
    private $nextPageUrl;

    /** @var array|null */
    private $allPageUrls;

    /*
     *
     */

    /** @var string|null */
    private $title;

    /** @var array|null */
    private $excerpt;

    /** @var array|null */
    private $contents;

    /** @var DateTime|null */
    private $dateCreated = null;

    /** @var array|null */
    private $shortCodeData;

    /** @var string[]|null */
    private $tags;

    /** @var string[]|null */
    private $preparedTags;

    /** @var string|null */
    private $slug;

    /** @var int|null */
    private $authorId;

    /** @var int|null */
    private $featuredImageId = null;

    /** @var string|null */
    private $postStatus;

    /*
     * LIST
     */

    /** @var int|null */
    private $listStartPos;

    /** @var array|null */
    private $listNumbers;

    /** @var array|null */
    private $listTitles;

    /** @var array|null */
    private $listContents;

    /*
     * META
     */

    /** @var string|null */
    private $metaKeywords;

    /** @var array|null */
    private $metaKeywordsAsTags;

    /** @var string|null */
    private $metaDescription;

    /*
     *
     */

    /** @var null|MediaFile */
    private $thumbnailData;

    /** @var MediaFile[] */
    private $attachmentData = [];

    /*
     *
     */

    /** @var PostMeta[]|null */
    private $customMeta;

    /** @var TaxonomyItem[]|null */
    private $customTaxonomies;

    /** @var string|null */
    private $template;

    /*
     *
     */

    /** @var array WordPress post data */
    private $wpPostData = [];

    /*
     *
     */

    /** @var TransformableFieldList|null */
    private $transformableFields = null;

    /** @var TransformableFieldList|null */
    private $interactableFields = null;

    /** @var TransformableFieldList|null */
    private $conditionCommandFields = null;

    /** @var TransformableFieldList|null */
    private $actionCommandFields = null;

    /*
     * GETTERS AND SETTERS
     */

    /**
     * @return array|null See {@link $categoryNames}
     */
    public function getCategoryNames(): ?array {
        return $this->categoryNames;
    }

    /**
     * @param array|null $categoryNames See {@link $categoryNames}
     */
    public function setCategoryNames(?array $categoryNames): void {
        $this->categoryNames = $categoryNames;
    }

    /**
     * @return boolean
     */
    public function isPaginate(): bool {
        return $this->paginate;
    }

    /**
     * @param boolean $paginate
     */
    public function setPaginate($paginate): void {
        $this->paginate = $paginate;
    }

    /**
     * @return string|null
     */
    public function getNextPageUrl(): ?string {
        return $this->nextPageUrl;
    }

    /**
     * @param string|null $nextPageUrl
     */
    public function setNextPageUrl(?string $nextPageUrl): void {
        $this->nextPageUrl = $nextPageUrl;
    }

    /**
     * @return array|null
     */
    public function getAllPageUrls(): ?array {
        return $this->allPageUrls;
    }

    /**
     * @param array|null $allPageUrls
     */
    public function setAllPageUrls(?array $allPageUrls): void {
        $this->allPageUrls = $allPageUrls;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void {
        $this->title = $title;
    }

    /**
     * @return array|null
     */
    public function getExcerpt(): ?array {
        return $this->excerpt;
    }

    /**
     * @param array|null $excerpt See {@link excerpt}
     */
    public function setExcerpt(?array $excerpt): void {
        $this->excerpt = $excerpt;
    }

    /**
     * @return array|null
     */
    public function getContents(): ?array {
        return $this->contents;
    }

    /**
     * @param array|null $contents
     */
    public function setContents(?array $contents): void {
        $this->contents = $contents;
    }

    /**
     * @return DateTime
     * @since 1.11.1 $asString argument is removed. Use {@link getDateCreatedString()} instead. This returns only
     *               {@link DateTime}.
     */
    public function getDateCreated(): DateTime {
        // The created date will not consider GMT offset defined in WP settings. So, the date must have been already
        // set. Otherwise, we probably create an inaccurate date here.
        if ($this->dateCreated === null) $this->dateCreated = new DateTime();
        return $this->dateCreated;
    }

    /**
     * @return string The date (see {@link getDateCreated()}) as a string in the MySQL date format
     * @since 1.11.1
     */
    public function getDateCreatedString(): string {
        return $this->getDateCreated()->format(Environment::mysqlDateFormat());
    }

    /**
     * @param DateTime|null $dateCreated The post creation date
     */
    public function setDateCreated(?DateTime $dateCreated): void {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return array|null
     */
    public function getShortCodeData(): ?array {
        return $this->shortCodeData;
    }

    /**
     * @param array|null $shortCodeData
     */
    public function setShortCodeData(?array $shortCodeData): void {
        $this->shortCodeData = $shortCodeData;
    }

    /**
     * @return string[]|null
     */
    public function getTags(): ?array {
        return $this->tags;
    }

    /**
     * @param string[]|null $tags
     */
    public function setTags(?array $tags): void {
        $this->tags = $tags;
    }

    /**
     * @return string[]|null
     */
    public function getPreparedTags(): ?array {
        return $this->preparedTags;
    }

    /**
     * @param string[]|null $preparedTags
     */
    public function setPreparedTags(?array $preparedTags): void {
        $this->preparedTags = $preparedTags;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     */
    public function setSlug(?string $slug): void {
        $this->slug = $slug;
    }

    /**
     * @return int|null
     * @since 1.11.0
     */
    public function getAuthorId(): ?int {
        return $this->authorId;
    }

    /**
     * @param int|null $authorId
     * @since 1.11.0
     */
    public function setAuthorId(?int $authorId): void {
        $this->authorId = $authorId;
    }

    /**
     * @return int|null
     * @since 1.12.0
     */
    public function getFeaturedImageId(): ?int {
        return $this->featuredImageId;
    }

    /**
     * @param int|null $featuredImageId
     * @since 1.12.0
     */
    public function setFeaturedImageId(?int $featuredImageId): void {
        $this->featuredImageId = $featuredImageId;
    }

    /**
     * @return string|null
     * @since 1.11.0
     */
    public function getPostStatus(): ?string {
        return $this->postStatus;
    }

    /**
     * @param string|null $postStatus
     * @since 1.11.0
     */
    public function setPostStatus(?string $postStatus): void {
        $this->postStatus = $postStatus;
    }

    /**
     * @return int|null
     */
    public function getListStartPos(): ?int {
        return $this->listStartPos;
    }

    /**
     * @param int|null $listStartPos
     */
    public function setListStartPos(?int $listStartPos): void {
        $this->listStartPos = $listStartPos;
    }

    /**
     * @return array|null
     */
    public function getListNumbers(): ?array {
        return $this->listNumbers;
    }

    /**
     * @param array|null $listNumbers
     */
    public function setListNumbers(?array $listNumbers): void {
        $this->listNumbers = $listNumbers;
    }

    /**
     * @return array|null
     */
    public function getListTitles(): ?array {
        return $this->listTitles;
    }

    /**
     * @param array|null $listTitles
     */
    public function setListTitles(?array $listTitles): void {
        $this->listTitles = $listTitles;
    }

    /**
     * @return array|null
     */
    public function getListContents(): ?array {
        return $this->listContents;
    }

    /**
     * @param array|null $listContents
     */
    public function setListContents(?array $listContents): void {
        $this->listContents = $listContents;
    }

    /**
     * @return string|null
     */
    public function getMetaKeywords(): ?string {
        return $this->metaKeywords;
    }

    /**
     * @param string|null $metaKeywords
     */
    public function setMetaKeywords(?string $metaKeywords): void {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return array|null
     */
    public function getMetaKeywordsAsTags(): ?array {
        return $this->metaKeywordsAsTags;
    }

    /**
     * @param array|null $metaKeywordsAsTags
     */
    public function setMetaKeywordsAsTags(?array $metaKeywordsAsTags): void {
        $this->metaKeywordsAsTags = $metaKeywordsAsTags;
    }

    /**
     * @return string|null
     */
    public function getMetaDescription(): ?string {
        return $this->metaDescription;
    }

    /**
     * @param string|null $metaDescription
     */
    public function setMetaDescription(?string $metaDescription): void {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return MediaFile|null
     */
    public function getThumbnailData(): ?MediaFile {
        return $this->thumbnailData;
    }

    /**
     * @param MediaFile|null $mediaFile
     */
    public function setThumbnailData(?MediaFile $mediaFile): void {
        $this->thumbnailData = $mediaFile;
    }

    /**
     * @return MediaFile[]
     */
    public function getAttachmentData(): array {
        return $this->attachmentData;
    }

    /**
     * @param MediaFile[]|null $attachmentData
     */
    public function setAttachmentData(?array $attachmentData): void {
        $this->attachmentData = $attachmentData ?: [];
    }

    /**
     * Deletes previously saved attachments.
     */
    public function deleteAttachments(): void {
        if(!$this->getAttachmentData()) return;

        foreach($this->getAttachmentData() as $mediaFile) {
            Utils::deleteFile($mediaFile->getLocalPath());

            // If the media file has an ID, delete the attachment with that ID.
            $mediaId = $mediaFile->getMediaId();
            if ($mediaId) {
                wp_delete_attachment($mediaId, true);
            }
        }
    }

    /**
     * @return PostMeta[]|null
     */
    public function getCustomMeta(): ?array {
        return $this->customMeta;
    }

    /**
     * @param PostMeta[]|null $customMeta See {@link customMeta}
     */
    public function setCustomMeta(?array $customMeta): void {
        $this->customMeta = $customMeta;
    }

    /**
     * @return TaxonomyItem[]|null
     */
    public function getCustomTaxonomies(): ?array {
        return $this->customTaxonomies;
    }

    /**
     * @param TaxonomyItem[]|null $customTaxonomies
     */
    public function setCustomTaxonomies(?array $customTaxonomies): void {
        $this->customTaxonomies = $customTaxonomies;
    }

    /**
     * @return string|null
     */
    public function getTemplate(): ?string {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template): void {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getWpPostData(): array {
        return $this->wpPostData;
    }

    /**
     * @param array $wpPostData
     */
    public function setWpPostData(array $wpPostData): void {
        $this->wpPostData = $wpPostData;
    }

    /**
     * Get all media files, which contain attachment media files and the thumbnail media file.
     *
     * @return MediaFile[]
     * @since 1.8.0
     */
    public function getAllMediaFiles(): array {
        $mediaFiles = $this->getAttachmentData();

        $thumbnailFile = $this->getThumbnailData();
        if ($thumbnailFile) $mediaFiles[] = $thumbnailFile;

        return $mediaFiles;
    }

    public function getTransformableFields(): TransformableFieldList {
        if ($this->transformableFields === null) {
            $this->transformableFields = new TransformableFieldList([
                new TransformableField(static::FIELD_TITLE,                  _wpcc('Title'),                    ValueType::T_STRING),
                new TransformableField(static::FIELD_EXCERPT,                _wpcc('Excerpt'),                  ValueType::T_STRING),
                new TransformableField(static::FIELD_CATEGORY_NAMES,         _wpcc('Category Names'),           [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                new TransformableField(static::FIELD_SLUG,                   _wpcc('Slug'),                     ValueType::T_STRING),
                new TransformableField(static::FIELD_TEMPLATE,               _wpcc('Content'),                  ValueType::T_STRING),
                new TransformableField(static::FIELD_PREPARED_TAGS,          _wpcc('Tags'),                     [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                new TransformableField(static::FIELD_META_KEYWORDS,          _wpcc('Meta Keywords'),            ValueType::T_STRING),
                new TransformableField(static::FIELD_META_DESCRIPTION,       _wpcc('Meta Description'),         ValueType::T_STRING),
                new TransformableField(static::FIELD_CUSTOM_META,            _wpcc('Custom Meta'),              [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                new TransformableField(static::FIELD_ATTACHMENT_TITLE,       _wpcc('Media Title'),              [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                new TransformableField(static::FIELD_ATTACHMENT_DESCRIPTION, _wpcc('Media Description'),        [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                new TransformableField(static::FIELD_ATTACHMENT_CAPTION,     _wpcc('Media Caption'),            [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                new TransformableField(static::FIELD_ATTACHMENT_ALT,         _wpcc('Media Alternate Text'),     [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                new TransformableField(static::FIELD_THUMBNAIL_TITLE,        _wpcc('Thumbnail Title'),          ValueType::T_STRING),
                new TransformableField(static::FIELD_THUMBNAIL_DESCRIPTION,  _wpcc('Thumbnail Description'),    ValueType::T_STRING),
                new TransformableField(static::FIELD_THUMBNAIL_CAPTION,      _wpcc('Thumbnail Caption'),        ValueType::T_STRING),
                new TransformableField(static::FIELD_THUMBNAIL_ALT,          _wpcc('Thumbnail Alternate Text'), ValueType::T_STRING),
                new TransformableField(static::FIELD_CUSTOM_TAXONOMIES,      _wpcc('Taxonomies'),               [ValueType::T_STRING, ValueType::T_COUNTABLE]),
            ], new FieldConfig(EventGroupKey::POST_DATA));
        }

        return $this->transformableFields;
    }

    public function getInteractableFields(): TransformableFieldList {
        if ($this->interactableFields === null) {
            $this->interactableFields = (new TransformableFieldList(null, new FieldConfig(EventGroupKey::POST_DATA)))
                ->addAllFromList($this->getTransformableFields())
                ->addAll([
                    new TransformableField(static::FIELD_NEXT_PAGE_URL,   _wpcc('Next Page URL'),      ValueType::T_STRING),
                    new TransformableField(static::FIELD_ALL_PAGE_URLS,   _wpcc('All Page URLs'),      [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                    new TransformableField(static::FIELD_SHORT_CODE_DATA, _wpcc('Custom Short Codes'), [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                ]);
        }

        return $this->interactableFields;
    }

    public function getConditionCommandFields(): ?TransformableFieldList {
        if ($this->conditionCommandFields === null) {
            $this->conditionCommandFields = (new TransformableFieldList(null, new FieldConfig(EventGroupKey::POST_DATA)))
                ->addAll([
                    // TODO: This is actually not useful. Either provide a full suit of conditions and actions for both
                    //  MediaFile class and MediaFile array or remove this.
                    (new SpecialTransformableField(static::FIELD_ATTACHMENT, _wpcc('Media Files'), ValueType::T_COUNTABLE))
                        // Modify the extractor options. We do not want to allow everything. We want to allow objects
                        // only. If everything is allowed, subject items contain only 1 key that has an array value. We
                        // want as many keys as there are media files. In other words, the resultant array must contain
                        // all media files as its direct values.
                        ->setModifyExtractorOptionsCallback(function(ValueExtractorOptions $options) {
                            $options
                                ->setAllowAll(false)
                                ->setAllowObjects(true);
                        }),

                    // TODO: Add publish date to action command fields as well and add action commands to let the user
                    //  modify the date conditionally.
                    (new DateTimeTransformableField(static::FIELD_DATE_CREATED, _wpcc('Publish Date'), ValueType::T_DATE))
                ]);
        }

        return $this->conditionCommandFields;
    }

    public function getActionCommandFields(): ?TransformableFieldList {
        return $this->actionCommandFields;
    }

}