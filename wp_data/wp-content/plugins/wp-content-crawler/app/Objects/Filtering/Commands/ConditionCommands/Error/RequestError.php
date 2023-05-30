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
use GuzzleHttp\Exception\TransferException;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractExceptionConditionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

class RequestError extends AbstractExceptionConditionCommand {

    public function getKey(): string {
        return CommandKey::REQUEST_ERROR;
    }

    public function getName(): string {
        return _wpcc('Is request error');
    }

    protected function onCheckException(Exception $e): bool {
        return $e instanceof TransferException;
    }
}