<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 17/11/2019
 * Time: 23:08
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post;


use WPCCrawler\Exceptions\MethodNotExistException;
use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostBotPreparer;
use WPCCrawler\Objects\Crawling\Preparers\TransformablePreparer;
use WPCCrawler\Objects\Html\EmptyHtmlTagRemover;
use WPCCrawler\Objects\Html\ScriptRemover;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Traits\FindAndReplaceTrait;
use WPCCrawler\PostDetail\PostDetailsService;

class PostDataPreparer extends AbstractPostBotPreparer {

    use FindAndReplaceTrait;

    /** @var array Find and replace configuration that will be applied to everything */
    private $frConfig = null;

    /**
     * Prepare the post bot
     *
     * @return void
     * @throws MethodNotExistException
     */
    public function prepare() {
        // Prepare instance variables
        $this->initFindReplaceConfig();

        // Prepare the post data
        $postData = $this->getBot()->getPostData();

        $cbPrepare = function($text) {
            return $this->applyPreparations($text);
        };

        $preparer = new TransformablePreparer($postData, array_keys($postData->getInteractableFields()->toAssociativeArray()), $cbPrepare);
        $preparer->prepare();

        // Prepare the post details
        PostDetailsService::getInstance()->prepareDetailData($this->getBot(), $cbPrepare);
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Apply preparations to a text considering the settings configured by the user
     *
     * @param string|null $text Text that should be prepared
     * @return string Prepared text
     * @since 1.9.0
     */
    private function applyPreparations(?string $text): string {
        if ($text === null) return '';

        if ($this->getBot()->getSettingForCheckbox(SettingKey::POST_REMOVE_EMPTY_HTML_TAGS)) {
            $text = $this->removeEmptyHtmlTags($text);
        }

        if ($this->getBot()->getSettingForCheckbox(SettingKey::POST_REMOVE_SCRIPTS)) {
            $text = $this->removeScripts($text);
        }

        // Replace &amp; with & character since Crawler turns & characters to &amp;.
        $text = $this->findAndReplace($this->frConfig, $text, false);

        return $text;
    }

    /**
     * Remove empty tags from an HTML code
     *
     * @param string|null $html HTML code whose empty tags should be removed
     * @return string HTML code whose empty tags are removed
     * @since 1.9.0
     */
    private function removeEmptyHtmlTags(?string $html): string {
        if ($html === null) return '';

        return (new EmptyHtmlTagRemover($html))->removeEmptyTags();
    }

    /**
     * Remove scripts from an HTML code
     *
     * @param string|null $html HTML code whose scripts should be removed
     * @return string HTML code whose scripts are removed
     * @since 1.9.0
     */
    private function removeScripts(?string $html): string {
        if ($html === null) return '';

        return (new ScriptRemover($html))->removeScripts()->getHtml();
    }

    /**
     * Initializes {@link frConfig}
     *
     * @since 1.9.0
     */
    private function initFindReplaceConfig(): void {
        if ($this->frConfig !== null) return;
        $this->frConfig = [
            $this->createFindReplaceConfig('&amp;', '&')
        ];
    }
}