<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/06/2020
 * Time: 11:04
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\ValueType\Interfaces;


interface OutputsNumeric extends Outputs {

    /**
     * Cast the value to a numeric string, float or integer
     *
     * @param mixed $newValue The value that should be casted to a numeric value
     * @return string|null The $newValue as a numeric value. Returns null if the value cannot be casted to a numeric
     *                     value or the value is null.
     * @since 1.11.0
     */
    public function onCastToNumeric($newValue): ?string;

}