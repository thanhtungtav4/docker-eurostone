<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 08/07/2020
 * Time: 10:58
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Numeric;


use Exception;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionFactory;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\StringCalculator;
use WPCCrawler\Objects\ValueType\Interfaces\OutputsFloat;
use WPCCrawler\Objects\ValueType\Interfaces\OutputsInteger;
use WPCCrawler\Objects\ValueType\Interfaces\OutputsNumeric;
use WPCCrawler\Objects\ValueType\TypeCaster;
use WPCCrawler\Objects\Views\CheckboxWithLabel;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\NumericInputWithLabel;
use WPCCrawler\Objects\Views\Select\SelectDecimalSeparatorWithLabel;

class Calculate extends AbstractActionCommand implements OutputsNumeric, OutputsInteger, OutputsFloat {

    /** @var null|string The formula used to calculate the latest result */
    private $formula = null;

    public function getKey(): string {
        return CommandKey::CALCULATE;
    }

    public function getName(): string {
        return _wpcc('Calculate');
    }

    public function getInputDataTypes(): array {
        return [ValueType::T_NUMERIC, ValueType::T_INTEGER, ValueType::T_FLOAT];
    }

    protected function createViews(): ?ViewDefinitionList {
        return (new ViewDefinitionList())
            // Decimal separator
            ->add((new ViewDefinition(SelectDecimalSeparatorWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Decimal separator'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('Select the decimal separator to be used in the 
                    resultant number. The one that is not selected will be used as the thousands separator.'))
                ->setVariable(ViewVariableName::NAME,  InputName::DECIMAL_SEPARATOR))

            // Use thousands separator
            ->add((new ViewDefinition(CheckboxWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Use thousands separator?'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('Check this if you want to use thousands separator in 
                    the result.'))
                ->setVariable(ViewVariableName::NAME,  InputName::USE_THOUSANDS_SEPARATOR))

            // Precision
            ->add((new ViewDefinition(NumericInputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Precision'))
                ->setVariable(ViewVariableName::INFO,  sprintf(
                    _wpcc('Enter how many digits at max there can be after the 
                        decimal separator. Default: %s'),
                    0
                ))
                ->setVariable(ViewVariableName::NAME,  InputName::PRECISION)
                ->setVariable(ViewVariableName::STEP,  1))

            // Formulas
            ->add(ViewDefinitionFactory::getInstance()->createFormulaInput());
    }

    protected function onExecute($key, $subjectValue) {
        $this->formula = null;

        // If the subject value is not valid, return the subject value as the result of the calculation since we cannot
        // perform any calculation on it.
        if (!$this->validatePossibleNumericValue($subjectValue)) return $subjectValue;

        // Get a random formula. If there is no formula, return the subject value as-is.
        $this->formula = $this->getRandomFormula();
        if (!$this->validateFormula($this->formula, $subjectValue)) return $subjectValue;

        // Create a calculator with the specified options
        $calculator = new StringCalculator(
            $this->getDecimalSeparator(),
            $this->getPrecision(),
            $this->shouldUseThousandsSeparator()
        );

        try {
            // Calculate
            $result = $this->formula !== null 
                ? $calculator->calculateForX($this->formula, $subjectValue)
                : $subjectValue;

        } catch (Exception $e) {
            Informer::addError($e->getMessage())->setException($e)->addAsLog();
            return $subjectValue;
        }

        // If the result is not valid, return the subject value as-is.
        if (!$this->validatePossibleNumericValue($result)) return $subjectValue;

        return $result;
    }

    public function getTestMessage(): ?string {
        if ($this->formula === null) return null;

        return sprintf(
            _wpcc('Formula: %1$s'),
            "<b>$this->formula</b>"
        );
    }

    /*
     * PROTECTED HELPERS
     */

    /**
     * Check if a formula is valid
     *
     * @param mixed $formula      The formula
     * @param mixed $subjectValue Variable value that will be injected into the formula
     * @return bool True if the formula is valid. Otherwise, false.
     * @since 1.11.0
     */
    protected function validateFormula($formula, $subjectValue): bool {
        if ($formula === null || $formula === '' || is_object($formula) || is_array($formula)) {
            Informer::addInfo(sprintf(
                _wpcc('No valid formula is found in "%1$s" command. No calculation could be performed. The subject 
                    value "%2$s" is not changed.'),
                $this->getName(),
                $subjectValue
            ));

            return false;
        }

        return true;
    }

    /**
     * Validate if the subject value is numeric and add an information message explaining the reason if it is not.
     *
     * @param mixed $possibleNumericValue A possible numeric value that will be validated
     * @return bool True if the subject value is numeric. Otherwise, false.
     * @since 1.11.0
     */
    protected function validatePossibleNumericValue($possibleNumericValue): bool {
        // If the subject value is not numeric, we cannot perform the calculation.
        if (!is_numeric($possibleNumericValue)) {
            // If the subject value is not empty, add an information message.
            if ($possibleNumericValue) {
                $message = sprintf(
                    _wpcc('The calculation cannot be performed in "%1$s" command because the value "%2$s" is not 
                    numeric. You can try to clear the thousands separators and change the decimal separator to "%3$s" to
                    make the value numeric. If the value has characters other than numbers and a decimal separator, 
                    please clear them as well.'),
                    $this->getName(),
                    $possibleNumericValue,
                    '.'
                );

                $logger = $this->getLogger();
                if ($logger) $logger->addMessage($message);

                Informer::addInfo($message);
            }


            return false;
        }

        return true;
    }

    /*
     * OPTION VALUE ACCESSORS
     */

    /**
     * @return string|null One of the formulas defined in "formulas" option. If there is no formula, returns null.
     * @since 1.11.0
     */
    protected function getRandomFormula(): ?string {
        // If there is no formula, then return null.
        $formulas = $this->getFormulas();
        if (!$formulas) return null;

        // Get a random formula. If the formula is empty, return null.
        $formula = $formulas[array_rand($formulas)];

        return $formula !== null ? $formula : null; // @phpstan-ignore-line
    }

    /**
     * Get the formulas. This method removes the empty and null formulas.
     *
     * @return string[]|null If there are formulas, a string array containing the formulas. Otherwise, null.
     * @since 1.11.0
     */
    protected function getFormulas(): ?array {
        $formulas = $this->getOption(InputName::FORMULA);
        if (!$formulas) return null;

        $formulas = array_map(function($v) {
            return trim($v);
        }, $formulas);

        $formulas = array_filter($formulas, function($v) {
            return $v !== null && $v !== '';
        });

        return $formulas ?: null;
    }

    /**
     * @return bool "Use thousands separator" option's value
     * @since 1.11.0
     */
    protected function shouldUseThousandsSeparator(): bool {
        return $this->getCheckboxOption(InputName::USE_THOUSANDS_SEPARATOR);
    }

    /**
     * @return string "Decimal separator" option's value
     * @since 1.11.0
     */
    protected function getDecimalSeparator(): string {
        return $this->getOption(InputName::DECIMAL_SEPARATOR) === 'comma' ? ',' : '.';
    }

    /**
     * @return int Precision option's value
     * @since 1.11.0
     */
    protected function getPrecision(): int {
        $precision = $this->getOption(InputName::PRECISION, 0);
        return is_numeric($precision) ? max((int) $precision, 0) : 0;
    }

    /*
     *
     */

    public function onCastToNumeric($newValue): ?string {
        return TypeCaster::getInstance()->toNumeric($newValue);
    }

    public function onCastToInteger($newValue): ?int {
        return TypeCaster::getInstance()->toInteger($newValue);
    }

    public function onCastToFloat($newValue): ?float {
        return TypeCaster::getInstance()->toFloat($newValue);
    }
}