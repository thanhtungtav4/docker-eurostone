<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/11/2018
 * Time: 12:13
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post;


use Illuminate\Support\Arr;
use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostBotPreparer;
use WPCCrawler\Objects\Settings\Enums\SettingKey;

class PostMetaAndTagInfoPreparer extends AbstractPostBotPreparer {

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public function prepare() {
        // Prepare tags
        $this->prepareTags();

        // Prepare meta description
        $this->prepareMetaDescription();
    }

    /**
     * Prepares tags using post tag selectors and meta keywords
     * @since 1.8.0
     */
    private function prepareTags(): void {
        $postData = $this->bot->getPostData();

        $postSaveMetaKeywords               = $this->bot->getSetting(SettingKey::POST_META_KEYWORDS);
        $postMetaKeywordsAsTags             = $this->bot->getSetting(SettingKey::POST_META_KEYWORDS_AS_TAGS);

        $findAndReplacesForTags             = $this->bot->prepareFindAndReplaces($this->bot->getSetting(SettingKey::POST_FIND_REPLACE_TAGS, null));
        $findAndReplacesForMetaKeywords     = $this->bot->prepareFindAndReplaces($this->bot->getSetting(SettingKey::POST_FIND_REPLACE_META_KEYWORDS, null));

        // Get tags
        $allTags = $this->getValuesForSelectorSetting(SettingKey::POST_TAG_SELECTORS,'text', false, false, true);
        if (!$allTags) $allTags = [];
        $allTags = Arr::flatten(array_map(function($tag) {

            // Explode and trim
            return array_map(function($tagFromExplode) {
                return trim($tagFromExplode);
            }, explode(",", $tag));

        }, Arr::flatten($allTags)));

        // Store the tags found by given selectors
        if(!empty($allTags)) $postData->setTags(array_unique($allTags));

        // Meta keywords
        if($postSaveMetaKeywords) {
            $metaKeywords = $this->bot->extractData($this->bot->getCrawler(), "meta[name=keywords]", "content", false, true, true);

            if(is_string($metaKeywords)) {
                $metaKeywords = trim($this->bot->findAndReplace($findAndReplacesForMetaKeywords, $metaKeywords), ",");

                $postData->setMetaKeywords($metaKeywords);

                if($postMetaKeywordsAsTags) {
                    $metaKeywordsAsTags = array_unique(explode(',', $metaKeywords));

                    // Add these tags to allTags as well
                    $allTags = array_merge($allTags, $metaKeywordsAsTags);

                    $postData->setMetaKeywordsAsTags($metaKeywordsAsTags);
                }
            }
        }

        // Prepare the tags by applying find-and-replaces
        if(!empty($allTags)) {
            foreach ($allTags as $mTag) {
                if ($mTag = $this->bot->findAndReplace($findAndReplacesForTags, $mTag)) {
                    $tagsPrepared[] = $mTag;
                }
            }

            // Add all tags to the main data
            if(!empty($tagsPrepared)) {
                $postData->setPreparedTags(array_unique($tagsPrepared));
            }
        }
    }

    /**
     * Prepares meta description
     * @since 1.8.0
     */
    private function prepareMetaDescription(): void {
        $postSaveMetaDescription = $this->bot->getSetting(SettingKey::POST_META_DESCRIPTION);
        if(!$postSaveMetaDescription) return;

        // Get the meta description
        $metaDescription = $this->bot->extractData($this->bot->getCrawler(), "meta[name=description]", "content", false, true, true);
        if (!is_string($metaDescription)) return;

        // Apply find and replaces
        $findAndReplacesForMetaDescription  = $this->bot->prepareFindAndReplaces($this->bot->getSetting(SettingKey::POST_FIND_REPLACE_META_DESCRIPTION, null));
        $metaDescription = $this->bot->findAndReplace($findAndReplacesForMetaDescription, $metaDescription);

        // Store it
        $this->bot->getPostData()->setMetaDescription($metaDescription);
    }
}
