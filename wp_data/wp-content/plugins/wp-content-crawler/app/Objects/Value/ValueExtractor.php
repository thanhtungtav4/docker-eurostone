<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 14/12/2018
 * Time: 10:07
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\Value;


use WPCCrawler\Exceptions\MethodNotExistException;
use WPCCrawler\Objects\Transformation\Objects\TransformableFieldList;
use WPCCrawler\Utils;

class ValueExtractor {

    const DEFAULT_SEPARATOR = '.';

    /** @var string */
    private $separator = self::DEFAULT_SEPARATOR;

    /** @var ValueExtractorOptions */
    private $options;

    /**
     * Fills the given map with the data extracted from given $value. The resultant array is a flat array where
     * keys are dot notation keys and the values are their values retrieved from the given $value.
     *
     * @param mixed  $value     The value from which the data will be extracted to fill the given map.
     * @param array  $map       An associative array showing the fields from which the data will be extracted. The
     *                          fields must be specified in dot notation. For example, "post.title" will extract the
     *                          value of the title field that exists in the value of post field. Here, the given object
     *                          has a "post" value and "getPost" method that returns an object having a "title" field
     *                          and "getTitle" method. This map must be associative. In other words, the dot notations
     *                          must be provided as keys of the given array.
     * @param string $separator Separator used in the dot notation.
     * @param null|ValueExtractorOptions $options Options that will be used by the extractor. When this is null, the
     *                                            defaults will be used. See {@link ValueExtractorOptions} for defaults.
     * @return array|null       Flattened array where keys are dot keys and the values are their corresponding values
     *                          extracted from $value. If $map evaluates to false, returns null.
     * @throws MethodNotExistException See {@link getForObject()}
     * @since 1.8.0
     * @since 1.11.0 Add $options parameter
     */
    public function fillAndFlatten($value, array $map, string $separator = '.', ?ValueExtractorOptions $options = null): ?array {
        $this->separator = $separator;
        $this->options = $options ?: new ValueExtractorOptions();

        // If there is no map, return null.
        if (!$map) return null;

        // Prepare the map such that the keys have the given separator instead of dot key.
        $map = $this->prepareMap($map);

        // Now, we will fill the values of the prepared map by getting them from the given $value.
        $results = [];
        foreach($map as $dotKey => $val) {
            $dotKey = (string) $dotKey;

            // There must be a dot key.
            if (!$this->isKeyValid($dotKey)) continue;

            // Extract the values from the given $value for this dot key
            $res = $this->getResult($value, $dotKey);

            // If there is no result, continue with the next one.
            if (!$res) continue;

            // Add the results
            $results = array_merge($results, $res);
        }

        return $results;
    }

    /*
     * MAP PREPARER METHOD
     */

    /**
     * Changes the dots in the given dot keys into the value specified with {@link separator}. For example, if the
     * separator is "|", then this array ['item1.item2' => 'Val 1', 'item1.item3.item4' => 'Val 2'] becomes
     * ['item1|item2' => 'Val 1', 'item1|item3|item4' => 'Val 2']
     *
     * @param null|array $map The map to be prepared, such as {@link TransformableFieldList::toAssociativeArray()}
     *                        retrieved from {@link Transformable::getTransformableFields()}
     * @return array Prepared map or null.
     * @since 1.8.0
     */
    private function prepareMap($map): array {
        $prepared = [];
        if ($map === null) return $prepared;

        foreach($map as $dotKey => $description) {
            $prepared[str_replace('.', $this->separator, $dotKey)] = $description;
        }

        return $prepared;
    }

    /*
     * MAP FILLER METHODS
     */

