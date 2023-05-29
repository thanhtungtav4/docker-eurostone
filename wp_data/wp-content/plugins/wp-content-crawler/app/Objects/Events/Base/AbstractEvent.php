<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 01/04/2020
 * Time: 19:24
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Events\Base;


use Exception;
use WPCCrawler\Interfaces\Arrayable;
use WPCCrawler\Objects\Events\Enums\EventKey;
use WPCCrawler\Objects\Events\Interfaces\Event;
use WPCCrawler\Objects\Events\Interfaces\Observer;

abstract class AbstractEvent implements Event, Arrayable {

    /**
     * @var Observer[] An array of callback functions, i.e. observers, that will be called (notified) when this event
     *      occurs.
     */
    private $observers = [];

    /**
     * @return string Get the identifier of this event, unique among all events. One of the constants defined in
     *                {@link EventKey}.
     * @since 1.11.0
     */
    abstract public function getKey(): string;

    /**
     * @return string Human-readable name of this event
     * @since 1.11.0
     */
    abstract public function getName(): string;

    /**
     * @return string A description for humans
     * @since 1.11.0
     */
    abstract public function getDescription(): string;

    /*
     *
     */

    /**
     * Attach an observer that will be notified when this event occurs
     *
     * @param Observer|null $observer
     * @since 1.11.0
     */
    public function attach(?Observer $observer): void {
        if ($observer === null) return;
        $this->observers[] = $observer;
    }

    /**
     * Detach a previously attached observer so that it will not be notified when this event occurs anymore
     *
     * @param Observer|null $observer
     * @since 1.11.0
     */
    public function detach(?Observer $observer): void {
        if ($observer === null) return;

        foreach($this->observers as $k => $o) {
            if ($o === $observer) {
                unset($this->observers[$k]);
                return;
            }
        }
    }

    /**
     * Notify all observers that this event occurred
     *
     * @throws Exception Commands run by the observers attached to this event can throw an exception. For example,
     *                   {@link StopAndDeletePost} command throws a {@link CancelSavingAndDeleteException}.
     * @since 1.11.0
     */
    public function notify(): void {
        foreach($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /*
     *
     */

    public function toArray(): array {
        return [
            'key'  => $this->getKey(),
            'name' => $this->getName(),
            'desc' => $this->getDescription()
        ];
    }

}