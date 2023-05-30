<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 27/05/2020
 * Time: 14:53
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\Date;


use DateTime;
use Exception;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractConditionCommand;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;

abstract class AbstractDateConditionCommand extends AbstractConditionCommand {

    // TODO: Add date action commands as well, so that the user can modify the publish date conditionally.

    /**
     * Check the condition by using the subject date
     *
     * @param DateTime $subjectDate A clone of the subject date. This value can be modified. The modifications will not
     *                              affect the original subject date passed to the condition. So, no need to clone this.
     * @return bool True if the condition is met. Otherwise, false.
     * @since 1.11.0
     */
    abstract protected function onCheckCondition(DateTime $subjectDate): bool;

    public function getInputDataTypes(): array {
        return [ValueType::T_DATE, ValueType::T_DATE_STR];
    }

    protected function onDoesApply($subjectValue): bool {
        // If there is no subject value, we cannot check the condition.
        if ($subjectValue === null) return false;

        // Get the subject date as a clone, because we do not want to modify the original date just to check the
        // condition. If the subject value is the date of the post, then it would be modified if we changed it here. We
        // do not want that.
        $subjectDate = $this->getSubjectDate($subjectValue, true);
        if ($subjectDate === null) return false;

        return $this->onCheckCondition($subjectDate);
    }

    /*
     *
     */

    /**
     * Get the subject value as a {@link DateTime} object
     *
     * @param DateTime|string|null|mixed $subjectValue The subject value of the condition
     * @param bool                       $clone        True if a clone of the resultant {@link DateTime} object should
     *                                                 be returned. If you do not want to modify the original
     *                                                 {@link DateTime} object when the $subjectValue is a
     *                                                 {@link DateTime} object, you might want to work on a clone.
     * @return DateTime|null See {@link parseDate()}
     * @since 1.11.0
     */
    protected function getSubjectDate($subjectValue, bool $clone = false): ?DateTime {
        $date = $this->parseDate($subjectValue);

        // Clone the date if there is a date and it should be cloned
        if ($date && $clone) $date = clone $date;

        return $date;
    }

    /**
     * Get {@link getCurrentTimeString()} as a {@link DateTime} object
     *
     * @return DateTime|null See {@link parseDate()}
     * @since 1.11.0
     */
    protected function getCurrentDate(): ?DateTime {
        return $this->parseDate($this->getCurrentTimeString());
    }

    /**
     * Create a {@link DateTime} object from a date by handling the exception thrown due to invalid date format. See
     * {@link handleDateTimeException()} for details.
     *
     * @param DateTime|string|null|mixed $date The date that should be parsed to a {@link DateTime} object
     * @return DateTime|null If the date could be parsed, a {@link DateTime} object. If the given date is a
     *                       {@link DateTime} object, it will be returned directly. Otherwise, null.
     * @since 1.11.0
     */
    protected function parseDate($date): ?DateTime {
        if ($date === null) return null;

        // If the date value is a DateTime object, return it directly.
        if ($date instanceof DateTime) return $date;

        // If the given date is not a string, return null.
        if (!is_string($date)) return null;

        // Make sure the date does not have whitespace in the beginning or end.
        $date = trim($date);

        // If the date is an empty string, return null. We do not allow that.
        if ($date === '') return null;

        try {
            // Try to parse the date
            return new DateTime($date);

        } catch (Exception $e) {
            // The date could not be parsed. Notify the user.
            $this->handleDateTimeException($e, (string) $date);
        }

        return null;
    }

    /**
     * Notify the user about the parsing error.
     *
     * @param Exception   $e              The exception caused by parsing a string to {@link DateTime}
     * @param string|null $invalidDateStr The invalid date string that caused the exception
     * @since 1.11.0
     */
    protected function handleDateTimeException(Exception $e, ?string $invalidDateStr): void {
        $message = sprintf(
            _wpcc('Date "%1$s" could not be parsed in "%2$s" command.'),
            $invalidDateStr ?: '',
            $this->getName()
        );

        $logger = $this->getLogger();
        if ($logger) $logger->addMessage($message);

        Informer::add((new Information($message, $e->getMessage(), InformationType::INFO))
            ->setException($e)->addAsLog());
    }

    /**
     * @return string WP's current time string in a format that can be parsed by {@link DateTime::__construct()}
     * @since 1.11.0
     */
    protected function getCurrentTimeString(): string {
        return (string) current_time('mysql');
    }

}