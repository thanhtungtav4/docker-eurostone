<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/03/2020
 * Time: 11:57
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Counts;


use Countable;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractProperty;
use WPCCrawler\Objects\Filtering\Property\Objects\CalculationResult;

class CountProperty extends AbstractProperty {

    /**
     * @inheritDoc
     */
    public function getKey(): string {
        return PropertyKey::COUNT;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string {
        return _wpcc("Count");
    }

    /**
     * @inheritDoc
     */
    public function doesRequireRawExtractedValues(): bool {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getInputDataTypes(): array {
        return [ValueType::T_COUNTABLE];
    }

    /**
     * @inheritDoc
     */
    public function getOutputDataTypes(): array {
        return [ValueType::T_INTEGER, ValueType::T_NUMERIC];
    }

    /**
     * @inheritDoc
     */
    protected function onCalculate($key, $source, AbstractBaseCommand $cmd) {
        $resultKey = $cmd->getFieldKey() ?: $key;

        // If the source is an array, remove the null and empty values from it.
        if (is_array($source)) {
            /** @var array $source */
            $source = array_filter($source, function($v) {
                if (is_string($v) && $v === '') return false;

                return $v !== null;
            });
        }

        return new CalculationResult(
            $resultKey ? "{$resultKey}.count" : null,
            is_array($source) || ($source instanceof Countable) ? count($source) : 0
        );
    }

}