    /**
     * Extract data from a value by using dot notation.
     *
     * @param mixed       $value        Value of dot key will be extracted from this.
     * @param string      $dotKey       A dot notation that will be used to extract the data from $value.
     * @param null|string $parentKey    Parent dot key for $dotKey, if exists. When flattening, the found item will be
     *                                  added under the parent key.
     * @return array|null Flattened array where keys are dot keys and the values are their corresponding values.
     * @since 1.8.0
     * @throws MethodNotExistException See {@link getForObject()}
     */
    private function getResult($value, $dotKey, $parentKey = null): ?array {
        // If the value is an object
        if (is_object($value)) {
            $res = $this->getForObject($value, $dotKey, $parentKey);

        // If it is an array
        } else if (is_array($value)) {
            $res = $this->getForArray($value, $dotKey, $parentKey);

        // Otherwise
        } else {
            $remainingDotKey = $dotKey;
            $firstKey = $this->shiftFirstKey($remainingDotKey);

            $res = $this->getForString($value, $firstKey, $parentKey);
        }

        return $res;
    }

    /**
     * Extract data from an object by using dot notation.
     *
     * @param object      $object       Value of dot key will be extracted from this.
     * @param string      $dotKey       A dot notation that will be used to extract the data from $value.
     * @param null|string $parentKey    Parent dot key for $dotKey, if exists. When flattening, the found item will be
     *                                  added under the parent key.
     * @return array|null Flattened array where keys are dot keys and the values are their corresponding values.
     * @since 1.8.0
     * @throws MethodNotExistException If a getter method does not exist in the given object's class.
     */
    private function getForObject($object, $dotKey, $parentKey = null): ?array {
        $res = $this->maybeCreateResult($object, $dotKey, $parentKey, $this->options->isAllowObjects());
        if ($res !== false) return $res;

        // Get first key in the dot notation and the remaining keys (the dot key that does not include the first key)
        $remainingDotKey = $dotKey;
        $firstKey = $this->shiftFirstKey($remainingDotKey);

        // If the firstKey cannot be found, we cannot create the getter method name.
        if ($firstKey === null) {
            throw new MethodNotExistException(sprintf('Method name could not be generated for %1$s', get_class($object)));
        }

        // To get the value, we need the getter method's name.
        $getterMethodName = $this->getGetterMethodName($firstKey);

        // If the method does not exist in the object, throw an exception.
        if(!method_exists($object, $getterMethodName)) {
            throw new MethodNotExistException(sprintf(_wpcc('%1$s method does not exist in %2$s'), $getterMethodName, get_class($object)));
        }

        // Get the value by calling the getter
        $value = $object->$getterMethodName();

        // Prepare the item's parent key
        $parentKey = $this->isKeyValid($parentKey) ? $parentKey . $this->separator . $firstKey : $firstKey;

        return $this->getResult($value, $remainingDotKey, $parentKey);
    }

    /**
     * Extract data from an array by using dot notation.
     *
     * @param array       $arr          Value of dot key will be extracted from this.
     * @param string      $dotKey       A dot notation that will be used to extract the data from $value.
     * @param null|string $parentKey    Parent dot key for $dotKey, if exists. When flattening, the found item will be
     *                                  added under the parent key.
     * @return array|null Flattened array where keys are dot keys and the values are their corresponding values.
     * @since 1.8.0
     * @throws MethodNotExistException See {@link getForObject()}
     */
    private function getForArray($arr, $dotKey, $parentKey = null): ?array {
        // If this is the last key and the arrays are allowed to be directly included
        if (!$this->isKeyValid($dotKey) && $this->options->isAllowArrays()) {
            // Get the combined key
            $combinedDotKey = $this->createCombinedDotKey($parentKey, $dotKey);

            // If the combined key is valid, return an array that contains the array directly.
            if ($this->isKeyValid($combinedDotKey)) {
                return [$combinedDotKey => $arr];
            }
        }

        // Get first key in the dot notation and the remaining keys (the dot key that does not include the first key)
        $remainingDotKey = $dotKey;
        $firstKey = $this->shiftFirstKey($remainingDotKey);

        // If there is a dot key and the array is an associative array
        if ($this->isKeyValid($dotKey) && isset($arr[$firstKey])) {
            $parentKey = $parentKey ? $parentKey . $this->separator . $firstKey : $firstKey;

            // Prepare the value at $firstKey index of the array
            return $this->getResult($arr[$firstKey], $remainingDotKey, $parentKey);

        } else {
            $results = [];

            // Array does not have keys. It is a sequential array.
            foreach($arr as $i => $value) {
                // Prepare it by adding the index to the parent key
                $res = $this->getResult($value, $dotKey, $parentKey . $this->separator . $i);
                if (!$res) continue;

                // Collect the results
                $results = array_merge($results, $res);
            }

            return $results;
        }
    }

