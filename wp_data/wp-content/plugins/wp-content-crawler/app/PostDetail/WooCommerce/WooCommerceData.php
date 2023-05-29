<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 16/11/2018
 * Time: 18:57
 */

namespace WPCCrawler\PostDetail\WooCommerce;


use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\File\MediaFile;
use WPCCrawler\Objects\Events\Enums\EventGroupKey;
use WPCCrawler\Objects\Filtering\Objects\FieldConfig;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;
use WPCCrawler\Objects\Transformation\Objects\TransformableField;
use WPCCrawler\Objects\Transformation\Objects\TransformableFieldList;
use WPCCrawler\PostDetail\Base\BasePostDetailData;
use WPCCrawler\PostDetail\WooCommerce\Data\ProductAttribute;

class WooCommerceData extends BasePostDetailData implements Transformable {

    // TODO: Save reviews

    const FIELD_PRODUCT_URL                 = 'productUrl';
    const FIELD_BUTTON_TEXT                 = 'buttonText';
    const FIELD_REGULAR_PRICE               = 'regularPrice';
    const FIELD_SALE_PRICE                  = 'salePrice';
    const FIELD_DOWNLOADABLE_TITLE          = 'downloadableMediaFiles.mediaTitle';
    const FIELD_DOWNLOADABLE_DESCRIPTION    = 'downloadableMediaFiles.mediaDescription';
    const FIELD_DOWNLOADABLE_CAPTION        = 'downloadableMediaFiles.mediaCaption';
    const FIELD_DOWNLOADABLE_ALT            = 'downloadableMediaFiles.mediaAlt';
    const FIELD_SKU                         = 'sku';
    const FIELD_STOCK_QUANTITY              = 'stockQuantity';
    const FIELD_WEIGHT                      = 'weight';
    const FIELD_LENGTH                      = 'length';
    const FIELD_WIDTH                       = 'width';
    const FIELD_HEIGHT                      = 'height';
    const FIELD_PURCHASE_NOTE               = 'purchaseNote';
    const FIELD_ATTRIBUTE_KEY               = 'attributes.key';
    const FIELD_ATTRIBUTE_KEY_RELAXED       = 'attributes.keyRelaxed';
    const FIELD_ATTRIBUTE_VALUE             = 'attributes.values';

    /** @var string|null */
    private $productType;

    /** @var bool */
    private $isVirtual = false;

    /** @var bool */
    private $isDownloadable = false;

    /** @var string|null */
    private $productUrl;

    /** @var string|null */
    private $buttonText;

    /** @var float|string|null */
    private $regularPrice;

    /** @var float|string|null */
    private $salePrice;

    /** @var MediaFile[] */
    private $downloadableMediaFiles = [];

    /** @var int|null */
    private $downloadLimit;

    /** @var int|null */
    private $downloadExpiry;

    /** @var string|null */
    private $sku;

    /** @var bool */
    private $isManageStock = false;

    /** @var float|string|null */
    private $stockQuantity;

    /** @var string|null */
    private $backorders;

    /** @var string|int|null */
    private $lowStockAmount;

    /** @var string|null */
    private $stockStatus;

    /** @var bool */
    private $isSoldIndividually = false;

    /** @var float|string|null */
    private $weight;

    /** @var float|string|null */
    private $length;

    /** @var float|string|null */
    private $width;

    /** @var float|string|null */
    private $height;

    /** @var int|null */
    private $shippingClassId;

    /** @var string|null */
    private $purchaseNote;

    /** @var bool */
    private $enableReviews = false;

    /** @var int|null */
    private $menuOrder;

    /** @var null|array */
    private $galleryImageUrls;

    /** @var null|ProductAttribute[] */
    private $attributes;

    /*
     *
     */

    /** @var TransformableFieldList|null */
    private $transformableFields = null;

    /** @var TransformableFieldList|null */
    private $interactableFields = null;

    /**
     * @return string|null
     */
    public function getProductType(): ?string {
        return $this->productType;
    }

    /**
     * @param string|null $productType
     */
    public function setProductType(?string $productType): void {
        $this->productType = $productType;
    }

    /**
     * @return bool
     */
    public function isVirtual(): bool {
        return $this->isVirtual;
    }

    /**
     * @param bool $isVirtual
     */
    public function setIsVirtual(bool $isVirtual): void {
        $this->isVirtual = $isVirtual;
    }

    /**
     * @return bool
     */
    public function isDownloadable(): bool {
        return $this->isDownloadable;
    }

