<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 27/05/2020
 * Time: 13:28
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Date;


use DateTime;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\Date\AbstractFixedDateConditionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

class DateIsNewerThanFixed extends AbstractFixedDateConditionCommand {

    public function getKey(): string {
        return CommandKey::DATE_IS_NEWER_THAN_FIXED;
    }

    public function getName(): string {
        return _wpcc('Is newer than fixed date');
    }

    protected function getInputDescription(): string {
        return _wpcc('The subject date must be newer than this date.');
    }

    protected function getConditionFormulaForHumans(): string {
        return sprintf('<b>(%1$s > %2$s)</b>',
            _wpcc('the subject date'),
            _wpcc('date given by you')
        );
    }

    protected function onCheckDateCondition(DateTime $subjectDate, DateTime $givenDate): bool {
        return $subjectDate > $givenDate;
    }

}