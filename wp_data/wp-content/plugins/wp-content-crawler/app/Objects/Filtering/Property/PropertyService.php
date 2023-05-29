<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/03/2020
 * Time: 11:56
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property;


use Exception;
use WPCCrawler\Exceptions\PropertyNotExistException;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Counts\CountProperty;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractProperty;
use WPCCrawler\Objects\Filtering\Property\Element\ElementAttributeValueProperty;
use WPCCrawler\Objects\Filtering\Property\Element\ElementHtmlProperty;
use WPCCrawler\Objects\Filtering\Property\Element\ElementNumericAttributeValueProperty;
use WPCCrawler\Objects\Filtering\Property\Element\ElementNumericTextProperty;
use WPCCrawler\Objects\Filtering\Property\Element\ElementTagNameProperty;
use WPCCrawler\Objects\Filtering\Property\Element\ElementTextProperty;
use WPCCrawler\Objects\Filtering\Property\Request\HttpStatusCodeProperty;
use WPCCrawler\Objects\Filtering\Property\Request\RequestErrorProperty;
use WPCCrawler\Objects\Filtering\Property\Strings\StringElementAttributeProperty;
use WPCCrawler\Objects\Filtering\Property\Strings\StringCharLengthProperty;
use WPCCrawler\Objects\Filtering\Property\Strings\StringElementNumericAttributeProperty;
use WPCCrawler\Objects\Filtering\Property\Strings\StringNumericValueProperty;
use WPCCrawler\Objects\Filtering\Property\Strings\StringWordLengthProperty;

class PropertyService {

    /** @var PropertyService */
    private static $instance = null;

    /**
     * @var array<string, AbstractProperty> Key-value pairs where keys are property keys and the values are objects
     *      extending to {@link AbstractProperty}
     */
    private $registry = null;

    /**
     * @return PropertyService
     * @since 1.11.0
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new PropertyService();
        }

        return self::$instance;
    }

    /**
     * This is a singleton
     * @since 1.11.0
     */
    protected function __construct() { }

    /*
     * PRIVATE REGISTRY CREATION METHODS
     */

    /**
     * @return class-string[] Names of classes extending to {@link AbstractProperty}
     * @since 1.11.0
     */
    private function createRegistry(): array {
        // Register all properties here
        return [
            StringCharLengthProperty::class,
            StringWordLengthProperty::class,
            StringNumericValueProperty::class,
            StringElementAttributeProperty::class,
            StringElementNumericAttributeProperty::class,

            CountProperty::class,

            ElementAttributeValueProperty::class,
            ElementNumericAttributeValueProperty::class,
            ElementHtmlProperty::class,
            ElementTextProperty::class,
            ElementNumericTextProperty::class,
            ElementTagNameProperty::class,

            RequestErrorProperty::class,
            HttpStatusCodeProperty::class,
        ];
    }

    /*
     * PUBLIC METHODS
     */

    /**
     * Get the instance of a property
     *
     * @param string|null $key   One of the constants defined in {@link PropertyKey}.
     * @param bool        $fresh True if a fresh new instance of the property should be returned. False if the singleton
     *                           instance of the property should be returned.
     * @return AbstractProperty|null If the property exists, its instance is returned. Otherwise, null.
     * @throws PropertyNotExistException If the property does not exist in the registry
     * @since 1.11.0
     */
    public function getProperty(?string $key, bool $fresh = false): ?AbstractProperty {
        if ($key === null) return null;

        $registry = $this->getRegistry();

        if (!isset($registry[$key])) {
            throw new PropertyNotExistException("Property with key '{$key}' does not exist.");
        }

        $singleton = $registry[$key];
        return $fresh
            ? $this->createPropertyInstance(get_class($singleton) ?: null)
            : $singleton;
    }

    /**
     * @return array An array of array representations of the registered properties
     * @since 1.11.0
     */
    public function getPropertiesAsArray(): array {
        $result = [];
        $instances = array_values($this->getRegistry());
        foreach($instances as $instance) {
            /** @var AbstractProperty $instance */
            $result[] = $instance->toArray();
        }

        return $result;
    }

    /**
     * @return array<string, AbstractProperty> See {@link $registry}
     * @since 1.11.0
     */
    public function getRegistry(): array {
        if ($this->registry === null) {
            $classNames = $this->createRegistry();

            $this->registry = [];
            foreach($classNames as $className) {
                $instance = $this->createPropertyInstance($className);
                if (!$instance) continue;

                /** @var AbstractProperty $instance */
                $this->registry[$instance->getKey()] = $instance;
            }
        }

        return $this->registry;
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * Create an instance of a {@link AbstractProperty} with its class name
     *
     * @param class-string|null $className Class name of an {@link AbstractProperty}
     * @return AbstractProperty|null If an instance could be created, the instance. Otherwise, null.
     * @since 1.11.0
     */
    protected function createPropertyInstance(?string $className): ?AbstractProperty {
        if ($className === null) return null;

        try {
            $instance = new $className();

        } catch (Exception $e) {
            return null;
        }

        // Make sure the instance is of correct type
        if (!is_a($instance, AbstractProperty::class)) return null;

        return $instance;
    }
    
}