<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 31/10/2018
 * Time: 22:01
 */

namespace WPCCrawler\Objects;


use Exception;
use MathParser\Interpreting\Evaluator;
use MathParser\StdMathParser;
use WPCCrawler\Objects\Enums\InformationMessage;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;

/**
 * Parses a string formula and calculates it.
 * @package WPCCrawler\objects
 */
class StringCalculator {

    /** @var string Decimal separator that will be used for the output. It can be either "." or ",". Defaults to ".". */
    private $decimalSeparatorAfter = ".";

    /** @var int Number of digits coming after the decimal separator */
    private $precision = 0;

    /** @var bool True if the result should use thousands separator. Otherwise, false. */
    private $useThousandsSeparator = false;

    /** @var string[] */
    private $validDecimalSeparators = [".", ","];

    /** @var string */
    private $defaultDecimalSeparator = ".";

    /**
     * @param string $decimalSeparatorAfter See {@link decimalSeparatorAfter}
     * @param int $precision See {@link precision}
     * @param bool $useThousandsSeparator See {@link useThousandsSeparator}
     */
    public function __construct($decimalSeparatorAfter = null, $precision = 0, $useThousandsSeparator = false) {
        // Make sure the decimal separator is either dot or comma
        if (!$decimalSeparatorAfter || !in_array($decimalSeparatorAfter, $this->validDecimalSeparators)) {
            $decimalSeparatorAfter = $this->defaultDecimalSeparator;
        }

        $this->decimalSeparatorAfter = $decimalSeparatorAfter;
        $this->precision = (int) $precision;
        $this->useThousandsSeparator = $useThousandsSeparator ? $useThousandsSeparator : false;
    }

    /**
     * @param string $formula A formula in string format. E.g. "3 + 5 * (4 / 6) - 20 + x"
     * @param array $variables Key-value pair where keys are variable names and the values are variable values. E.g.
     *                          ['x' => 50].
     * @return mixed
     * @throws Exception        If value of a variable is not numeric
     */
    public function calculate($formula, $variables = []) {
        // Make sure the variables are numeric
        foreach($variables as $k => &$v) {
            if (!is_numeric($v)) {
                $e = new Exception("Value {$v} is not numeric.");

                Informer::add(Information::fromInformationMessage(
                    InformationMessage::VALUE_NOT_NUMERIC_ERROR,
                    _wpcc("Value") . ": {$v}" . ' - ' . sprintf(_wpcc('You can try clearing thousands separators and setting decimal separator as "%1$s"'), '.'),
                    InformationType::ERROR
                )->setException($e)->addAsLog());

                throw $e;
            }
        }

        // Uses https://github.com/mossadal/math-parser
        $parser = new StdMathParser();

        // Generate an abstract syntax tree
        $AST = $parser->parse($formula);

        // Evaluate the expression:
        $evaluator = new Evaluator();
        if ($variables) $evaluator->setVariables($variables);

        // Evaluate the expression and return it
        $result = $AST->accept($evaluator);

        return $this->prepareResult($result);
    }

    /**
     * Calculates a formula where it contains only one variable, which is 'X' or 'x'.
     *
     * @param string $formula See {@link calculate}
     * @param double|string|float $x The value of the variable X
     * @return mixed See {@link calculate}
     * @throws Exception See {@link calculate}
     */
    public function calculateForX($formula, $x) {
        return $this->calculate($formula, ['X' => $x, 'x' => $x]);
    }

    /*
     * SETTERS
     */

    /**
     * @param string $decimalSeparatorAfter
     * @return StringCalculator
     */
    public function setDecimalSeparatorAfter($decimalSeparatorAfter) {
        $this->decimalSeparatorAfter = $decimalSeparatorAfter;
        return $this;
    }

    /**
     * @param int $precision
     * @return StringCalculator
     */
    public function setPrecision($precision) {
        $this->precision = $precision;
        return $this;
    }

    /**
     * @param bool $useThousandsSeparator See {@link useThousandsSeparator}
     */
    public function setUseThousandsSeparator($useThousandsSeparator): void {
        $this->useThousandsSeparator = $useThousandsSeparator;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * @param double $number
     * @return string
     */
    private function prepareResult($number) {
        $thousandSeparator = $this->getThousandSeparator();
        $result = number_format($number, $this->precision, $this->decimalSeparatorAfter, $thousandSeparator);

        if (!$this->useThousandsSeparator) {
            $result = str_replace($thousandSeparator, '', $result);
        }

        return $result;
    }

    /**
     * Get the thousand separator depending on {@link decimalSeparatorAfter}
     * @return string If the decimal separator is ".", returns ",". Otherwise, returns ".".
     */
    private function getThousandSeparator() {
        return $this->decimalSeparatorAfter === $this->validDecimalSeparators[0] ? $this->validDecimalSeparators[1] : $this->validDecimalSeparators[0];
    }
}