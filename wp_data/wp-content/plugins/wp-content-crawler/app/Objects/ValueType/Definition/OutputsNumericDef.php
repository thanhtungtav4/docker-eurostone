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
use WPCCrawler\Objects\ValueType\Interfaces\OutputsNumeric;

class OutputsNumericDef extends AbstractOutputsDef {

    public function getValueType(): int {
        return ValueType::T_NUMERIC;
    }

    public function getInterface(): string {
        return OutputsNumeric::class;
    }

    protected function onCast($obj, $value) {
        /** @var OutputsNumeric $obj */
        return $obj->onCastToNumeric($value);
    }

}