    /**
     * @param bool $isDownloadable
     */
    public function setIsDownloadable(bool $isDownloadable): void {
        $this->isDownloadable = $isDownloadable;
    }

    /**
     * @param string|null $default Default value to be returned if the value is null.
     * @return string|null
     */
    public function getProductUrl(?string $default = null): ?string {
        return $this->productUrl !== null ? $this->productUrl : $default;
    }

    /**
     * @param string|null $productUrl
     */
    public function setProductUrl(?string $productUrl): void {
        $this->productUrl = $productUrl;
    }

    /**
     * @param string|null $default Default value to be returned if the value is null.
     * @return string|null
     */
    public function getButtonText(?string $default = null): ?string {
        return $this->buttonText !== null ? $this->buttonText : $default;
    }

    /**
     * @param string|null $buttonText
     */
    public function setButtonText(?string $buttonText): void {
        $this->buttonText = $buttonText;
    }

    /**
     * @param mixed $default Default value to be returned if the value is null.
     * @return float|string|null
     */
    public function getRegularPrice($default = null) {
        return $this->regularPrice !== null ? $this->regularPrice : $default;
    }

    /**
     * @param float|string|null $regularPrice
     */
    public function setRegularPrice($regularPrice): void {
        $this->regularPrice = $regularPrice;
    }

    /**
     * @param mixed $default Default value to be returned if the value is null.
     * @return float|string|null
     */
    public function getSalePrice($default = null) {
        return $this->salePrice !== null ? $this->salePrice : $default;
    }

    /**
     * @param float|string|null $salePrice
     */
    public function setSalePrice($salePrice): void {
        $this->salePrice = $salePrice;
    }

    /**
     * @return MediaFile[]
     */
    public function getDownloadableMediaFiles(): array {
        return $this->downloadableMediaFiles;
    }

    /**
     * @param MediaFile[] $downloadableMediaFiles
     */
    public function setDownloadableMediaFiles(array $downloadableMediaFiles): void {
        $this->downloadableMediaFiles = $downloadableMediaFiles;
    }

    /**
     * @param int|null $default Default value to be returned if the value is null.
     * @return int|null
     */
    public function getDownloadLimit(?int $default = null): ?int {
        return $this->downloadLimit !== null ? $this->downloadLimit : $default;
    }

    /**
     * @param int|null $downloadLimit
     */
    public function setDownloadLimit(?int $downloadLimit): void {
        $this->downloadLimit = $downloadLimit;
    }

    /**
     * @param int|null $default Default value to be returned if the value is null.
     * @return int|null
     */
    public function getDownloadExpiry(?int $default = null): ?int {
        return $this->downloadExpiry !== null ? $this->downloadExpiry : $default;
    }

    /**
     * @param int|null $downloadExpiry
     */
    public function setDownloadExpiry(?int $downloadExpiry): void {
        $this->downloadExpiry = $downloadExpiry;
    }

    /**
     * @param string|null $default Default value to be returned if the value is null.
     * @return string|null
     */
    public function getSku(?string $default = null): ?string {
        return $this->sku !== null ? $this->sku : $default;
    }

    /**
     * @param string|null $sku
     */
    public function setSku(?string $sku): void {
        $this->sku = $sku;
    }

    /**
     * @return bool
     */
    public function isManageStock(): bool {
        return $this->isManageStock;
    }

    /**
     * @param bool $isManageStock
     */
    public function setIsManageStock(bool $isManageStock): void {
        $this->isManageStock = $isManageStock;
    }

    /**
     * @param float|string|null $default Default value to be returned if the value is null.
     * @return float|string|null
     */
    public function getStockQuantity($default = null) {
        return $this->stockQuantity !== null ? $this->stockQuantity : $default;
    }

    /**
     * @param float|string|null $stockQuantity
     */
    public function setStockQuantity($stockQuantity): void {
        $this->stockQuantity = $stockQuantity;
    }

    /**
     * @return string|null
     */
    public function getBackorders(): ?string {
        return $this->backorders;
    }

    /**
     * @param string|null $backorders
     */
    public function setBackorders(?string $backorders): void {
        $this->backorders = $backorders;
    }

    /**
     * @param string|int|null $default Default value to be returned if the value is null.
     * @return string|int|null
     */
    public function getLowStockAmount($default = null) {
        return $this->lowStockAmount !== null ? $this->lowStockAmount : $default;
    }

    /**
     * @param string|int|null $lowStockAmount
     */
    public function setLowStockAmount($lowStockAmount): void {
        $this->lowStockAmount = $lowStockAmount;
    }

