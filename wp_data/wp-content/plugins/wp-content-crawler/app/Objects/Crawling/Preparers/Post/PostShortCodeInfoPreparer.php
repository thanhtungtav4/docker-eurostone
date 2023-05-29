<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/11/2018
 * Time: 12:04
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post;


use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostBotPreparer;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Utils;

class PostShortCodeInfoPreparer extends AbstractPostBotPreparer {

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public function prepare() {
        $postCustomShortCodeSelectors       = $this->bot->getSetting(SettingKey::POST_CUSTOM_CONTENT_SHORTCODE_SELECTORS);
        $shortCodeSpecificFindAndReplaces   = $this->bot->getSetting(SettingKey::POST_FIND_REPLACE_CUSTOM_SHORT_CODE, []);
        $findAndReplacesForCustomShortCodes = $this->bot->prepareFindAndReplaces($this->bot->getSetting(SettingKey::POST_FIND_REPLACE_CUSTOM_SHORTCODES, null));

        // If there is no selector, stop.
        if(!$postCustomShortCodeSelectors || empty($postCustomShortCodeSelectors)) return;

        foreach($postCustomShortCodeSelectors as $selectorData) {
            if(
                !isset($selectorData[SettingInnerKey::SELECTOR]) || empty($selectorData[SettingInnerKey::SELECTOR]) ||
                !isset($selectorData[SettingInnerKey::SHORT_CODE]) || empty($selectorData[SettingInnerKey::SHORT_CODE])
            )
                continue;

            $isSingle = isset($selectorData[SettingInnerKey::SINGLE]);

            $results = $this->getBot()->extractValuesWithSelectorData($this->bot->getCrawler(), $selectorData, "html", false, $isSingle, true);
            if (!$results) continue;

            $result = '';

            // If the results is an array, combine all the data into a single string.
            if(is_array($results)) {
                foreach($results as $key => $r) $result .= $r;
            } else {
                /** @var string $result */
                $result = $results;
            }

            // Find and replace in custom short codes
            $currentFindReplaces = [];
            foreach($shortCodeSpecificFindAndReplaces as $key => $item) {
                // If this replacement does not belong to the current short code, continue.
                if(Utils::array_get($item, "short_code") != $selectorData["short_code"]) continue;

                // Store the find-replace
                $currentFindReplaces[] = $item;

                // Remove this replacement configuration since it cannot be used for another short code.
                unset($shortCodeSpecificFindAndReplaces[$key]);
            }

            // Apply the replacements that are specific for current short code
            $result = $this->bot->applyFindAndReplacesSingle($currentFindReplaces, $result);

            // Apply find-and-replaces
            $result = $this->bot->findAndReplace($findAndReplacesForCustomShortCodes, $result);

            $shortCodeContent[] = [
                "data"          =>  $result,
                "short_code"    =>  $selectorData["short_code"]
            ];
        }

        if(!empty($shortCodeContent)) {
            $this->bot->getPostData()->setShortCodeData($shortCodeContent);
        }
    }

}
