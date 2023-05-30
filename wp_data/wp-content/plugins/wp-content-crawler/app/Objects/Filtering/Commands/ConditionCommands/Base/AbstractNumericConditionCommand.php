<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 28/03/2020
 * Time: 11:18
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base;


use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\NumericInputWithLabel;

/**
 * This is the base class for the commands that contain only 1 numeric input option and that accept only numeric
 * subjects.
 *
 * @since 1.11.0
 */
abstract class AbstractNumericConditionCommand extends AbstractConditionCommand {

    /**
     * @return string The input's description that will be shown to the user as an explanation
     * @since 1.11.0
     */
    protected function getInputDescription(): string {
        return _wpcc('Enter the value');
    }

    /**
     * Check if the condition applies
     *
     * @param float $subjectValue Value retrieved from the subject. Actual value.
     * @param float $optionValue  Value entered to the command
     * @return bool True if the condition applies. Otherwise, false.
     * @since 1.11.0
     */
    protected abstract function onCheckCondition(float $subjectValue, float $optionValue): bool;

    /*
     *
     */

    public function getInputDataTypes(): array {
        return [ValueType::T_NUMERIC, ValueType::T_FLOAT, ValueType::T_INTEGER];
    }

    protected function createViews(): ?ViewDefinitionList {
        return (new ViewDefinitionList())
            ->add((new ViewDefinition(NumericInputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Value'))
                ->setVariable(ViewVariableName::INFO,  $this->getInputDescription())
                ->setVariable(ViewVariableName::NAME,  InputName::NUMBER)
                ->setVariable(ViewVariableName::STEP,  'any'))
            ;
    }

    protected function onDoesApply($subjectValue): bool {
        $optionValue = $this->getOption(InputName::NUMBER);

        $preparedSubjectValue = $this->parseFloat($subjectValue);
        $preparedOptionValue  = $this->parseFloat($optionValue);

        if ($preparedSubjectValue === null || $preparedOptionValue === null) {
            $logger = $this->getLogger();
            if ($logger) $logger
                ->addMessage(sprintf(
                    _wpcc('This condition is not met because the subject ("%1$s") and/or the option ("%2$s") value 
                    is not numeric. To compare two numbers, they both should be numeric. You can try to clear thousands 
                    separators and to use %3$s as the decimal separator. Please also clear any characters other than the
                    numbers and the decimal separator.'),
                    $subjectValue,
                    $optionValue,
                    '"."'
                ));

            return false;
        }

        return $this->onCheckCondition($preparedSubjectValue, $preparedOptionValue);
    }

    /*
     *
     */

    /**
     * Parse a value to float
     *
     * @param mixed $value The value that will be parsed to float
     * @return float|null If the value is numeric, its float equivalent. Otherwise, null.
     * @since 1.11.0
     */
    protected function parseFloat($value): ?float {
        return $value !== null && is_numeric($value) ? (float) $value : null;
    }
}