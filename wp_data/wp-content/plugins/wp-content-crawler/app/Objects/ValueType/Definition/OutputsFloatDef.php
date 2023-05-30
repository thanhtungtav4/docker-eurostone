<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/06/2020
 * Time: 19:41
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\ValueType\Definition;


use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\ValueType\Definition\Base\AbstractOutputsDef;
use WPCCrawler\Objects\ValueType\Interfaces\OutputsFloat;

class OutputsFloatDef extends AbstractOutputsDef {

    public function getValueType(): int {
        return ValueType::T_FLOAT;
    }

    public function getInterface(): string {
        return OutputsFloat::class;
    }

    protected function onCast($obj, $value): ?float {
        /** @var OutputsFloat $obj */
        return $obj->onCastToFloat($value);
    }

}