    /**
     * Extract data from a value that is not an array or an object by using dot notation.
     *
     * @param string|float|null $value     Value of dot key will be extracted from this.
     * @param string|null       $dotKey    A dot notation that will be used to extract the data from $value.
     * @param string|null       $parentKey Parent dot key for $dotKey, if exists. When flattening, the found item will
     *                                     be added under the parent key.
     * @return array|null Flattened array where keys are dot keys and the values are their corresponding values.
     * @since 1.8.0
     */
    private function getForString($value, ?string $dotKey, ?string $parentKey): ?array {
        // Merge the parent key and the dot key to create a combined dot key
        $key = $this->createCombinedDotKey($parentKey, $dotKey);

        // If the key does not exist, return null.
        if (!$this->isKeyValid($key)) return null;

        // If the value should be validated
        if (!$this->options->isAllowAll()) {
            // If the value is numeric, empty or null, return null.
            if (!$this->options->isAllowNumeric()       && is_numeric($value))  return null;
            if (!$this->options->isAllowEmptyString()   && $value === '')       return null;
            if (!$this->options->isAllowNull()          && $value === null)     return null;
        }

        // Create a 1-item associative array with the key and the value.
        return [$key => $value];
    }

    /*
     * OTHER HELPERS
     */

    /**
     * Shifts the first key from a dot key
     *
     * @param string|null $dotKey See {@link Utils::shiftFirstKey()}
     * @return string|null First key. See {@link Utils::shiftFirstKey()}.
     * @uses Utils::shiftFirstKey()
     * @since 1.11.1
     */
    private function shiftFirstKey(&$dotKey): ?string {
        return Utils::shiftFirstKey($dotKey, $this->separator);
    }

    /**
     * Get getter method name of an object's field
     *
     * @param string $fieldName Field name of an object.
     * @return string Name of the getter method that should return the value of given $fieldName
     * @since 1.8.0
     */
    private function getGetterMethodName(string $fieldName): string {
        return "get" . ucfirst($fieldName);
    }

    /**
     * Merge the parent key and the dot key to create a combined dot key
     *
     * @param string|null $parentKey
     * @param string|null $dotKey
     * @return string Combined dot key
     * @since 1.11.0
     */
    private function createCombinedDotKey(?string $parentKey, ?string $dotKey): string {
        return implode($this->separator, array_filter([$parentKey, $dotKey], function($v) {
            return $this->isKeyValid($v);
        }));
    }

    /**
     * @param string|null $dotKey
     * @return bool True if the given dot key is a non-empty string
     * @since 1.11.0
     */
    private function isKeyValid(?string $dotKey): bool {
        return $dotKey !== null && $dotKey !== '';
    }

    /**
     * @param mixed       $value          The value that might be added as a result
     * @param string|null $dotKey         The dot key
     * @param string|null $parentKey      The parent key of the dot key
     * @param bool        $isValueAllowed True if the value can be added as a result, in case the dot key is not valid
     * @return false|array|null If the result should not be created, returns false. Otherwise, returns null or the result
     *                          as an array.
     * @since 1.11.0
     */
    private function maybeCreateResult($value, $dotKey, $parentKey, bool $isValueAllowed) {
        // If we did not reach the end of the dot key, return false.
        if ($this->isKeyValid($dotKey)) return false;

        // If the value is not allowed, return null.
        if (!$isValueAllowed) return null;

        // Create the final key
        $combinedDotKey = $this->createCombinedDotKey($parentKey, $dotKey);

        // If the combined key is valid, return an array. Otherwise, return null.
        return $this->isKeyValid($combinedDotKey)
            ? [$combinedDotKey => $value]
            : null;
    }

}