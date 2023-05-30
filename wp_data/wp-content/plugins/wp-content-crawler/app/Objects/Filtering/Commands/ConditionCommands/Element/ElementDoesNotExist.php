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
class ElementDoesNotExist extends AbstractBotConditionCommand {

    public function getKey(): string {
        return CommandKey::ELEMENT_DOES_NOT_EXIST;
    }

    public function getName(): string {
        return _wpcc('Does not exist');
    }

    public function doesNeedSubjectValue(): bool {
        return false;
    }

    protected function onCheckCondition($node): bool {
        // This command does not need subject values, since we expect to find no subject values. Extract the elements
        // from the crawler by using the defined CSS selectors.
        $result = $this->extractSubjectValues();

        // If the result is an empty array, then the selectors did not match anything and the condition is met.
        return is_array($result) && !$result;
    }

}