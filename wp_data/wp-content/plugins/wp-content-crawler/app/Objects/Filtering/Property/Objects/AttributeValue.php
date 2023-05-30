<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 18/02/2021
 * Time: 11:34
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Objects;


class AttributeValue {

    /** @var string|float|null If found, the attribute value. Otherwise, null. */
    private $value;

    /**
     * @param float|string|null $value See {@link value}
     * @since 1.11.0
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * @return float|string|null See {@link value}
     * @since 1.11.0
     */
    public function getValue() {
        return $this->value;
    }

}