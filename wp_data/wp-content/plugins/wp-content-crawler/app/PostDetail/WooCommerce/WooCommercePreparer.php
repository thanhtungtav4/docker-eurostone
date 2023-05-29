<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 16/11/2018
 * Time: 18:58
 */

namespace WPCCrawler\PostDetail\WooCommerce;


use Illuminate\Support\Arr;
use WPCCrawler\Objects\File\FileService;
use WPCCrawler\Objects\File\MediaFile;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\PostDetail\Base\BasePostDetailPreparer;
use WPCCrawler\PostDetail\WooCommerce\Preparers\ProductAttributePreparer;

class WooCommercePreparer extends BasePostDetailPreparer {

    /** @var WooCommerceData */
    private $wcData;

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public function prepare() {
        // Store the detail data in an instance variable for easy of use
        $wooData = $this->getDetailData();
        if (!($wooData instanceof WooCommerceData)) {
            return;
        }

        $this->wcData = $wooData;

        // Prepare the data
        $this->prepareProductType();
        $this->prepareIsVirtual();
        $this->prepareIsDownloadable();

        // General
        $this->prepareExternalProductDetails();
        $this->prepareRegularPrice();
        $this->prepareSalePrice();
        $this->prepareDownloadableFileUrls();
        $this->prepareDownloadLimit();
        $this->prepareDownloadExpiry();

        // Inventory
        $this->prepareSku();
        $this->prepareManageStock();
        $this->prepareStockQuantity();
        $this->prepareBackorders();
        $this->prepareLowStockAmount();
        if (!$this->wcData->isManageStock()) $this->prepareStockStatus();
        $this->prepareIsSoldIndividually();

        // Shipping
        $this->prepareWeight();
        $this->prepareLength();
        $this->prepareWidth();
        $this->prepareHeight();
        $this->prepareShippingClassId();

        // Attributes
        (new ProductAttributePreparer($this->getBot(), $this->getDetailData()))->prepare();

        // Advanced
        $this->preparePurchaseNote();
        $this->prepareEnableReviews();
        $this->prepareMenuOrder();

        // Others
        $this->prepareGalleryImageUrls();
    }

    /*
     * HELPERS
     */

    /** Prepares the product type */
    private function prepareProductType(): void {
        // When the settings of the plugin are saved, values of WooCommerce options are not saved to the database since
        // the options are not visible prior to selecting the post type as "product". Hence, the product type is not
        // set. Here, we set the product type as "simple" in case it is not available in the database.
        $productType = $this->bot->getSetting(WooCommerceSettings::WC_PRODUCT_TYPE, 'simple');
        $this->wcData->setProductType(is_string($productType) ? $productType : null);
    }

    /** Prepares whether a product is virtual or not */
    private function prepareIsVirtual(): void {
        $isVirtual = $this->bot->getSettingForCheckbox(WooCommerceSettings::WC_VIRTUAL);
        $this->wcData->setIsVirtual($isVirtual);
    }

    /** Prepares whether a product is downloadable or not */
    private function prepareIsDownloadable(): void {
        $isDownloadable = $this->bot->getSettingForCheckbox(WooCommerceSettings::WC_DOWNLOADABLE);
        $this->wcData->setIsDownloadable($isDownloadable);
    }

    /** Prepares product URL and button text */
    private function prepareExternalProductDetails(): void {
        $productUrl = $this->bot->getSetting(WooCommerceSettings::WC_PRODUCT_URL, '');
        $this->wcData->setProductUrl(is_string($productUrl) ? $productUrl : '');

        $buttonText = $this->bot->getSetting(WooCommerceSettings::WC_BUTTON_TEXT, '');
        $this->wcData->setButtonText(is_string($buttonText) ? $buttonText : '');
    }

    /** Prepares regular price of the product */
    private function prepareRegularPrice(): void {
        $result = $this->getValuesForSelectorSetting(WooCommerceSettings::WC_REGULAR_PRICE_SELECTORS, 'text', false, true, true);
        if (!$result) return;

        $this->wcData->setRegularPrice($result);
    }

    /** Prepares regular price of the product */
    private function prepareSalePrice(): void {
        $result = $this->getValuesForSelectorSetting(WooCommerceSettings::WC_SALE_PRICE_SELECTORS, 'text', false, true, true);
        if (!$result) return;

        $this->wcData->setSalePrice($result);
    }

