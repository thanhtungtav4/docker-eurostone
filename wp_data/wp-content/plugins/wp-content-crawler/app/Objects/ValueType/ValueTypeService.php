<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/06/2020
 * Time: 17:33
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\ValueType;


use DateTime;
use Exception;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\ValueType\Definition\Base\AbstractOutputsDef;
use WPCCrawler\Objects\ValueType\Definition\OutputsFloatDef;
use WPCCrawler\Objects\ValueType\Definition\OutputsIntegerDef;
use WPCCrawler\Objects\ValueType\Definition\OutputsNumericDef;
use WPCCrawler\Objects\ValueType\Definition\OutputsStringDef;
use WPCCrawler\Objects\ValueType\Interfaces\Outputs;
use WPCCrawler\Objects\ValueType\Interfaces\OutputsString;

class ValueTypeService {

    /** @var null|ValueTypeService */
    private static $instance = null;

    /**
     * @var string[] An array of the names of {@link AbstractOutputsDef} classes. These classes will be used to cast
     *      values to different types.
     */
    private $outputDefinitionRegistry = null;

    /**
     * @var null|AbstractOutputsDef[] Stores the instances of the output definitions existing in
     *      {@link outputDefinitionRegistry}
     */
    private $outputDefinitions = null;

    /**
     * @var array Key-value pairs structured as [int => {@link AbstractOutputsDef}]. Keys are the return values of
     *      {@link AbstractOutputsDef::getValueType()}. This is used to access an output definition without iterating
     *      over all of the definitions.
     */
    private $typeOutputDefinitionMap = null;

    /**
     * @return ValueTypeService
     * @since 1.11.0
     */
    public static function getInstance(): ValueTypeService {
        if (static::$instance === null) static::$instance = new ValueTypeService();
        return static::$instance;
    }

    /**
     * This is a singleton
     * @since 1.11.0
     */
    protected function __construct() { }

    /**
     * @return string[] See {@link outputDefinitionRegistry}
     * @since 1.11.0
     */
    protected function getOutputDefinitionRegistry(): array {
        if ($this->outputDefinitionRegistry === null) {
            // Register all output definitions here.
            $this->outputDefinitionRegistry = [
                OutputsStringDef::class,
                OutputsNumericDef::class,
                OutputsFloatDef::class,
                OutputsIntegerDef::class,
            ];
        }

        return $this->outputDefinitionRegistry;
    }

    /*
     *
     */

    /**
     * Get the value types that an object can output
     *
     * @param object|Outputs|null $caster An object that implements children of {@link Outputs}
     * @return int[] The value types that the given $caster can output. An array of constants defined in
     *               {@link ValueType}.
     * @since 1.11.0
     */
    public function getOutputTypes(?object $caster): array {
        $types = [];
        if (!$caster) {
            return $types;
        }
        
        foreach($this->getOutputDefinitions() as $outputDef) {
            if (!is_a($caster, $outputDef->getInterface())) continue;
            $types[] = $outputDef->getValueType();
        }

        return $types;
    }

    /**
     * TODO: Make sure this works as expected.
     * Cast a variable to a type
     *
     * @param object|Outputs|null $obj     The object that implements casting interfaces, e.g. {@link OutputsString}
     * @param mixed               $value   The value that will be casted
     * @param int                 $newType The type to which the value will be casted, one of the constants defined in
     *                                     {@link ValueType}
     * @return mixed|null The value casted to the given type if it could be casted. Otherwise, null.
     * @since 1.11.0
     */
    public function castTo(?object $obj, $value, int $newType) {
        if (!$obj) {
            return null;
        }
        
        $map = $this->getTypeOutputDefinitionMap();
        if (!isset($map[$newType])) return null;

        /** @var AbstractOutputsDef $outputDef */
        $outputDef = $map[$newType];
        return $outputDef->cast($obj, $value);
    }

    /**
     * Get {@link ValueType} of a variable
     *
     * @param mixed $var      A value
     * @return int|int[]|null If found, one or more of the constants defined in {@link ValueType}. Otherwise, null.
     *                        This
     *                        returns an array if the type has a parent type. For example, if the type is
     *                        {@link ValueType::T_JSON}, then this returns an array of {@link ValueType::T_JSON} and
     *                        {@link ValueType::T_STRING} because {@link ValueType::T_JSON} extends
     *                        {@link ValueType::T_STRING}.
     * @since 1.11.0
     */
    public function getTypeOf($var) {
        if (is_string($var)) {
            $result = [ValueType::T_STRING];

            // Check if this is a date-time string
            if (preg_match('/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/', $var) === 1) {
                $result[] = ValueType::T_DATE_STR;
            }

            // If this is a numeric string, add the numeric type
            if (is_numeric($var)) $result[] = ValueType::T_NUMERIC;

            // Check if this is a JSON string
            if (Str::startsWith($var, ['{', '[']) && Str::endsWith($var, ['[', '}'])) {
                if (json_decode($var, true) !== null) $result[] = ValueType::T_JSON;
            }

            return sizeof($result) === 1 ? $result[0] : $result;

        } else if (is_int($var)) {
            return [ValueType::T_NUMERIC, ValueType::T_INTEGER];

        } else if (is_float($var)) {
            return [ValueType::T_NUMERIC, ValueType::T_FLOAT];

        } else if (is_array($var)) {
            return ValueType::T_ARRAY;

        } else if (is_bool($var)) {
            return ValueType::T_BOOLEAN;

        } else if (is_object($var)) {
            if (is_a($var, Crawler::class))  return ValueType::T_ELEMENT;
            if (is_a($var, DateTime::class)) return ValueType::T_DATE;
        }

        return null;
    }

    /**
     * @return AbstractOutputsDef[] See {@link outputDefinitions}
     * @since 1.11.0
     */
    public function getOutputDefinitions(): array {
        if ($this->outputDefinitions === null) {
            $this->outputDefinitions = [];
            foreach($this->getOutputDefinitionRegistry() as $cls) {
                try {
                    // Create a new instance
                    $instance = new $cls();

                    // Make sure the instance is of the correct type. If not, continue with the next one.
                    if (!is_a($instance, AbstractOutputsDef::class)) continue;

                    // Add the instance to the collection.
                    $this->outputDefinitions[] = $instance;

                } catch (Exception $e) {
                    // Do nothing.
                }
            }
        }

        return $this->outputDefinitions;
    }

    /**
     * @return array See {@link typeOutputDefinitionMap}
     * @since 1.11.0
     */
    public function getTypeOutputDefinitionMap(): array {
        if ($this->typeOutputDefinitionMap === null) {
            $this->typeOutputDefinitionMap = [];

            foreach($this->getOutputDefinitions() as $outputDef) {
                $this->typeOutputDefinitionMap[$outputDef->getValueType()] = $outputDef;
            }
        }

        return $this->typeOutputDefinitionMap;
    }

}