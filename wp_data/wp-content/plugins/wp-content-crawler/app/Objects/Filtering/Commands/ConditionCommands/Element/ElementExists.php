<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/04/2020
 * Time: 09:21
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Element;


use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractBotConditionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

/**
 * Extracts the elements by using the CSS selectors defined in the settings of this command.
 *
 * @since 1.11.0
 */
class ElementExists extends AbstractBotConditionCommand {

    public function getKey(): string {
        return CommandKey::ELEMENT_EXISTS;
    }

    public function getName(): string {
        return _wpcc('Exists');
    }

    protected function onCheckCondition($node): bool {
        // If the node is not null, it means it exists.
        return $node !== null;
    }

}