    /**
     * @return string|null
     */
    public function getStockStatus(): ?string {
        return $this->stockStatus;
    }

    /**
     * @param string|null $stockStatus
     */
    public function setStockStatus(?string $stockStatus): void {
        $this->stockStatus = $stockStatus;
    }

    /**
     * @return bool
     */
    public function isSoldIndividually(): bool {
        return $this->isSoldIndividually;
    }

    /**
     * @param bool $isSoldIndividually
     */
    public function setIsSoldIndividually(bool $isSoldIndividually): void {
        $this->isSoldIndividually = $isSoldIndividually;
    }

    /**
     * @param mixed $default Default value to be returned if the value is null.
     * @return float|string|null
     */
    public function getWeight($default = null) {
        return $this->weight !== null ? $this->weight : $default;
    }

    /**
     * @param float|string|null $weight
     */
    public function setWeight($weight): void {
        $this->weight = $weight;
    }

    /**
     * @param mixed $default Default value to be returned if the value is null.
     * @return float|string|null
     */
    public function getLength($default = null) {
        return $this->length !== null ? $this->length : $default;
    }

    /**
     * @param float|string|null $length
     */
    public function setLength($length): void {
        $this->length = $length;
    }

    /**
     * @param mixed $default Default value to be returned if the value is null.
     * @return float|string|null
     */
    public function getWidth($default = null) {
        return $this->width !== null ? $this->width : $default;
    }

    /**
     * @param float|string|null $width
     */
    public function setWidth($width): void {
        $this->width = $width;
    }

    /**
     * @param mixed $default Default value to be returned if the value is null.
     * @return float|string|null
     */
    public function getHeight($default = null) {
        return $this->height !== null ? $this->height : $default;
    }

    /**
     * @param float|string|null $height
     */
    public function setHeight($height): void {
        $this->height = $height;
    }

    /**
     * @param int|null $default Default value to be returned if the value is null.
     * @return int|null
     */
    public function getShippingClassId(?int $default = null): ?int {
        return $this->shippingClassId !== null ? $this->shippingClassId : $default;
    }

    /**
     * @param int|null $shippingClassId
     */
    public function setShippingClassId(?int $shippingClassId): void {
        $this->shippingClassId = $shippingClassId;
    }

    /**
     * @param string|null $default Default value to be returned if the value is null.
     * @return string|null
     */
    public function getPurchaseNote(?string $default = null): ?string {
        return $this->purchaseNote !== null ? $this->purchaseNote : $default;
    }

    /**
     * @param string|null $purchaseNote
     */
    public function setPurchaseNote(?string $purchaseNote): void {
        $this->purchaseNote = $purchaseNote;
    }

    /**
     * @return bool
     */
    public function isEnableReviews(): bool {
        return $this->enableReviews;
    }

    /**
     * @param bool $enableReviews
     */
    public function setEnableReviews(bool $enableReviews): void {
        $this->enableReviews = $enableReviews;
    }

    /**
     * @param int|null $default Default value to be returned if the value is null.
     * @return int|null
     */
    public function getMenuOrder(?int $default = null): ?int {
        return $this->menuOrder !== null ? $this->menuOrder : $default;
    }

    /**
     * @param int|null $menuOrder
     */
    public function setMenuOrder(?int $menuOrder): void {
        $this->menuOrder = $menuOrder;
    }

    /**
     * @return array|null
     */
    public function getGalleryImageUrls(): ?array {
        return $this->galleryImageUrls;
    }

    /**
     * @param array|null $galleryImageUrls
     */
    public function setGalleryImageUrls(?array $galleryImageUrls): void {
        $this->galleryImageUrls = $galleryImageUrls;
    }

    /**
     * @return ProductAttribute[]|null
     */
    public function getAttributes(): ?array {
        return $this->attributes;
    }

    /**
     * @param ProductAttribute[]|null $attributes
     */
    public function setAttributes(?array $attributes): void {
        $this->attributes = $attributes;
    }

