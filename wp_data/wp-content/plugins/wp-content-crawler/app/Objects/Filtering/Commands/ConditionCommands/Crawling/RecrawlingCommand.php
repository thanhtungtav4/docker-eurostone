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
 * Checks if a recrawling operation is currently being run. If so, the condition is met.
 *
 * @since 1.11.0
 */
class RecrawlingCommand extends AbstractCrawlingConditionCommand {

    public function getKey(): string {
        return CommandKey::RECRAWLING;
    }

    public function getName(): string {
        return _wpcc('Is recrawling');
    }

    protected function onCheckCondition(PostBot $postBot): bool {
        $saverData = $postBot->getPostSaverData();
        return $saverData ? $saverData->isRecrawl() : false;
    }
}