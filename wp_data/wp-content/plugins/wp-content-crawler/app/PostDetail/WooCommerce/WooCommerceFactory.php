<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/11/2018
 * Time: 08:50
 */

namespace WPCCrawler\PostDetail\WooCommerce;


use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\PostDetail\Base\BasePostDetailData;
use WPCCrawler\PostDetail\Base\BasePostDetailDeleter;
use WPCCrawler\PostDetail\Base\BasePostDetailDuplicateChecker;
use WPCCrawler\PostDetail\Base\BasePostDetailFactory;
use WPCCrawler\Objects\Crawling\Data\PostSaverData;
use WPCCrawler\PostDetail\Base\BasePostDetailPreparer;
use WPCCrawler\PostDetail\Base\BasePostDetailSaver;
use WPCCrawler\PostDetail\Base\BasePostDetailService;
use WPCCrawler\PostDetail\Base\BasePostDetailSettings;
use WPCCrawler\PostDetail\Base\BasePostDetailTester;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Factory\BaseWooAdapterFactory;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Factory\Woo33AdapterFactory;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Factory\Woo34AdapterFactory;
use WPCCrawler\PostDetail\WooCommerce\Adapter\Factory\Woo35AdapterFactory;
use WPCCrawler\Utils;

class WooCommerceFactory extends BasePostDetailFactory {

    /**
     * @return string Name of this post detail. For example, "WooCommerce".
     * @since 1.9.0
     */
    public function getName(): string {
        return "WooCommerce";
    }

    /**
     * @return string An identifier for this post detail. This must not contain any spaces. E.g. "woocommerce". This
     *                value is used, for example, in input names.
     * @since 1.9.0
     */
    public function getIdentifier(): string {
        return "product";
    }

    /**
     * @return bool True if the detail is available to be shown and interacted with. Otherwise, false.
     */
    protected function getAvailability(): bool {
        // If WooCommerce is not currently active, this post detail is not available.
        if (!Utils::isPluginActive('woocommerce/woocommerce.php')) return false;

        // Make sure 'wc' function exists, since we need it to get the version of WooCommerce installed.
        if (!function_exists('wc')) {
            Informer::addError(sprintf(_wpcc('%1$s function does not exist. You can try to install one of the previous versions of WooCommerce.'), 'wc'))
                ->addAsLog();
            return false;
        }

        // Get version of WooCommerce
        $version = wc()->version;

        // This is available starting from 3.3
        if (version_compare($version, '3.3', '<')) return false;

        return true;
    }

    /**
     * @param SettingsImpl $postSettings
     * @return bool
     * @since 1.8.0
     */
    protected function getAvailabilityForPost(SettingsImpl $postSettings): bool {
        // This is only available if the post type is WooCommerce product.
        $postTypeKey = SettingKey::WPCC_POST_TYPE;
        $postType = $postSettings->getSetting(SettingKey::DO_NOT_USE_GENERAL_SETTINGS) ? $postSettings->getSetting($postTypeKey) : get_option($postTypeKey);

        return strtolower($postType) === 'product';
    }

    /**
     * @param SettingsImpl|null $postSettings
     * @return null|WooCommerceSettings
     * @since 1.8.0
     */
    protected function createSettings(?SettingsImpl $postSettings): ?BasePostDetailSettings {
        return new WooCommerceSettings($postSettings);
    }

    /**
     * @return WooCommerceData
     */
    protected function createData(): BasePostDetailData {
        return new WooCommerceData();
    }

    /**
     * @param PostBot            $postBot
     * @param BasePostDetailData $data
     * @return WooCommercePreparer
     */
    protected function createPreparer(PostBot $postBot, BasePostDetailData $data): BasePostDetailPreparer {
        return new WooCommercePreparer($postBot, $data);
    }

    /**
     * @param PostSaverData      $postSaverData
     * @param BasePostDetailData $data
     * @return WooCommerceSaver|null
     */
    protected function createSaver(PostSaverData $postSaverData, BasePostDetailData $data): ?BasePostDetailSaver {
        /** @var BaseWooAdapterFactory[] $adapterFactories */
        $adapterFactories = [
            Woo35AdapterFactory::getInstance(),
            Woo34AdapterFactory::getInstance(),
            Woo33AdapterFactory::getInstance()
        ];

        // Get the current WooCommerce version
        $version = wc()->version;

        // Find a suitable adapter factory
        $suitableFactory = null;
        foreach($adapterFactories as $adapterFactory) {
            $min = $adapterFactory->getMinVersion();
            $max = $adapterFactory->getMaxVersion();

            if (version_compare($version, $min, '<')) continue;
            if ($max && version_compare($version, $max, '>=')) continue;

            $suitableFactory = $adapterFactory;
            break;
        }

        if (!$suitableFactory) {
            Informer::addError(_wpcc('A suitable adapter factory could not be found for current WooCommerce version.'))
                ->addAsLog();
            return null;
        }

        return new WooCommerceSaver($postSaverData, $data, $suitableFactory);
    }

    /**
     * @param PostBot            $postBot
     * @param BasePostDetailData $detailData
     * @return null|WooCommerceTester
     * @since 1.8.0
     */
    protected function createTester(PostBot $postBot, BasePostDetailData $detailData): ?BasePostDetailTester {
        return new WooCommerceTester($postBot, $detailData);
    }

    /**
     * Create a service for this post detail.
     *
     * @return WooCommerceService
     */
    protected function createService(): ?BasePostDetailService {
        return new WooCommerceService();
    }

    /**
     * Create a duplicate checker for this post detail.
     *
     * @param PostBot|null            $postBot
     * @param BasePostDetailData|null $detailData
     * @return WooCommerceDuplicateChecker
     * @since 1.8.0
     */
    protected function createDuplicateChecker(?PostBot $postBot, ?BasePostDetailData $detailData): ?BasePostDetailDuplicateChecker {
        return new WooCommerceDuplicateChecker($postBot, $detailData);
    }

    /**
     * Create a deleter for this post detail.
     *
     * @return WooCommerceDeleter
     * @since 1.8.0
     */
    protected function createDeleter(): ?BasePostDetailDeleter {
        return new WooCommerceDeleter();
    }
}
