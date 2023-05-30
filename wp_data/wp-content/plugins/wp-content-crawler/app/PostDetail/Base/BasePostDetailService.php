<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/11/2018
 * Time: 19:44
 */

namespace WPCCrawler\PostDetail\Base;


use WPCCrawler\Objects\Traits\ShortCodeReplacerAndFindAndReplace;

abstract class BasePostDetailService {

    use ShortCodeReplacerAndFindAndReplace;

    /**
     * Get configurations for the options boxes of the settings.
     *
     * @return array|null A key-value pair. The keys are meta keys of the settings. The values are arrays storing the
     *                    configuration for the options box for that setting. The values can be created by using
     *                    {@link OptionsBoxConfiguration::init()}.
     * @since 1.8.0
     */
    public function getOptionsBoxConfigs(): ?array {
        return null;
    }

    /**
     * Add assets, such as styles and scripts, that should be added to site settings page.
     * @since 1.8.0
     */
    public function addSiteSettingsAssets(): void {

    }

    /**
     * Add assets, such as styles and scripts, that should be added to site tester page.
     * @since 1.8.0
     */
    public function addSiteTesterAssets(): void {

    }

    /**
     * Apply the short codes in the values of the detail data. The short codes can be applied using
     * {@link ShortCodeReplacer::replaceShortCodes}, which is available as trait in this class.
     *
     * @param BasePostDetailData $data
     * @param array              $map        See {@link ShortCodeReplacer::replaceShortCodes}
     * @param array              $frForMedia Find-replace config that can be used replace media file URLs with local
     *                                       URLs.
     */
    public function applyShortCodes(BasePostDetailData $data, $map, $frForMedia): void {

    }

    /**
     * Get category taxonomies for this post detail.
     *
     * @return array|null An array whose keys are category taxonomy names, and the values are the descriptions. E.g. for
     *                    WooCommerce, ["product_cat" => "WooCommerce"]. The array can contain more than one category.
     * @since 1.8.0
     */
    public function getCategoryTaxonomies(): ?array {
        return null;
    }

}