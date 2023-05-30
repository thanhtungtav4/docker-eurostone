<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/11/2018
 * Time: 11:42
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post;


use Exception;
use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostBotPreparer;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\OptionsBox\OptionsBoxService;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Utils;

class PostPaginationInfoPreparer extends AbstractPostBotPreparer {

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public function prepare() {
        $postIsPaginate = $this->bot->getSettingForCheckbox(SettingKey::POST_PAGINATE);

        // Add whether or not to paginate the post when saving to the db
        $this->bot->getPostData()->setPaginate($postIsPaginate);

        // Before clearing the content, check if the post should be paginated and take related actions.
        // Do this before clearing the content, because pagination might be inside the content and the user might mark
        // it as unnecessary element.
        if(!$postIsPaginate) return;

        // Prepare next page URL
        $this->prepareNextPageUrl();

        // Prepare all page URLs
        $this->prepareAllPageUrls();
    }

    /**
     * Prepares next page URL
     * @since 1.8.0
     */
    private function prepareNextPageUrl(): void {
        $postNextPageUrlSelectors = $this->bot->getSetting(SettingKey::POST_NEXT_PAGE_URL_SELECTORS);
        if (!is_array($postNextPageUrlSelectors)) return;

        // Get next page URL of the post
        foreach($postNextPageUrlSelectors as $selectorData) {
            $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
            if (!$selector) continue;

            $attr = Utils::array_get($selectorData, SettingInnerKey::ATTRIBUTE);
            if (!$attr) $attr = 'href';

            // Get the next page URL
            $nextPageUrl = $this->bot->extractData($this->bot->getCrawler(), $selector, $attr, false, true, true);
            if (!is_string($nextPageUrl) || !$nextPageUrl) continue;

            // Resolve the next page URL
            try {
                $nextPageUrl = $this->bot->resolveUrl($nextPageUrl);
            } catch (Exception $e) {
                // Nothing to do here. This is a quite unlikely exception, since this method is run after
                // the post URL is set to the post bot.
                Informer::addError(_wpcc('URL could not be resolved') . ' - ' . $nextPageUrl)->addAsLog();
            }

            // Apply options box settings
            $optionsBoxApplier = OptionsBoxService::getInstance()->createApplierFromSelectorData($selectorData);
            if ($optionsBoxApplier) $nextPageUrl = $optionsBoxApplier->apply($nextPageUrl);

            $this->bot->getPostData()->setNextPageUrl($nextPageUrl);
            break;
        }

    }

    /**
     * Prepares all page URLs
     * @since 1.8.0
     */
    private function prepareAllPageUrls(): void {
        $postAllPageUrlsSelectors = $this->bot->getSetting(SettingKey::POST_NEXT_PAGE_ALL_PAGES_URL_SELECTORS);
        if (!is_array($postAllPageUrlsSelectors)) return;

        // Get all page URLs of the post
        foreach($postAllPageUrlsSelectors as $selectorData) {
            $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
            if (!$selector) continue;

            $attr = Utils::array_get($selectorData, SettingInnerKey::ATTRIBUTE);
            if (!$attr) $attr = 'href';

            // Get all page URLs
            $allPageUrls = $this->bot->extractData($this->bot->getCrawler(), $selector, $attr, "part_url", false, true);
            if (!is_array($allPageUrls) || !$allPageUrls) continue;

            // Sort the URLs according to their position in the source code
            $allPageUrls = Utils::array_msort($allPageUrls, ["start" => SORT_ASC]);

            // Prepare the URLs.
            foreach($allPageUrls as &$item) {
                if (!$item['data']) continue;

                try {
                    $item['data'] = $this->bot->resolveUrl($item['data']);
                } catch (Exception $e) {
                    // Nothing to do here. This is a quite unlikely exception, since this method is run after
                    // the post URL is set to the post bot.
                    Informer::addError(_wpcc('URL could not be resolved') . ' - ' . $item['data'])->addAsLog();
                }
            }

            // Apply options box settings
            $optionsBoxApplier = OptionsBoxService::getInstance()->createApplierFromSelectorData($selectorData);
            if ($optionsBoxApplier) $allPageUrls = $optionsBoxApplier->applyToArray($allPageUrls, 'data');

            $this->bot->getPostData()->setAllPageUrls($allPageUrls);
            break;
        }
    }

}
