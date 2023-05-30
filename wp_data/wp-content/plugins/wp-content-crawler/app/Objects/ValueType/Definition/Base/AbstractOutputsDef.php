<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/06/2020
 * Time: 19:42
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\ValueType\Definition\Base;


use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\ValueType\Interfaces\Outputs;

/**
 * Base class for output definitions. An output definition defines which interface should be used to cast a value to a
 * different data type and provides a method to do the casting.
 *
 * @since 1.11.0
 */
abstract class AbstractOutputsDef {

    /**
     * @return int One of the types defined in {@link ValueType}. The returned value defines the data type of the
     *             output of {@link cast()} method.
     * @since 1.11.0
     */
    abstract public function getValueType(): int;

    /**
     * @return class-string<Outputs> Class name of an {@link Outputs}. If a class implements the interface returned by
     *                               this method, it means that that class can cast a value to the data type returned by
     *                               {@link getValueType()}.
     * @since 1.11.0
     */
    abstract public function getInterface(): string;

    /**
     * Cast a value
     *
     * @param object|Outputs $obj   See {@link cast()}
     * @param mixed          $value See {@link cast()}
     * @return mixed|null See {@link cast()}
     * @since 1.11.0
     */
    abstract protected function onCast($obj, $value);

    /**
     * Cast a value
     *
     * @param object|Outputs $obj   An object that implements the interface returned by {@link getInterface()}
     * @param mixed          $value The value that should be casted to the value type returned by {@link
     *                              getValueType()}
     * @return mixed|null The value casted to the type returned by {@link getValueType()}. Returns null if the value
     *                    cannot be casted to the type, the object does not implement the interface or the value is
     *                    null.
     * @since 1.11.0
     */
    public function cast($obj, $value) {
        // If the given caster is not of the correct type, return null.
        if (!is_a($obj, $this->getInterface())) return null;

        // The caster is of the correct type. Cast the value.
        return $this->onCast($obj, $value);
    }
}