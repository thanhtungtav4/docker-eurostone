<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/08/16
 * Time: 15:04
 */

namespace WPCCrawler\Objects\Crawling\Data;


use WPCCrawler\Objects\Crawling\Data\Url\PostUrlList;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Events\Enums\EventGroupKey;
use WPCCrawler\Objects\Filtering\Objects\FieldConfig;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;
use WPCCrawler\Objects\Transformation\Objects\TransformableField;
use WPCCrawler\Objects\Transformation\Objects\TransformableFieldList;

class CategoryData implements Transformable {

    const FIELD_POST_URL            = 'postUrlList.items.url';
    const FIELD_POST_THUMBNAIL_URL  = 'postUrlList.items.thumbnailUrl';

    /** @var PostUrlList|null */
    private $postUrlList;

    /** @var array */
    private $thumbnails;

    /** @var string|null */
    private $nextPageUrl;

    /*
     *
     */

    /** @var TransformableFieldList|null */
    private $transformableFields = null;

    /** @var TransformableFieldList|null */
    private $interactableFields = null;

    /*
     * GETTERS AND SETTERS
     */

    /**
     * @return PostUrlList
     */
    public function getPostUrlList(): PostUrlList {
        if ($this->postUrlList === null) {
            $this->postUrlList = new PostUrlList();
        }

        return $this->postUrlList;
    }

    /**
     * @param PostUrlList|null $postUrlList
     */
    public function setPostUrlList(?PostUrlList $postUrlList): void {
        $this->postUrlList = $postUrlList ?: new PostUrlList();
    }

    /**
     * Reverse the order of {@link postUrlList}
     *
     * @since 1.11.0
     */
    public function reversePostUrls(): void {
        $this->getPostUrlList()->reverse();
    }

    /**
     * @return array See {@link thumbnails}
     */
    public function getThumbnails(): array {
        return $this->thumbnails ?: [];
    }

    /**
     * @param array $thumbnails See {@link thumbnails}
     */
    public function setThumbnails(array $thumbnails): void {
        $this->thumbnails = $thumbnails;
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

    /*
     *
     */

    public function getTransformableFields(): TransformableFieldList {
        if ($this->transformableFields === null) {
            $this->transformableFields = new TransformableFieldList();
        }

        return $this->transformableFields;
    }

    public function getInteractableFields(): TransformableFieldList {
        if ($this->interactableFields === null) {
            $this->interactableFields = (new TransformableFieldList(null, new FieldConfig(EventGroupKey::CATEGORY_DATA)))
                ->add(new TransformableField(static::FIELD_POST_URL,           _wpcc('Post URL'),           [ValueType::T_STRING, ValueType::T_COUNTABLE]))
                ->add(new TransformableField(static::FIELD_POST_THUMBNAIL_URL, _wpcc('Post Thumbnail URL'), [ValueType::T_STRING, ValueType::T_COUNTABLE]));
        }

        return $this->interactableFields;
    }

    public function getConditionCommandFields(): ?TransformableFieldList {
        return null;
    }

    public function getActionCommandFields(): ?TransformableFieldList {
        return null;
    }

}