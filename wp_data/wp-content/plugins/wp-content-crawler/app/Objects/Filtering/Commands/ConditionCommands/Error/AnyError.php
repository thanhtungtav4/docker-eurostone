<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 18/11/2020
 * Time: 11:07
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Error;


use Exception;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractExceptionConditionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

class AnyError extends AbstractExceptionConditionCommand {

    public function getKey(): string {
        return CommandKey::ERROR;
    }

    public function getName(): string {
        return _wpcc('Is error');
    }

    protected function onCheckException(Exception $e): bool {
        // This condition is met if there is any exception. Since this method is only called when there is an exception,
        // this condition is already met at this point of execution. So, return true.
        return true;
    }
}