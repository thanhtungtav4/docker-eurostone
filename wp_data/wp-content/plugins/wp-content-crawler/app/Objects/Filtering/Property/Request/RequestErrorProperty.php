<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 18/11/2020
 * Time: 10:36
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Request;


use Exception;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractProperty;
use WPCCrawler\Objects\Filtering\Property\Objects\CalculationResult;

class RequestErrorProperty extends AbstractProperty {

    public function getKey(): string {
        return PropertyKey::REQUEST_ERROR;
    }

    public function getName(): string {
        return _wpcc('Error');
    }

    public function getInputDataTypes(): array {
        return [ValueType::T_REQUEST];
    }

    public function getOutputDataTypes(): array {
        return [ValueType::T_EXCEPTION];
    }

    protected function onCalculate($key, $source, AbstractBaseCommand $cmd): ?CalculationResult {
        if (!($source instanceof Exception)) return null;

        return new CalculationResult($key, $source);
    }
}