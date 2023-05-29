<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/04/2020
 * Time: 22:36
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Observers;


use WPCCrawler\Objects\Events\EventService;
use WPCCrawler\Objects\Events\Interfaces\Event;
use WPCCrawler\Objects\Events\Interfaces\Observer;
use WPCCrawler\Objects\Filtering\Explaining\Loggers\Logger;
use WPCCrawler\Objects\Filtering\Filter\FilterApplier;

class FilterActionExecutor implements Observer {

    /** @var FilterApplier */
    private $applier;

    /**
     * @param FilterApplier $applier
     * @since 1.11.0
     */
    public function __construct(FilterApplier $applier) {
        $this->applier = $applier;
    }

    /**
     * @inheritDoc
     */
    public function update(Event $event): void {
        // If the condition was not checked before, check it.
        if ($this->applier->getConditionCheckResult() === null) {
            $this->applier->checkCondition();

            $this->maybeLogThatConditionCheckedHere();
        }

        // If the condition is met, execute the actions.
        if ($this->applier->getConditionCheckResult()) {
            $this->applier->executeActions();
        }
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * Adds a log message indicating that the condition is checked here, if there is a logger.
     *
     * @since 1.11.0
     */
    protected function maybeLogThatConditionCheckedHere(): void {
        $logger = $this->getConditionLogger();
        if (!$logger) return;

        $filter       = $this->applier->getFilter();
        $eventService = EventService::getInstance();

        $logger->addMessage(sprintf(
            _wpcc('The condition is executed by the event defined for the action part (%1$s), because the defined 
                action event (%1$s) is fired earlier than the defined condition event (%2$s).'),
            $eventService->getEventNameByKey($filter->getActionEventKey()),
            $eventService->getEventNameByKey($filter->getConditionEventKey())
        ));
    }

    /**
     * @return Logger|null Logger of the top-most filter condition, if it exists. Otherwise, null.
     * @since 1.11.0
     */
    protected function getConditionLogger(): ?Logger {
        $condition = $this->applier->getFilter()->getCondition();
        return $condition ? $condition->getLogger() : null;
    }
}