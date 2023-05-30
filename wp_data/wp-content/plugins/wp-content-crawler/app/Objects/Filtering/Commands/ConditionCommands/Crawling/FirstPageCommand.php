<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 15/11/2020
 * Time: 00:44
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Crawling;


use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractCrawlingConditionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

/**
 * Checks if the first page is being crawled/recrawled. If it is the first page, the condition is met.
 *
 * @since 1.11.0
 */
class FirstPageCommand extends AbstractCrawlingConditionCommand {

    public function getKey(): string {
        return CommandKey::FIRST_PAGE;
    }

    public function getName(): string {
        return _wpcc('Is first page');
    }

    protected function onCheckCondition(PostBot $postBot): bool {
        $saverData = $postBot->getPostSaverData();
        return $saverData ? $saverData->isFirstPage() : false;
    }
}