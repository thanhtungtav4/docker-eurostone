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


interface OutputsFloat extends Outputs {

    /**
     * Cast the value to float
     *
     * @param mixed $newValue The value that should be casted to float
     * @return float|null The $newValue as a float. Returns null if the value cannot be cast to float or the value is
     *                    null.
     * @since 1.11.0
     */
    public function onCastToFloat($newValue): ?float;

}