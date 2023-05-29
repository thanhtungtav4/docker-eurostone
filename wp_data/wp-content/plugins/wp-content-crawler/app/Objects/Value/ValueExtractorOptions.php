<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/03/2020
 * Time: 07:04
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Value;


class ValueExtractorOptions {

    /** @var bool True if all allow options should be considered as true. Defaults to false. */
    private $allowAll = false;

    /** @var bool True if the numeric values should be allowed. Defaults to false. */
    private $allowNumeric = false;

    /** @var bool True if the null values should be allowed. Defaults to false. */
    private $allowNull = false;

    /** @var bool True if the empty string values should be allowed. Defaults to false. */
    private $allowEmptyString = false;

    /** @var bool True if the results can contain objects. Defaults to false. */
    private $allowObjects = false;

    /** @var bool True if the results can contain arrays. Defaults to false. */
    private $allowArrays = false;

    /**
     * @return bool See {@link $allowAll}
     * @since 1.11.0
     */
    public function isAllowAll(): bool {
        return $this->allowAll;
    }

    /**
     * @param bool $allowAll See {@link $allowAll}
     * @return $this
     * @since 1.11.0
     */
    public function setAllowAll(bool $allowAll) {
        $this->allowAll = $allowAll;
        return $this;
    }

    /**
     * @return bool See {@link $allowNumeric}
     * @since 1.11.0
     */
    public function isAllowNumeric(): bool {
        return $this->allowNumeric || $this->allowAll;
    }

    /**
     * @param bool $allowNumeric See {@link $allowNumeric}
     * @return $this
     * @since 1.11.0
     */
    public function setAllowNumeric(bool $allowNumeric) {
        $this->allowNumeric = $allowNumeric;
        return $this;
    }

    /**
     * @return bool See {@link $allowNull}
     * @since 1.11.0
     */
    public function isAllowNull(): bool {
        return $this->allowNull || $this->allowAll;
    }

    /**
     * @param bool $allowNull See {@link $allowNull}
     * @return $this
     * @since 1.11.0
     */
    public function setAllowNull(bool $allowNull) {
        $this->allowNull = $allowNull;
        return $this;
    }

    /**
     * @return bool See {@link $allowEmptyString}
     * @since 1.11.0
     */
    public function isAllowEmptyString(): bool {
        return $this->allowEmptyString || $this->allowAll;
    }

    /**
     * @param bool $allowEmptyString See {@link $allowEmptyString}
     * @return $this
     * @since 1.11.0
     */
    public function setAllowEmptyString(bool $allowEmptyString) {
        $this->allowEmptyString = $allowEmptyString;
        return $this;
    }

    /**
     * @return bool See {@link $allowObjects}
     * @since 1.11.0
     */
    public function isAllowObjects(): bool {
        return $this->allowObjects || $this->allowAll;
    }

    /**
     * @param bool $allowObjects See {@link $allowObjects}
     * @return $this
     * @since 1.11.0
     */
    public function setAllowObjects(bool $allowObjects) {
        $this->allowObjects = $allowObjects;
        return $this;
    }

    /**
     * @return bool See {@link $allowArrays}
     * @since 1.11.0
     */
    public function isAllowArrays(): bool {
        return $this->allowArrays || $this->allowAll;
    }

    /**
     * @param bool $allowArrays See {@link $allowArrays}
     * @return $this
     * @since 1.11.0
     */
    public function setAllowArrays(bool $allowArrays) {
        $this->allowArrays = $allowArrays;
        return $this;
    }

}