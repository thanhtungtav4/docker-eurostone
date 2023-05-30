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


interface OutputsString extends Outputs {

    /**
     * Cast the value to string
     *
     * @param mixed $newValue The value that should be casted to string
     * @return string|null The $newValue as a string. Returns null if the value cannot be cast to string or the value
     *                     is null.
     * @since 1.11.0
     */
    public function onCastToString($newValue): ?string;

}