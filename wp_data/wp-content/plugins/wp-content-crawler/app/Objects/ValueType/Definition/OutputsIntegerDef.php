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
use WPCCrawler\Objects\ValueType\Interfaces\OutputsInteger;

class OutputsIntegerDef extends AbstractOutputsDef {

    public function getValueType(): int {
        return ValueType::T_INTEGER;
    }

    public function getInterface(): string {
        return OutputsInteger::class;
    }

    protected function onCast($obj, $value): ?int {
        /** @var OutputsInteger $obj */
        return $obj->onCastToInteger($value);
    }

}