    /**
     * Validates all of the data defined in this class. Removes/changes invalid ones.
     */
    public function validateData(): void {
        // Downloads
        $this->setDownloadLimit($this->getDownloadLimit(-1));
        $this->setDownloadExpiry($this->getDownloadExpiry(-1));

        // Sku
        $this->setSku($this->getSku(''));

        // Stocks
        $this->setLowStockAmount($this->getLowStockAmount(''));
        $this->setStockQuantity($this->getStockQuantity(''));

        // Weight and dimensions
        $this->setWeight($this->getWeight(''));
        $this->setLength($this->getLength(''));
        $this->setWidth($this->getWidth(''));
        $this->setHeight($this->getHeight(''));

        // Shipping class ID
        $this->setShippingClassId($this->getShippingClassId(0));

        // Purchase note
        $this->setPurchaseNote($this->getPurchaseNote(''));

        // When adding new values, make sure the values are valid and in a format that WooCommerce wants. For example,
        // prices might not be float after applying short codes. Likewise, other integer or float values might not be
        // valid. Decimal separators might not be valid, which might result in the prices not being saved, etc. Check
        // everything and validate all data.
    }

    public function getTransformableFields(): TransformableFieldList {
        if ($this->transformableFields === null) {
            $this->transformableFields = new TransformableFieldList([
                new TransformableField(static::FIELD_BUTTON_TEXT,              _wpcc('Product Button Text'),          ValueType::T_STRING),
                new TransformableField(static::FIELD_DOWNLOADABLE_TITLE,       _wpcc('Product Media Title'),          [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                new TransformableField(static::FIELD_DOWNLOADABLE_DESCRIPTION, _wpcc('Product Media Description'),    [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                new TransformableField(static::FIELD_DOWNLOADABLE_CAPTION,     _wpcc('Product Media Caption'),        [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                new TransformableField(static::FIELD_DOWNLOADABLE_ALT,         _wpcc('Product Media Alternate Text'), [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                new TransformableField(static::FIELD_PURCHASE_NOTE,            _wpcc('Product Purchase Note'),        ValueType::T_STRING),
                new TransformableField(static::FIELD_ATTRIBUTE_KEY,            _wpcc('Product Attribute Name'),       [ValueType::T_STRING, ValueType::T_COUNTABLE]),
                new TransformableField(static::FIELD_ATTRIBUTE_VALUE,          _wpcc('Product Attribute Values'),     [ValueType::T_STRING, ValueType::T_COUNTABLE]),
            ], new FieldConfig(EventGroupKey::POST_DATA));
        }

        return $this->transformableFields;
    }

    public function getInteractableFields(): TransformableFieldList {
        if ($this->interactableFields === null) {
            $this->interactableFields = (new TransformableFieldList(null, new FieldConfig(EventGroupKey::POST_DATA)))
                ->addAllFromList($this->getTransformableFields())

                // FIELD_ATTRIBUTE_KEY does not allow taxonomy keys to be modified without providing an additional
                // parameter, which is designed to block changes made by ValueSetter, to not transform the value by
                // transformation APIs. But, we want the users to be able to change the key via the filters. For that
                // purpose, FIELD_ATTRIBUTE_KEY_RELAXED is added. So, we simply replace the one that blocks changes with
                // the one that allows changes. See ProductAttribute::getKeyRelaxed() and
                // ProductAttribute::setKeyRelaxed() for more information.
                ->replaceByKey(
                    static::FIELD_ATTRIBUTE_KEY,
                    (new TransformableField(static::FIELD_ATTRIBUTE_KEY_RELAXED, _wpcc('Product Attribute Name'), [ValueType::T_STRING, ValueType::T_COUNTABLE]))
                        ->setFieldConfigs([new FieldConfig(EventGroupKey::POST_DATA)])
                )

                ->addAll([
                    new TransformableField(static::FIELD_PRODUCT_URL,    _wpcc('Product URL'),            ValueType::T_STRING),
                    new TransformableField(static::FIELD_REGULAR_PRICE,  _wpcc('Product Regular Price'),  ValueType::T_NUMERIC),
                    new TransformableField(static::FIELD_SALE_PRICE,     _wpcc('Product Sale Price'),     ValueType::T_NUMERIC),
                    new TransformableField(static::FIELD_SKU,            _wpcc('Product SKU'),            ValueType::T_STRING),
                    new TransformableField(static::FIELD_STOCK_QUANTITY, _wpcc('Product Stock Quantity'), ValueType::T_NUMERIC),
                    new TransformableField(static::FIELD_WEIGHT,         _wpcc('Product Weight'),         ValueType::T_NUMERIC),
                    new TransformableField(static::FIELD_LENGTH,         _wpcc('Product Length'),         ValueType::T_NUMERIC),
                    new TransformableField(static::FIELD_WIDTH,          _wpcc('Product Width'),          ValueType::T_NUMERIC),
                    new TransformableField(static::FIELD_HEIGHT,         _wpcc('Product Height'),         ValueType::T_NUMERIC),
                ]);
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