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


use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractProperty;
use WPCCrawler\Objects\Filtering\Property\Objects\CalculationResult;

class ElementTextProperty extends AbstractProperty {

    public function getKey(): string {
        return PropertyKey::ELEMENT_TEXT;
    }

    public function getName(): string {
        return _wpcc('Text');
    }

    public function getInputDataTypes(): array {
        return [ValueType::T_ELEMENT];
    }

    public function getOutputDataTypes(): array {
        return [ValueType::T_STRING];
    }

    protected function onCalculate($key, $source, AbstractBaseCommand $cmd): ?CalculationResult {
        if (!($source instanceof Crawler)) return null;

        return new CalculationResult($key, $source->text());
    }

}