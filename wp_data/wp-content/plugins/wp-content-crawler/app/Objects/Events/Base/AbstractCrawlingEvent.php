<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/04/2020
 * Time: 15:07
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Events\Base;


use WPCCrawler\Objects\Events\Enums\EventGroupKey;

abstract class AbstractCrawlingEvent extends AbstractEvent {

    /**
     * @return string Get the event group this event is in. One of the constants defined in {@link EventGroupKey}.
     *                Event groups are used to group related events so that things can target certain event groups
     *                instead of specific events. Event groups contain similar events. For example, if post data is
     *                available for three different events, then those events can be in the same group because the
     *                things targeting the post data is suitable for all of the events.
     * @since 1.11.0
     */
    abstract public function getEventGroup(): string;

    /*
     *
     */

    public function toArray(): array {
        return array_merge(parent::toArray(), [
            'eventGroup' => $this->getEventGroup()
        ]);
    }

}