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
use WPCCrawler\Objects\Views\NumericInputWithLabel;

abstract class AbstractRelativeDateConditionCommand extends AbstractDateConditionCommand {

    /**
     * @return string A text that explains what to enter to "hours" input
     * @since 1.11.0
     */
    abstract protected function getInputDescription(): string;

    /**
     * @return string A string that shows the inequality/equality that should be satisfied for the condition to be met.
     *                The time given by the user can be included as "hours", since the user enters the time in "hours".
     *                For example, "now - the date > hours". The resultant string must be translatable.
     * @since 1.11.0
     */
    abstract protected function getConditionFormulaForHumans(): string;

    /**
     * Check if the date condition is satisfied
     *
     * @param DateTime $subjectDate See {@link onCheckCondition()}
     * @param DateTime $now         Current date. This date can be modified if it is required to check the condition.
     *                              So, no need to clone this.
     * @param int      $seconds     The number of seconds given by the user
     * @return bool True if the condition is satisfied. Otherwise, false.
     * @since 1.11.0
     */
    abstract protected function onCheckDateCondition(DateTime $subjectDate, DateTime $now, int $seconds): bool;

    /*
     *
     */

    protected function createViews(): ?ViewDefinitionList {
        $description = $this->getInputDescription() . ' ' . sprintf(
                _wpcc('You can enter decimals. The value can be negative as well. For the condition to be met, the 
                following condition should be satisfied: %1$s'),
                $this->getConditionFormulaForHumans()
            );

        return (new ViewDefinitionList())
            ->add((new ViewDefinition(NumericInputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Hours'))
                ->setVariable(ViewVariableName::INFO,  $description)
                ->setVariable(ViewVariableName::NAME,  InputName::NUMBER)
                ->setVariable(ViewVariableName::STEP,  'any'))
            ;
    }

    protected function onCheckCondition(DateTime $subjectDate): bool {
        // Get the current date
        $now = $this->getCurrentDate();
        if ($now === null) return false;

        // Get the number of minutes
        $hours = $this->getOption(InputName::NUMBER);
        if ($hours === null || !is_numeric($hours)) return false;

        $seconds = (int) round((float) $hours * 60 * 60);
        return $this->onCheckDateCondition($subjectDate, $now, $seconds);
    }

}