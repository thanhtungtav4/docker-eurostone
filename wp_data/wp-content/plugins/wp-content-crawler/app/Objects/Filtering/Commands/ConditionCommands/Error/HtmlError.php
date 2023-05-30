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
use InvalidArgumentException;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractExceptionConditionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

class HtmlError extends AbstractExceptionConditionCommand {

    public function getKey(): string {
        return CommandKey::HTML_ERROR;
    }

    public function getName(): string {
        return _wpcc('Is HTML error');
    }

    protected function onCheckException(Exception $e): bool {
        // AbstractBot catches an InvalidArgumentException when there is an HTML error. This is probably not OK to
        // say that the exception is thrown because of an HTML error. But, this is the best one we have currently.
        return $e instanceof InvalidArgumentException;
    }
}