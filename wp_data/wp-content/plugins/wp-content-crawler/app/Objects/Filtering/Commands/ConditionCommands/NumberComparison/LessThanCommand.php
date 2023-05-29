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

class LessThanCommand extends AbstractNumericConditionCommand {

    public function getKey(): string {
        return CommandKey::LESS_THAN;
    }

    public function getName(): string {
        return _wpcc("Less than");
    }

    protected function onCheckCondition(float $subjectValue, float $optionValue): bool {
        return $subjectValue < $optionValue;
    }

}