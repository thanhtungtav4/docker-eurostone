<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/11/2018
 * Time: 19:46
 */

namespace WPCCrawler\PostDetail\WooCommerce;


use WPCCrawler\Exceptions\MethodNotExistException;
use WPCCrawler\Objects\Crawling\Preparers\TransformablePreparer;
use WPCCrawler\Objects\File\FileService;
use WPCCrawler\Objects\OptionsBox\Enums\OptionsBoxTab;
use WPCCrawler\Objects\OptionsBox\Enums\OptionsBoxType;
use WPCCrawler\Objects\OptionsBox\Enums\TabOptions\FileTemplatesTabOptions;
use WPCCrawler\Objects\OptionsBox\OptionsBoxConfiguration;
use WPCCrawler\PostDetail\Base\BasePostDetailData;
use WPCCrawler\PostDetail\Base\BasePostDetailService;
use WPCCrawler\PostDetail\WooCommerce\Data\ProductAttribute;

class WooCommerceService extends BasePostDetailService {

    /**
     * Get configurations for the options boxes of the settings.
     *
     * @return array A key-value pair. The keys are meta keys of the settings. The values are arrays storing the
     *               configuration for the options box for that setting. The values can be created by using
     *               {@link OptionsBoxConfiguration::init()}.
     * @since 1.8.0
     */
    public function getOptionsBoxConfigs(): array {
        return [
            // File URL selectors
            WooCommerceSettings::WC_FILE_URL_SELECTORS => OptionsBoxConfiguration::init()
                ->setType(OptionsBoxType::FILE)
                ->addTabOption(OptionsBoxTab::FILE_TEMPLATES, FileTemplatesTabOptions::OPTION_ALLOWED_TEMPLATE_TYPES, [
                    FileTemplatesTabOptions::TEMPLATE_TYPE_FILE_NAME,
                    FileTemplatesTabOptions::TEMPLATE_TYPE_MEDIA_TITLE,
                ])
                ->get(),
        ];
    }

    /**
     * Add assets, such as styles and scripts, that should be added to site tester page.
     * @since 1.8.0
     */
    public function addSiteTesterAssets(): void {
        $assetManager = WooCommerceAssetManager::getInstance();
        $assetManager->addTester();
    }

    /**
     * Apply the short codes in the values of the detail data. The short codes can be applied using
     * {@link ShortCodeReplacer::replaceShortCodes}, which is available as trait in this class.
     *
     * @param BasePostDetailData $data
     * @param array              $map        See {@link ShortCodeReplacer::replaceShortCodes}
     * @param array              $frForMedia Find-replace config that can be used replace media file URLs with local
     *                                       URLs.
     * @throws MethodNotExistException See {@link TransformablePreparer::prepare()}
     */
    public function applyShortCodes(BasePostDetailData $data, $map, $frForMedia): void {
        /** @var WooCommerceData $data */

        $fields = [
            // General
            WooCommerceData::FIELD_PRODUCT_URL,
            WooCommerceData::FIELD_BUTTON_TEXT,
            WooCommerceData::FIELD_REGULAR_PRICE,
            WooCommerceData::FIELD_SALE_PRICE,

            // Inventory
            WooCommerceData::FIELD_SKU,
            WooCommerceData::FIELD_STOCK_QUANTITY,

            // Shipping
            WooCommerceData::FIELD_WEIGHT,
            WooCommerceData::FIELD_LENGTH,
            WooCommerceData::FIELD_WIDTH,
            WooCommerceData::FIELD_HEIGHT,

            // Purchase note
            WooCommerceData::FIELD_PURCHASE_NOTE
        ];

        $preparer = new TransformablePreparer($data, $fields, function($text) use (&$map) {
            return $this->replaceShortCodesSingle($map, $text);
        });
        $preparer->prepare();

        // Attributes
        $this->applyShortCodesToAttributes($data, $map);

        // Apply short codes to media files
        $this->applyShortCodesToDownloadableMediaFiles($data, $map, $frForMedia);

        // Replace media file URLs using $frForMedia
        $galleryImageUrls = $data->getGalleryImageUrls();
        if ($galleryImageUrls) {
            $data->setGalleryImageUrls($this->applyShortCodesConsideringFileName($map, $galleryImageUrls, $frForMedia));
        }

        // TODO: Make sure all of the settings that use options box are handled here.
    }

    /**
     * Get category taxonomies for this post detail.
     *
     * @return array An array whose keys are category taxonomy names, and the values are the descriptions. E.g. for
     *               WooCommerce, ["product_cat" => "WooCommerce"]. The array can contain more than one category.
     * @since 1.8.0
     */
    public function getCategoryTaxonomies(): array {
        return [
            'product_cat' => _wpcc("WooCommerce")
        ];
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Applies short codes to names of the downloadable media files
     *
     * @param WooCommerceData $data       The data from which the downloadable media files will be retrieved
     * @param array           $map        See {@link ShortCodeReplacer::replaceShortCodes}
     * @param array           $frForMedia See {@link BasePostDetailService::applyShortCodes}
     * @since 1.8.0
     */
    private function applyShortCodesToDownloadableMediaFiles($data, &$map, &$frForMedia): void {
        // Apply the short codes and collect find-replace configurations that can be used to replace old media URLs
        // with the URLs changed after applying short codes to file names
        foreach($data->getDownloadableMediaFiles() as $mediaFile) {
            $currentFrForMedia = FileService::getInstance()->applyShortCodesToMediaFileName($mediaFile,$map);
            if (!$currentFrForMedia) continue;

            // Collect find-replace configurations
            $frForMedia = array_merge($frForMedia, $currentFrForMedia);
        }

        // Make the replacements
        foreach($data->getDownloadableMediaFiles() as $mediaFile) {
            $mediaFile->setMediaTitle($this->applyShortCodesConsideringFileNameSingle($map, $mediaFile->getMediaTitle(), $frForMedia));
        }
    }

    /**
     * Apply short codes to attribute names and values
     *
     * @param WooCommerceData $data
     * @param array $map See {@link BasePostDetailService::applyShortCodes()}
     * @since 1.8.0
     */
    private function applyShortCodesToAttributes($data, &$map): void {
        $attributes = $data->getAttributes();
        if (!$attributes) return;

        $prepared = [];
        foreach($attributes as $attribute) {
            $attrName   = $attribute->getKey();
            $attrValues = $attribute->getValues();

            $attrName   = $this->replaceShortCodesSingle($map, $attrName);
            $attrValues = $this->replaceShortCodes($map, $attrValues);

            if (!$attrName || !$attrValues) continue;

            // If this name exists, append the value to the existing key.
            foreach($prepared as $preparedAttribute) {
                /** @var ProductAttribute $preparedAttribute */
                if (strtolower($preparedAttribute->getKey()) == strtolower($attrName)) {
                    $preparedAttribute->setValues(array_unique(array_merge($preparedAttribute->getValues(), $attrValues)));
                    continue 2;
                }
            }

            $attribute->setKey($attrName, true);
            $attribute->setValues($attrValues);

            // Otherwise, add it as a new key.
            $prepared[] = $attribute;
        }

        $data->setAttributes($prepared);
    }
}
