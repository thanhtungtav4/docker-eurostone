<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 28/05/2020
 * Time: 10:49
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\Date;


use DateTime;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\InputWithLabel;

abstract class AbstractFixedDateConditionCommand extends AbstractDateConditionCommand {

    /**
     * @return string A text that explains what to enter to "date" input
     * @since 1.11.0
     */
    abstract protected function getInputDescription(): string;

    /**
     * @return string A string that shows the inequality/equality that should be satisfied for the condition to be met.
     *                The time given by the user can be included as "given date", since the user enters the time as a
     *                fixed date. For example, "the date > the given date". The resultant string must be translatable.
     * @since 1.11.0
     */
    abstract protected function getConditionFormulaForHumans(): string;

    /**
     * Check if the date condition is satisfied
     *
     * @param DateTime $subjectDate See {@link onCheckCondition()}
     * @param DateTime $givenDate   The date given by the user
     * @return bool True if the condition is satisfied. Otherwise, false.
     * @since 1.11.0
     */
    abstract protected function onCheckDateCondition(DateTime $subjectDate, DateTime $givenDate): bool;

    /*
     *
     */

    protected function createViews(): ?ViewDefinitionList {
        $description = $this->getInputDescription() . ' ' . _wpcc_enter_date_with_format() . ' ' . sprintf(
                _wpcc('For the condition to be met, the following condition should be satisfied: %1$s'),
                $this->getConditionFormulaForHumans()
            );

        return (new ViewDefinitionList())
            ->add((new ViewDefinition(InputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Date'))
                ->setVariable(ViewVariableName::INFO,  $description)
                ->setVariable(ViewVariableName::NAME,  InputName::DATE)
                ->setVariable(ViewVariableName::TYPE,  'text'))
            ;
    }

    protected function onCheckCondition(DateTime $subjectDate): bool {
        // Get the given date
        $givenDateStr = $this->getOption(InputName::DATE);
        if ($givenDateStr === null || $givenDateStr === '') return false;

        $givenDate = $this->parseDate($givenDateStr);
        if ($givenDate === null) return false;

        return $this->onCheckDateCondition($subjectDate, $givenDate);
    }

}