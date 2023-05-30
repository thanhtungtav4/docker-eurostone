<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/12/2018
 * Time: 18:16
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\OptionsBox\Boxes\Base;


abstract class BaseOptionsBoxApplier {

    /** @var BaseOptionsBoxData */
    private $data;

    /** @var bool True if the applier must run for testing purposes. Otherwise, false. */
    private $isForTest = false;

    /** @var bool True if the applier will run for a test conducted from within the options box. Otherwise, false. */
    private $isFromOptionsBox = false;

    /**
     * @param BaseOptionsBoxData $optionsBoxData
     */
    public function __construct(BaseOptionsBoxData $optionsBoxData) {
        $this->data = $optionsBoxData;
    }

    /**
     * Apply the options configured in options box to the given value
     * @param mixed $value
     * @return mixed|null $modifiedValue Null, if the item should be removed. Otherwise, the modified value.
     * @since 1.11.0
     */
    abstract protected function onApply($value);

    /**
     * Applies the options configured in options box to the given value
     * @param mixed $value
     * @return mixed|null Null, if the item should be removed. Otherwise, the modified value.
     */
    public function apply($value) {
        // If the box should not be applied to the value, return the value without changing it.
        if (!$this->shouldApply($value)) {
            return $value;
        }

        return $this->onApply($value);
    }

    /**
     * @param array $arr        An array of items.
     * @param null|string $key  If given, the options will be applied to $arr[$key]. If no key is given, the array is
     *                          assumed to be flat, containing only non-array values.
     * @return array            Modified array.
     */
    public function applyToArray($arr, $key = null) {
        // If there is no data, return the original array since there is no setting to apply.
        if (!$this->dataExists()) return $arr;

        // If the parameter is not an array, make it an array.
        if (!is_array($arr)) $arr = [$arr];

        $arr = array_map(function($v) use (&$key) {
            // Apply only if the item is not an array.
            if (!is_array($v)) {
                return $this->apply($v);

                // Apply to the given key, if there is a key.
            } else if($key && isset($v[$key])) {
                $v[$key] = $this->apply($v[$key]);
                return $v;
            }

            return null;
        }, $arr);

        // Make sure null values are removed. apply method returns null only if the item should be removed.
        return array_filter($arr, function($v) {
            return $v !== null;
        });
    }

    /**
     * @return BaseOptionsBoxData
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return bool True if the data exists.
     * @since 1.8.0
     */
    public function dataExists(): bool {
        return true;
    }

    /**
     * @return bool
     */
    public function isForTest() {
        return $this->isForTest;
    }

    /**
     * @param bool $isForTest See {@link $isForTest}
     * @return BaseOptionsBoxApplier
     */
    public function setForTest($isForTest) {
        $this->isForTest = $isForTest;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFromOptionsBox() {
        return $this->isFromOptionsBox;
    }

    /**
     * @param bool $isFromOptionsBox See {@link $isFromOptionsBox}
     * @return BaseOptionsBoxApplier
     */
    public function setFromOptionsBox($isFromOptionsBox) {
        $this->isFromOptionsBox = $isFromOptionsBox;
        return $this;
    }

    /**
     * Check if the options box should be applied to the given value.
     *
     * @param mixed|null $value The value that will be checked if the options box should be applied to it
     * @return bool True if the box should be applied to the given value. False if it should not be applied.
     * @since 1.11.0
     * @noinspection PhpUnusedParameterInspection
     */
    public function shouldApply($value): bool {
        return true;
    }

}