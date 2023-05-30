<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/12/2018
 * Time: 09:47
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\Crawling\Preparers;


use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Crawling\Preparers\Interfaces\Preparer;
use WPCCrawler\Objects\GlobalShortCodes\GlobalShortCodeService;
use WPCCrawler\Objects\GlobalShortCodes\ShortCodes\IFrameGlobalShortCode;
use WPCCrawler\Objects\GlobalShortCodes\ShortCodes\ScriptGlobalShortCode;
use WPCCrawler\Objects\Settings\Enums\SettingKey;

/**
 * Prepares find-replace configs whose purpose is to provide convenience for the user, e.g. removing links and replacing
 * iframes with iframe short code.
 *
 * @package WPCCrawler\Objects\Crawling\Preparers
 * @since   1.8.0
 */
class BotConvenienceFindReplacePreparer implements Preparer {

    const REMOVE_LINKS_FIND     = '/<a\b[^>]*>((?:\n|.)*?)<\/a>/';
    const REMOVE_LINKS_REPLACE  = '$1';

    /*
     *
     */

    /** @var string */
    private $convertIframesToShortCodeFind           = '/<iframe\s([^>]*)>[^<]*<\/iframe>/';
    /** @var string */
    private $convertIframesToShortCodeReplaceFormat  = '[%1$s $1]';

    /** @var string */
    private $convertScriptsToShortCodeFind           = '/<script\s([^>]*)>[^<]*<\/script>/';
    /** @var string */
    private $convertScriptsToShortCodeReplaceFormat  = '[%1$s $1]';

    /** @var AbstractBot */
    private $bot;

    /** @var array|null Stores the prepared find-replace configs. */
    private $fr = null;

    public function __construct(AbstractBot $bot) {
        $this->bot = $bot;
    }

    /**
     * @return array Prepares find-replace config
     * @since 1.8.0
     */
    public function prepare(): array {
        // Prepare the find and replace configurations only if they were not prepared.
        if ($this->fr === null) {
            $this->fr = [];
            $this->prepareRemoveLinks();
            $this->prepareConvertIframesToShortCode();
            $this->prepareConvertScriptsToShortCode();
        }

        return $this->fr ?: [];
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Prepare link removal find-replace configuration
     *
     * @since 1.8.0
     */
    private function prepareRemoveLinks(): void {
        $removeLinksFromShortCodes = $this->bot->getSettingForCheckbox(SettingKey::POST_REMOVE_LINKS_FROM_SHORT_CODES);

        // Remove links from short codes
        if(!$removeLinksFromShortCodes) return;

        // Prepare the config
        $this->fr[] = $this->bot->createFindReplaceConfig(
            trim(static::REMOVE_LINKS_FIND, '/'),
            static::REMOVE_LINKS_REPLACE,
            true
        );
    }

    /**
     * Prepare find-replace config that can be used to convert iframe elements to iframe short code
     * @since 1.8.0
     */
    private function prepareConvertIframesToShortCode(): void {
        $convertIframesToShortCode = $this->bot->getSettingForCheckbox(SettingKey::POST_CONVERT_IFRAMES_TO_SHORT_CODE);

        // Convert iframes to short code
        if (!$convertIframesToShortCode) return;

        // Prepare the config
        $this->fr[] = $this->bot->createFindReplaceConfig(
            trim($this->convertIframesToShortCodeFind, '/'),
            sprintf(
                $this->convertIframesToShortCodeReplaceFormat,
                GlobalShortCodeService::getShortCodeTagName(IFrameGlobalShortCode::class)
            ),
            true
        );
    }

    /**
     * Prepare find-replace config that can be used to convert iframe elements to iframe short code
     * @since 1.8.0
     */
    private function prepareConvertScriptsToShortCode(): void {
        $convertScriptsToShortCode = $this->bot->getSettingForCheckbox(SettingKey::POST_CONVERT_SCRIPTS_TO_SHORT_CODE);

        // Convert iframes to short code
        if (!$convertScriptsToShortCode) return;

        // Prepare the config
        $this->fr[] = $this->bot->createFindReplaceConfig(
            trim($this->convertScriptsToShortCodeFind, '/'),
            sprintf(
                $this->convertScriptsToShortCodeReplaceFormat,
                GlobalShortCodeService::getShortCodeTagName(ScriptGlobalShortCode::class)
            ),
            true
        );
    }

}
