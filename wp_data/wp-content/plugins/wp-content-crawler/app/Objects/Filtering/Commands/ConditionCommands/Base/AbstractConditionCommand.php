<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 14/03/2020
 * Time: 14:21
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base;


use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Enums\FilterOptionKey;
use WPCCrawler\Objects\Filtering\Explaining\Loggers\ConditionCommandLogger;

abstract class AbstractConditionCommand extends AbstractBaseCommand {

    /**
     * @var string[]|object[]|null When {@link doesApply()} is called, dot keys of the subjects meeting the condition is
     *      collected in this array. This value is recreated after every {@link doesApply()} call. If
     *      {@link doesApply()} was not called before, this is null. If this command wants to store objects, then this
     *      is an object array.
     */
    private $suitableSubjectItems = null;

    /**
     * @var null|bool Stores the result of {@link doesApply()}. If the condition was not checked before, this is null.
     *      If the condition was checked and met, this is true. Otherwise, this is false.
     */
    private $conditionCheckResult = null;

    /**
     * @var null|ConditionCommandLogger If this {@link isVerbose()}, then this logger will log details about this
     *      command's execution.
     */
    private $logger = null;

    /**
     * Check if the condition applies to the subject by retrieving the options via
     * {@link AbstractBaseCommand::getOptions()}.
     *
     * @param mixed|null $subjectValue
     * @return bool True if the condition applies to the subject. Otherwise, false.
     * @since 1.11.0
     */
    abstract protected function onDoesApply($subjectValue): bool;

    /**
     * @return bool True if this condition applies when only one match is obtained.
     * @since 1.11.0
     */
    protected function isOneMatchEnough(): bool {
        return (bool) $this->getOption(FilterOptionKey::CMD_OPTION_STOP_AFTER_FIRST_MATCH, false);
    }

    /**
     * @return bool True if the condition applies to the subject. Otherwise, false.
     * @since 1.11.0
     */
    public function doesApply(): bool {
        $this->setExecuted(true);
        if ($this->isVerbose()) {
            $this->logger = (new ConditionCommandLogger());
            $this->logger->tick();
        }

        $this->conditionCheckResult = $this->doDoesApply();

        if ($this->logger) $this->logger->tock();

        $this->executionFinished();
        return $this->conditionCheckResult ?: false;
    }

    /**
     * @return string[]|object[]|null See {@link suitableSubjectItems}
     * @since 1.11.0
     */
    public function getSuitableSubjectItems(): ?array {
        return $this->suitableSubjectItems;
    }

    /**
     * @return null|bool See {@link conditionCheckResult}
     * @since 1.11.0
     */
    public function getConditionCheckResult(): ?bool {
        return $this->conditionCheckResult;
    }

    /**
     * @return ConditionCommandLogger|null See {@link logger}
     * @since 1.11.0
     */
    public function getLogger(): ?ConditionCommandLogger {
        return $this->logger;
    }

    /*
     *
     */

    /**
     * @return bool See {@link doesApply()}
     * @since 1.11.0
     */
    protected function doDoesApply(): bool {
        $this->suitableSubjectItems = [];

        // If this command does not need subject values, check the condition with a null subject value.
        if (!$this->doesNeedSubjectValue()) {
            return $this->onDoesApply(null);
        }

        $logger = $this->getLogger();

        $subjectValues = $this->getSubjectValues();
        if (!$subjectValues) {
            if ($logger) $logger
                ->addMessage(_wpcc('Command is not executed because it does not have any subject values that can 
                    be used to check the condition.'));
            $this->setExecuted(false);
            return false;
        }

        $oneMatchEnough = $this->isOneMatchEnough();

        // Check all of the values and collect the matched ones
        $field = $this->getField();
        $doesApply = false;
        foreach($subjectValues as $k => $v) {
            // If this condition does not apply to this value, continue with the next one.
            if (!$this->onDoesApply($v)) {
                // If there is a logger, log this value as a denied subject.
                if ($logger) {

                    if ($field) {
                        $subjectItem = $field->getSubjectItem($k, $v);
                        if ($subjectItem === null) continue;

                        $logger->addDeniedSubjectItem($field->getSubjectItemForHumans($k, $v, $subjectItem));

                    } else {
                        $logger->addDeniedSubjectItem((string) $v);
                    }
                }

                continue;
            }

            // If we reached here, this condition applies.
            if (!$doesApply) {
                $doesApply = true;
            }

            // If there is a field, let it decide what should be used as subject items
            if ($field) {
                $subjectItem = $field->getSubjectItem($k, $v);
                if ($subjectItem === null) continue;

                if ($logger) $logger->addSubjectItem($field->getSubjectItemForHumans($k, $v, $subjectItem));

            } else {
                // Otherwise, use the key, which is probably a dot key.
                $subjectItem = $k;
                if ($logger) $logger->addSubjectItem((string) $v);
            }

            $this->suitableSubjectItems[] = $subjectItem;

            if ($oneMatchEnough) {
                if ($logger) $logger
                    ->addMessage(_wpcc('Execution is stopped after first match, because it is configured that way.'));
                break;
            }
        }

        return $doesApply;
    }

    protected function onTest($subject): ?array {
        $result = $this->onDoesApply($subject)
            ? _wpcc('The condition is met')
            : _wpcc('The condition is not met');

        $this->setExecuted(true)->executionFinished();
        return [$result];
    }

    protected function getFieldLists() {
        $transformable = $this->getDataSource();
        return $transformable === null ? null : $transformable->getConditionCommandFields();
    }
}