<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 18/02/2021
 * Time: 08:38
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Strings;


use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Objects\AttributeValue;

class StringElementNumericAttributeProperty extends StringElementAttributeProperty {

    public function getKey(): string {
        return PropertyKey::STRING_ELEMENT_NUMERIC_ATTR_VALUE;
    }

    public function getName(): string {
        return _wpcc('Element numeric attribute value');
    }

    public function getOutputDataTypes(): array {
        return [ValueType::T_FLOAT];
    }

    protected function getAttributeValue(?Crawler $node, ?string $attr): ?AttributeValue {
        $attrValue = parent::getAttributeValue($node, $attr);
        if ($attrValue === null) return null;

        $value = $attrValue->getValue();
        if ($value === null || !is_numeric($value)) {
            return null;
        }

        return new AttributeValue((float) $value);
    }

}