    /** Prepares downloadable file URLs of the product */
    private function prepareDownloadableFileUrls(): void {
        // If the product is not downloadable, stop.
        if (!$this->wcData->isDownloadable()) return;

        // Get the file selectors
        $selectors = $this->bot->getSetting(WooCommerceSettings::WC_FILE_URL_SELECTORS);
        if (!$selectors) return;

        $crawler = $this->getBot()->getCrawler();
        if (!$crawler) return;

        // Save the media files
        $mediaFiles = FileService::getInstance()->saveFilesWithSelectors($this->getBot(), $crawler, $selectors);
        if (!$mediaFiles) return;

        $this->wcData->setDownloadableMediaFiles($mediaFiles);
    }

    /** Prepares download limit */
    private function prepareDownloadLimit(): void {
        $downloadLimit = $this->bot->getSetting(WooCommerceSettings::WC_DOWNLOAD_LIMIT);
        $this->wcData->setDownloadLimit($downloadLimit ? (int) $downloadLimit : 0);
    }

    /** Prepares download expiry */
    private function prepareDownloadExpiry(): void {
        $downloadExpiry = $this->bot->getSetting(WooCommerceSettings::WC_DOWNLOAD_EXPIRY);
        $this->wcData->setDownloadExpiry($downloadExpiry ? (int) $downloadExpiry : 0);
    }

    /** Prepares SKU */
    private function prepareSku(): void {
        $result = $this->getValuesForSelectorSetting(WooCommerceSettings::WC_SKU_SELECTORS, 'text', false, true, true);
        if (!$result) return;

        $this->wcData->setSku(is_string($result) ? $result : null);
    }

    /** Prepares whether stock management is enabled or not */
    private function prepareManageStock(): void {
        $isManageStock = $this->bot->getSettingForCheckbox(WooCommerceSettings::WC_MANAGE_STOCK);
        $this->wcData->setIsManageStock($isManageStock);
    }

    /** Prepares stock quantity */
    private function prepareStockQuantity(): void {
        $result = $this->getValuesForSelectorSetting(WooCommerceSettings::WC_STOCK_QUANTITY_SELECTORS, 'text', false, true, true);
        if (!$result) return;

        $this->wcData->setStockQuantity($result);
    }

    /** Prepares backorder availability information */
    private function prepareBackorders(): void {
        $backorders = $this->bot->getSetting(WooCommerceSettings::WC_BACKORDERS);
        if ($backorders) $this->wcData->setBackorders($backorders);
    }

    /** Prepares low stock amount */
    private function prepareLowStockAmount(): void {
        $lowStockAmount = $this->bot->getSetting(WooCommerceSettings::WC_LOW_STOCK_AMOUNT);
        if ($lowStockAmount) $this->wcData->setLowStockAmount((int) $lowStockAmount);
    }

    /** Prepares stock status information */
    private function prepareStockStatus(): void {
        $status = $this->bot->getSetting(WooCommerceSettings::WC_STOCK_STATUS);
        $this->wcData->setStockStatus(is_string($status) ? $status : null);
    }

    /** Prepares whether the product is sold individually or not */
    private function prepareIsSoldIndividually(): void {
        $isSoldIndividually = $this->bot->getSettingForCheckbox(WooCommerceSettings::WC_SOLD_INDIVIDUALLY);
        $this->wcData->setIsSoldIndividually($isSoldIndividually);
    }

    /** Prepares weight of the product */
    private function prepareWeight(): void {
        $result = $this->getValuesForSelectorSetting(WooCommerceSettings::WC_WEIGHT_SELECTORS, 'text', false, true, true);
        if (!$result) return;

        $this->wcData->setWeight($result);
    }

    /** Prepares length of the product */
    private function prepareLength(): void {
        $result = $this->getValuesForSelectorSetting(WooCommerceSettings::WC_LENGTH_SELECTORS, 'text', false, true, true);
        if (!$result) return;

        $this->wcData->setLength($result);
    }

    /** Prepares width of the product */
    private function prepareWidth(): void {
        $result = $this->getValuesForSelectorSetting(WooCommerceSettings::WC_WIDTH_SELECTORS, 'text', false, true, true);
        if (!$result) return;

        $this->wcData->setWidth($result);
    }

    /** Prepares height of the product */
    private function prepareHeight(): void {
        $result = $this->getValuesForSelectorSetting(WooCommerceSettings::WC_HEIGHT_SELECTORS, 'text', false, true, true);
        if (!$result) return;

        $this->wcData->setHeight($result);
    }

