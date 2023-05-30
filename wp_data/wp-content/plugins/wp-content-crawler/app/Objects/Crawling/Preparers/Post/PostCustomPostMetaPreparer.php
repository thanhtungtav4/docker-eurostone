<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/11/2018
 * Time: 12:22
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post;


use WPCCrawler\Objects\Crawling\Data\Meta\PostMeta;
use WPCCrawler\Objects\Crawling\Data\Meta\PostMetaList;
use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostBotPreparer;
use WPCCrawler\Objects\Settings\Enums\SettingKey;

class PostCustomPostMetaPreparer extends AbstractPostBotPreparer {

    /** @var PostMetaList */
    private $customMetaList = null;

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public function prepare() {
        $this->customMetaList = new PostMetaList();

        // Get custom meta with selectors
        $this->prepareCustomMetaWithSelectors();

        // Get manually added custom post meta
        $this->prepareManuallyAddedCustomMeta();

        // Apply find and replace options
        $this->applyFindReplaces();

        // If there is no custom meta, stop.
        if($this->getCustomMetaList()->isEmpty()) return;

        // Store it
        $this->bot->getPostData()->setCustomMeta($this->getCustomMetaList()->getAll());
    }

    /**
     * Finds the custom meta whose selectors are specified and sets them to {@link $customMetaList}
     *
     * @since 1.8.0
     */
    private function prepareCustomMetaWithSelectors(): void {
        $postCustomPostMetaSelectors = $this->bot->getSetting(SettingKey::POST_CUSTOM_META_SELECTORS);

        // No need to continue if there is no selector.
        if(empty($postCustomPostMetaSelectors)) return;

        $list = $this->getCustomMetaList();
        foreach ($postCustomPostMetaSelectors as $selectorData) {
            // If there is no meta key, continue with the next one.
            if (!isset($selectorData["meta_key"]) || empty($selectorData["meta_key"])) continue;

            $isMultiple = isset($selectorData["multiple"]);

            // Extract the values
            $results = $this->bot->extractValuesWithSelectorData($this->getBot()->getCrawler(), $selectorData, 'text', false, !$isMultiple, true);
            if (!$results) continue;

            // Add the values
            /** @var string|string[] $results */
            $list->add(new PostMeta($selectorData["meta_key"], $results, $isMultiple));
        }
    }

    /**
     * Prepares the manually-entered custom meta and sets them to {@link $customMetaList}
     *
     * @since 1.8.0
     */
    private function prepareManuallyAddedCustomMeta(): void {
        $customPostMetaData = $this->bot->getSetting(SettingKey::POST_CUSTOM_META);

        // No need to continue if there is no custom meta.
        if(empty($customPostMetaData)) return;

        $list = $this->getCustomMetaList();
        foreach($customPostMetaData as $metaData) {
            if(!isset($metaData["key"]) || !$metaData["key"] || !isset($metaData["value"])) continue;
            $isMultiple = isset($metaData["multiple"]);

            $list->add(new PostMeta($metaData["key"], $metaData["value"], $isMultiple));
        }
    }

    /**
     * Applies find and replace options for the custom meta
     * @since 1.8.0
     */
    private function applyFindReplaces(): void {
        $postMetaSpecificFindAndReplaces = $this->bot->getSetting(SettingKey::POST_FIND_REPLACE_CUSTOM_META);
        $list = $this->getCustomMetaList();

        // If there is no custom meta or find-replace options, stop.
        if($list->isEmpty() || !$postMetaSpecificFindAndReplaces) return;

        // Collect find-replace configurations for each meta key. Each meta key can have multiple find-replace configs.
        // So, create a map where its keys are meta keys and values are an array of find-replace configs.
        /** @var array<string, array> $findReplaceConfigMap */
        $findReplaceConfigMap = [];
        foreach($postMetaSpecificFindAndReplaces as $key => $item) {
            $metaKey = $item['meta_key'];
            if ($metaKey === '') continue;

            // If no entry for this meta key exists in the map, create one and initialize it as an empty array.
            if (!isset($findReplaceConfigMap[$metaKey])) {
                $findReplaceConfigMap[$metaKey] = [];
            }

            // Append this find-replace config to its entry in the map.
            $findReplaceConfigMap[$metaKey][] = $item;
        }

        // Find replace in specific custom meta
        // Loop over each custom meta created previously
        foreach($list->getAll() as $i => $customMetaItem) {
            // Get current meta item's meta key and data
            $currentMetaKey = $customMetaItem->getKey();
            $results        = $customMetaItem->getData();

            // Continue with the next one if meta key or data does not exist in the current custom meta item.
            if(!$currentMetaKey || !$results) continue;

            // Get the find-replace configs for this meta key. If there is none, continue with the next one.
            $currentFindReplaces = $findReplaceConfigMap[$currentMetaKey] ?? null;
            if (!$currentFindReplaces) continue;

            // Apply find-replaces
            $results = is_array($results) 
                ? $this->bot->applyFindAndReplaces($currentFindReplaces, $results)
                : $this->bot->applyFindAndReplacesSingle($currentFindReplaces, $results);

            // If there are results, reassign it to the current custom meta item.
            $customMetaItem->setData($results);
        }
    }

    /*
     *
     */

    /**
     * @return PostMetaList
     * @since 1.11.0
     */
    public function getCustomMetaList(): PostMetaList {
        if ($this->customMetaList === null) {
            $this->customMetaList = new PostMetaList();
        }

        return $this->customMetaList;
    }
}
