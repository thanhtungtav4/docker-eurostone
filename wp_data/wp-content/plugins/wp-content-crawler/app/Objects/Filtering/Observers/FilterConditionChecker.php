<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/04/2020
 * Time: 22:34
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Observers;


use WPCCrawler\Objects\Events\Interfaces\Event;
use WPCCrawler\Objects\Events\Interfaces\Observer;
use WPCCrawler\Objects\Filtering\Filter\FilterApplier;

class FilterConditionChecker implements Observer {

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
        // If the condition was checked before, do not check it. This happens when another event executed the actions.
        // See FilterActionExecutor.
        if ($this->applier->getConditionCheckResult() !== null) return;

        // Check the condition. The result of this operation can be retrieved from the applier later, when needed by
        // the action observer.
        $this->applier->checkCondition();
    }

}