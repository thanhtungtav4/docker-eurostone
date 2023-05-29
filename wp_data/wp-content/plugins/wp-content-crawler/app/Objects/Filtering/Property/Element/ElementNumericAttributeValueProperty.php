<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 12/04/2020
 * Time: 09:06
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Element;


use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Objects\CalculationResult;

class ElementNumericAttributeValueProperty extends ElementAttributeValueProperty {

    public function getKey(): string {
        return PropertyKey::ELEMENT_NUMERIC_ATTR_VALUE;
    }

    public function getName(): string {
        return _wpcc('Numeric attribute value');
    }

    public function getOutputDataTypes(): array {
        return [ValueType::T_NUMERIC];
    }

    protected function onCalculate($key, $source, AbstractBaseCommand $cmd): ?CalculationResult {
        $result = parent::onCalculate($key, $source, $cmd);
        if (!($result instanceof CalculationResult)) {
            return null;
        }

        $value = $result->getValue();
        return new CalculationResult($key, is_numeric($value)
            ? (float) $value
            : null
        );
    }

}