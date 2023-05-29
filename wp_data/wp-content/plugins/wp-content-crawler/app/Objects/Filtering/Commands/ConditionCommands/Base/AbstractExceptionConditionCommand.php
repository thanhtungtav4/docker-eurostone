<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 18/11/2020
 * Time: 11:04
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base;


use Exception;
use WPCCrawler\Objects\Enums\ValueType;

abstract class AbstractExceptionConditionCommand extends AbstractConditionCommand {

    public function getInputDataTypes(): array {
        return [ValueType::T_EXCEPTION];
    }

    protected function isTestable(): bool {
        return false;
    }

    protected function onDoesApply($subjectValue): bool {
        if (!($subjectValue instanceof Exception)) return false;

        return $this->onCheckException($subjectValue);
    }

    /**
     * @param Exception $e The exception that should be checked
     * @return bool True if the exception satisfies the condition. Otherwise, false.
     * @since 1.11.0
     */
    abstract protected function onCheckException(Exception $e): bool;

}