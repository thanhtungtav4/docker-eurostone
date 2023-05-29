<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 03/04/2020
 * Time: 08:27
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Filter;


use WPCCrawler\Objects\Events\Base\AbstractEvent;
use WPCCrawler\Objects\Events\EventService;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\FilterDependencyProvider\FilterDependencyProvider;
use WPCCrawler\Objects\Filtering\Observers\FilterActionExecutor;
use WPCCrawler\Objects\Filtering\Observers\FilterConditionChecker;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;

class FilterApplier {

    /** @var Filter The filter whose conditions are checked and, if conditions hold, whose commands will be applied */
    private $filter;

    /** @var FilterDependencyProvider */
    private $provider;

    /**
     * @param Filter                   $filter See {@link filter}
     * @param FilterDependencyProvider $provider
     * @since 1.11.0
     */
    public function __construct(Filter $filter, FilterDependencyProvider $provider) {
        $this->filter   = $filter;
        $this->provider = $provider;
    }

    /**
     * By using the given {@link Transformable}s ({@link FilterDependencyProvider::getDataSourceMap()}), checks if the
     * {@link filter}'s condition is met ({@link checkCondition()}) and, if it is met, executes the {@link filter}'s
     * actions ({@link executeActions()}).
     *
     * Applies the given filter by using the given {@link Transformable}s. The identifiers will be used to decide which
     * data source should be used by which {@link AbstractBaseCommand}.
     *
     * @uses  checkCondition()
     * @uses  executeActions()
     * @since 1.11.0
     */
    public function apply(): void {
        // If the filter's condition is not met, stop.
        if (!$this->checkCondition()) return;

        // The condition is met. Execute the actions.
        $this->executeActions();
    }

    /**
     * Subscribe to the events specified in the filter so that the condition and the action can be run when those
     * events are triggered. If the filter is disabled, the filter will not be subscribed to the events.
     *
     * @param AbstractEvent      $defaultConditionEvent Default condition event that will be used if the condition
     *                                                  event specified by the filter could not be retrieved.
     * @param AbstractEvent|null $defaultActionEvent    Default action event that will be used if the action event
     *                                                  specified by the filter could not be retrieved. If this is
     *                                                  null, default condition event will be used for this.
     * @since 1.11.0
     */
    public function subscribeEvents(AbstractEvent $defaultConditionEvent, ?AbstractEvent $defaultActionEvent = null): void {
        // If this filter is disabled, do not subscribe to the events.
        if ($this->getFilter()->isDisabled()) return;

        // Normally, we need to check if the events defined by the user are actually allowed for the fields of the
        // commands, and the subscription must not occur if the event is not allowed. However, adding this check will
        // introduce additional overhead and room for bugs unnecessarily. The commands must be designed in such a way
        // that unavailability of a requirement is handled silently, without causing any fatal error or misbehavior. The
        // UI does not provide a way to assign fields to unsuitable events. So, if we expect a mismatch between the
        // field and its event, it will come from a developer that tries to do something unexpected. If they can do it,
        // let them do it. We try to provide as much functionality as possible. If what is done is possible, it is OK to
        // do so. Our purpose is to not burden the users with finding the suitable fields, which is already done in the
        // UI. So, hacking this is OK, if it is possible.

        $eventService = EventService::getInstance();

        // Get the specified condition event by its key. If it does not exist, use the default condition event.
        $eventCondition = $eventService->getEventByKey($this->getFilter()->getConditionEventKey());
        if ($eventCondition === null) $eventCondition = $defaultConditionEvent;
        $eventCondition->attach(new FilterConditionChecker($this));

        // Get the specified action event by its key. If it does not exist, use the default action event.
        $eventAction = $eventService->getEventByKey($this->getFilter()->getActionEventKey());
        if ($eventAction === null) $eventAction = $defaultActionEvent ?: $defaultConditionEvent;
        $eventAction->attach(new FilterActionExecutor($this));
    }

    /**
     * @return bool True if the condition evaluates to true. Otherwise, false.
     * @since 1.11.0
     */
    public function checkCondition(): bool {
        return $this->getFilter()->checkCondition($this->provider);
    }

    /**
     * Execute this filter's actions. This method does not check if the filter's condition is met.
     *
     * @since 1.11.0
     */
    public function executeActions(): void {
        $this->getFilter()->executeActions($this->provider);
    }

    /**
     * @return bool|null See {@link Filter::getConditionCheckResult()}
     * @since 1.11.0
     */
    public function getConditionCheckResult(): ?bool {
        return $this->getFilter()->getConditionCheckResult();
    }

    /*
     * PUBLIC GETTERS
     */

    /**
     * @return Filter See {@link filter}
     * @since 1.11.0
     */
    public function getFilter(): Filter {
        return $this->filter;
    }
}