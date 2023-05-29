<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 28/03/2020
 * Time: 21:36
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base;


use Illuminate\Support\Str;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Views\CheckboxWithLabel;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\InputWithLabel;

/**
 * This is the base class for the commands that contain only 1 text input option and that accept only string subjects.
 *
 * @since 1.11.0
 */
abstract class AbstractTextConditionCommand extends AbstractConditionCommand {

    /**
     * @return string Name of the input into which the text will be entered.
     * @since 1.11.0
     */
    protected function getInputName(): string {
        return _wpcc('Text');
    }

    /**
     * @return string Input description that will be shown to the user as an explanation
     * @since 1.11.0
     */
    protected abstract function getInputDescription(): string;

    /**
     * Check if the condition applies
     *
     * @param string $subjectValue Value retrieved from the subject. Actual value.
     * @param string $optionValue  Value entered to the command
     * @return bool True if the condition applies. Otherwise, false.
     * @since 1.11.0
     */
    protected abstract function onCheckCondition(string $subjectValue, string $optionValue): bool;

    /**
     * @return bool True if this command has options. Otherwise, false. When this returns false, no view will be
     *              created.
     * @since 1.11.0
     */
    protected function hasOptions(): bool  {
        return true;
    }

    /**
     * @return bool True if "case insensitive" checkbox should be added as an option
     * @since 1.11.0
     */
    protected function addCaseInsensitiveCheckbox(): bool {
        return true;
    }

    /**
     * @return bool False. This is the default return value of {@link onDoesApply()} returned when it is not possible
     *              to check the condition due to not existing subject or option value.
     * @since 1.11.0
     */
    protected function getDefaultResult(): bool {
        return false;
    }

    /*
     *
     */

    /**
     * @inheritDoc
     */
    public function getInputDataTypes(): array {
        return [ValueType::T_STRING];
    }

    protected function createViews(): ?ViewDefinitionList {
        if (!$this->hasOptions()) return null;

        $list = (new ViewDefinitionList())
            ->add((new ViewDefinition(InputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, $this->getInputName())
                ->setVariable(ViewVariableName::INFO,  $this->getInputDescription())
                ->setVariable(ViewVariableName::NAME,  InputName::TEXT)
                ->setVariable(ViewVariableName::TYPE,  'text'));

        if ($this->addCaseInsensitiveCheckbox()) {
            $list->add((new ViewDefinition(CheckboxWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Case insensitive?'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('Check this if the texts should be compared in a case-insensitive way'))
                ->setVariable(ViewVariableName::NAME,  InputName::CASE_INSENSITIVE));
        }

        return $list;
    }

    /**
     * @inheritDoc
     */
    protected function onDoesApply($subjectValue): bool {
        $optionValue = $this->hasOptions()
            ? $this->getStringOption(InputName::TEXT)
            : null;

        if (!is_scalar($subjectValue) && $subjectValue !== null) {
            return false;
        }

        if (($this->hasOptions() && $optionValue === null) || $subjectValue === null) {
            return $this->getDefaultResult();
        }

        // If the comparison should be case-insensitive, make the values lowercase.
        if ($this->addCaseInsensitiveCheckbox() && $this->getCheckboxOption(InputName::CASE_INSENSITIVE)) {
            $optionValue  = Str::lower((string) $optionValue);
            $subjectValue = Str::lower((string) $subjectValue);
        }

        return $this->onCheckCondition((string) $subjectValue, (string) $optionValue);
    }

}