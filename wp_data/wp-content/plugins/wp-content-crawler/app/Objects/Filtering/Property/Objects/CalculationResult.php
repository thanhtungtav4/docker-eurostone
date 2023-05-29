<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 06/07/2020
 * Time: 13:32
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Objects;


use WPCCrawler\Objects\Filtering\Property\Base\AbstractProperty;

/**
 * Stores the return values of {@link AbstractProperty} calculations
 *
 * @since 1.11.0
 */
class CalculationResult {

    /** @var mixed New key of the calculated property */
    private $key;

    /** @var mixed Value calculated from the source subject item */
    private $value;

    /**
     * @param mixed $key   See {@link key}
     * @param mixed $value See {@link value}
     * @since 1.11.0
     */
    public function __construct($key, $value) {
        $this->key   = $key;
        $this->value = $value;
    }

    /**
     * @return mixed See {@link key}
     * @since 1.11.0
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @return mixed See {@link value}
     * @since 1.11.0
     */
    public function getValue() {
        return $this->value;
    }

}