<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 01/11/2018
 * Time: 17:01
 */

namespace WPCCrawler\Objects\OptionsBox\Boxes\Def\Options;


use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxOptions;
use WPCCrawler\Utils;

class DefaultOptionsBoxCalculationOptions extends BaseOptionsBoxOptions {

    /** @var string Decimal separator that will be used for the output */
    private $decimalSeparatorAfter;

    /** @var bool True if the result should use thousands separator. Otherwise, false. */
    private $isUseThousandsSeparator;

    /** @var bool True if the item should be removed when its value is not numeric. Otherwise, false. */
    private $isRemoveIfNotNumeric;

    /** @var int Number of digits coming after the decimal separator */
    private $precision;

    /** @var array Array of strings. Each string is a formula. */
    private $formulas;

    /**
     * Prepares the instance variables using the raw data
     */
    protected function prepare(): void {
        // Prepare decimal separator
        $rawData = $this->getRawData();
        $this->decimalSeparatorAfter = $this->getDecimalSeparator(Utils::array_get($rawData, 'decimal_separator_after'));

        // Prepare "use thousands separator"
        $this->isUseThousandsSeparator = isset($rawData['use_thousands_separator']);

        // Prepare "remove if not numeric"
        $this->isRemoveIfNotNumeric = isset($rawData['remove_if_not_numeric']);

        // Prepare precision
        $rawPrecision = Utils::array_get($rawData, 'precision', 0);
        $this->precision = is_numeric($rawPrecision) ? (int) $rawPrecision : 0;
        if ($this->precision < 0) $this->precision = 0; // Make sure the precision is positive

        // Prepare calculations
        $this->formulas = array_map(function($v) {
            return $v && isset($v['formula']) ? $v['formula'] : null;
        }, Utils::array_get($rawData, 'formulas', []));
    }

    /**
     * @param string $selectedOption Selected option in the select input in the calculations tab of the options box
     * @return string Decimal separator. Either "." or ",". Defaults to ".".
     */
    private function getDecimalSeparator($selectedOption) {
        if (!$selectedOption) return '.';
        return $selectedOption === 'comma' ? ',' : '.';
    }

    /*
     * GETTERS
     */

    /**
     * @return string See {@link decimalSeparatorAfter}
     */
    public function getDecimalSeparatorAfter() {
        return $this->decimalSeparatorAfter;
    }

    /**
     * @return bool See {@link useThousandsSeparator}
     */
    public function isUseThousandsSeparator() {
        return $this->isUseThousandsSeparator;
    }

    /**
     * @return bool See {@link removeIfNotNumeric}
     */
    public function isRemoveIfNotNumeric() {
        return $this->isRemoveIfNotNumeric;
    }

    /**
     * @return int See {@link precision}
     */
    public function getPrecision() {
        return $this->precision;
    }

    /**
     * @return array See {@link formulas}
     */
    public function getFormulas() {
        return $this->formulas;
    }

}