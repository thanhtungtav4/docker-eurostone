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
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\Date\AbstractRelativeDateConditionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

class DateIsNewerThanRelative extends AbstractRelativeDateConditionCommand {

    public function getKey(): string {
        return CommandKey::DATE_IS_NEWER_THAN_RELATIVE;
    }

    public function getName(): string {
        return _wpcc('Is newer than');
    }

    protected function getInputDescription(): string {
        return _wpcc('The subject date must be newer than this many hours when compared to the current time.');
    }

    protected function getConditionFormulaForHumans(): string {
        return sprintf('<b>(%1$s - %2$s < %3$s)</b>',
            _wpcc('now'),
            _wpcc('the subject date'),
            _wpcc('hours')
        );
    }

    protected function onCheckDateCondition(DateTime $subjectDate, DateTime $now, int $seconds): bool {
        // Check the condition:
        // now - date < seconds  =>  now < date + seconds
        $subjectDate->modify("{$seconds} second");
        return $now < $subjectDate;
    }

}