    /** Prepares shipping class ID  */
    private function prepareShippingClassId(): void {
        $shippingClassId = $this->bot->getSetting(WooCommerceSettings::WC_PRODUCT_SHIPPING_CLASS);
        if (!is_numeric($shippingClassId)) return;

        $this->wcData->setShippingClassId((int) $shippingClassId);
    }

    /**
     * Prepares the purchase note
     * @since 1.8.0
     */
    private function preparePurchaseNote(): void {
        // Find the purchase notes using the given selectors
        $addAll = $this->bot->getSettingForCheckbox(WooCommerceSettings::WC_PURCHASE_NOTE_ADD_ALL_FOUND);
        $purchaseNotes = $this->getValuesForSelectorSetting(WooCommerceSettings::WC_PURCHASE_NOTE_SELECTORS, 'text', false, !$addAll, true);

        // Make sure the purchase notes variable is a flat array
        if (is_array($purchaseNotes)) $purchaseNotes = Arr::flatten($purchaseNotes);
        if (!$purchaseNotes) $purchaseNotes = [];
        if (!is_array($purchaseNotes)) $purchaseNotes = [$purchaseNotes];

        // Get purchase note settings
        $customPurchaseNotes = $this->bot->getSetting(WooCommerceSettings::WC_CUSTOM_PURCHASE_NOTES, []);
        $alwaysAddCustomPurchaseNote = $this->bot->getSettingForCheckbox(WooCommerceSettings::WC_ALWAYS_ADD_CUSTOM_PURCHASE_NOTE);

        // Collect all purchase notes in an array
        $finalPurchaseNotes = [];

        // Custom purchase note should be added if there is no purchase note found by a CSS selector or the user is
        // specified that it should always be added.
        if ((!$purchaseNotes || $alwaysAddCustomPurchaseNote) && $customPurchaseNotes) {
            // Select one of the custom purchase notes
            $customNote = $customPurchaseNotes[array_rand($customPurchaseNotes, 1)];
            if ($customNote) $finalPurchaseNotes[] = $customNote;
        }

        // If there are purchase notes found by the CSS selectors, append them.
        $finalPurchaseNotes = array_merge($finalPurchaseNotes, $purchaseNotes);

        // Create the final purchase note by combining all purchase notes with a new line char
        $finalPurchaseNote = implode("\n", $finalPurchaseNotes);

        // Assign the note in the data
        $this->wcData->setPurchaseNote($finalPurchaseNote);
    }

    /**
     * Prepares 'enable reviews' value of the data
     * @since 1.8.0
     */
    private function prepareEnableReviews(): void {
        $this->wcData->setEnableReviews($this->bot->getSettingForCheckbox(WooCommerceSettings::WC_ENABLE_REVIEWS));
    }

    /**
     * Prepares menu order value
     * @since 1.8.0
     */
    private function prepareMenuOrder(): void {
        $menuOrder = $this->bot->getSetting(WooCommerceSettings::WC_MENU_ORDER, 0);
        if (!is_numeric($menuOrder)) return;

        $this->wcData->setMenuOrder((int) $menuOrder);
    }

    /**
     * Prepares gallery image URLs
     * @since 1.8.0
     */
    private function prepareGalleryImageUrls(): void {
        // Saving gallery images is main post preparers' responsibility. Here, we first check if the user wants to
        // save the gallery images as WooCommerce gallery. If so, we get the main post's gallery image URLs and set them
        // to WooCommerce data's gallery image URLs. This is just for reaching gallery image URLs from the WooCommerce
        // data, when they are needed.
        $saveAsWooCommerceGallery = $this->bot->getSettingForCheckbox(SettingKey::POST_SAVE_IMAGES_AS_WOOCOMMERCE_GALLERY);
        if (!$saveAsWooCommerceGallery) return;

        // Get the attachments from the main post data. They contain the gallery image URLs.
        $attachmentData = $this->bot->getPostData()->getAttachmentData();
        if (!is_array($attachmentData)) return;

        // Now, extract the gallery image URLs from the attachment data and set them to the WooCommerce data's gallery
        // image URLs.
        $this->wcData->setGalleryImageUrls(array_filter(array_map(function($mediaFile) {
            /** @var MediaFile $mediaFile */
            return $mediaFile->isGalleryImage() ? $mediaFile->getLocalUrl() : null;
        }, $attachmentData)));
    }

}
