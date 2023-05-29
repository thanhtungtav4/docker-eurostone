<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 09/05/2020
 * Time: 19:51
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Explaining\Explainers;


use WPCCrawler\Objects\Events\EventService;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractActionCommand;
use WPCCrawler\Objects\Filtering\Explaining\Base\AbstractExplainer;
use WPCCrawler\Objects\Filtering\Filter\Filter;

/**
 * Explains an already-executed filter
 *
 * @since 1.11.0
 */
class FilterExplainer extends AbstractExplainer {

    /** @var Filter The already-executed filter that will be explained */
    private $filter;

    /**
     * @param Filter $filter See {@link filter}
     * @since 1.11.0
     */
    public function __construct(Filter $filter) {
        $this->filter = $filter;
    }

    public function explain(): array {
        $condition  = $this->filter->getCondition();

        return [
            'type'      => 'filter',
            'title'     => $this->filter->getTitle(),
            'enabled'   => $this->filter->isEnabled(),
            'if'        => $condition ? (new FilterConditionExplainer($condition))->explain() : null,
            'ifEvent'   => $this->getEventNameWithKey($this->filter->getConditionEventKey()),
            'then'      => $this->getActionExplanations(),
            'thenEvent' => $this->getEventNameWithKey($this->filter->getActionEventKey()),
        ];
    }

    /*
     *
     */

    /**
     * Explain all action commands of {@link filter}
     *
     * @return array|null If there is no action, returns null. Otherwise, returns an array of arrays explaining each
     *                    action in this filter
     * @since 1.11.0
     */
    protected function getActionExplanations(): ?array {
        $actions = $this->filter->getActions();
        if (!$actions) return null;

        $results = [];
        foreach($actions as $actionCommand) {
            $results[] = $this->getActionExplanation($actionCommand);
        }

        return $results;
    }

    /**
     * Explains an action command
     *
     * @param AbstractActionCommand|null $cmd The command
     * @return array|null The explanation. If there is no command, returns null.
     * @since 1.11.0
     */
    protected function getActionExplanation(?AbstractActionCommand $cmd): ?array {
        return $cmd ? (new ActionCommandExplainer($cmd))->explain() : null;
    }

    /**
     * @param string|null $eventKey Key of an event registered to {@link EventService}
     * @return string|null If found, the human-readable name of the event. Otherwise, null.
     * @since 1.11.0
     */
    protected function getEventNameWithKey(?string $eventKey): ?string {
        if (!$eventKey) return null;

        $event = EventService::getInstance()->getEventByKey($eventKey);
        return $event ? $event->getName() : null;
    }

}