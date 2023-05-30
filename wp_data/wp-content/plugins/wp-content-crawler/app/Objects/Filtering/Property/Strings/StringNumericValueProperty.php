<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/03/2020
 * Time: 11:57
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Strings;


use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractProperty;
use WPCCrawler\Objects\Filtering\Property\Objects\CalculationResult;

class StringNumericValueProperty extends AbstractProperty {

    public function getKey(): string {
        return PropertyKey::NUMERIC_VALUE;
    }

    public function getName(): string {
        return _wpcc("Numeric value");
    }

    public function getInputDataTypes(): array {
        return [ValueType::T_STRING];
    }

    public function getOutputDataTypes(): array {
        return [ValueType::T_NUMERIC];
    }

    protected function onCalculate($key, $source, AbstractBaseCommand $cmd): CalculationResult {
        return new CalculationResult($key, is_numeric($source) ? (float) $source : null);
    }

}