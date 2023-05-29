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


interface OutputsInteger extends Outputs {

    /**
     * Cast the value to integer
     *
     * @param mixed $newValue The value that should be casted to integer
     * @return int|null The $newValue as an integer. Returns null if the value cannot be cast to integer or the value
     *                  is null.
     * @since 1.11.0
     */
    public function onCastToInteger($newValue): ?int;

}