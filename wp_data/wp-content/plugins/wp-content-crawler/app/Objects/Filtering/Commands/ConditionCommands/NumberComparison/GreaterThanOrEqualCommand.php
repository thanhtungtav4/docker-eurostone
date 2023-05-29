<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 14/03/2020
 * Time: 15:22
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\NumberComparison;


use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractNumericConditionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

class GreaterThanOrEqualCommand extends AbstractNumericConditionCommand {

    public function getKey(): string {
        return CommandKey::GREATER_THAN_OR_EQUAL;
    }

    public function getName(): string {
        return _wpcc("Greater than or equal to");
    }

    protected function onCheckCondition(float $subjectValue, float $optionValue): bool {
        return $subjectValue >= $optionValue;
    }

}