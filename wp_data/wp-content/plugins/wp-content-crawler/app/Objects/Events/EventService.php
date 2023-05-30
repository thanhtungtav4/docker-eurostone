<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 01/04/2020
 * Time: 19:23
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Events;


use Exception;
use WPCCrawler\Objects\Events\Base\AbstractEvent;
use WPCCrawler\Objects\Events\Enums\EventKey;
use WPCCrawler\Objects\Events\Events\AfterCategoryCrawlerReadyEvent;
use WPCCrawler\Objects\Events\Events\AfterCategoryRequestEvent;
use WPCCrawler\Objects\Events\Events\AfterPostCrawlerReadyEvent;
use WPCCrawler\Objects\Events\Events\AfterPostRequestEvent;
use WPCCrawler\Objects\Events\Events\AfterSpinningEvent;
use WPCCrawler\Objects\Events\Events\AfterTranslationEvent;
use WPCCrawler\Objects\Events\Events\CategoryDataReadyEvent;
use WPCCrawler\Objects\Events\Events\PostDataReadyEvent;

class EventService {

    /** @var EventService */
    private static $instance = null;

    /** @var bool */
    private $registryInitialized = false;

    /**
     * @var array|null An associative array that stores singleton instances of the registered event classes. Keys are
     *      class names and the values are their instances.
     */
    private $eventRegistry = null;

    /**
     * @var array|null The same thing as {@link eventRegistry}, but the keys are event keys instead of class names.
     */
    private $eventKeyRegistry = null;

    /**
     * @return EventService
     * @since 1.11.0
     */
    public static function getInstance(): EventService {
        if (static::$instance === null) static::$instance = new EventService();
        return static::$instance;
    }

    /**
     * This is a singleton.
     * @since 1.11.0
     */
    protected function __construct() { }

    /**
     * @return string[] An array of names of classes that extend to {@link AbstractEvent}
     * @since 1.11.0
     */
    protected function getEventClasses(): array {
        // Add all available event classes here
        return [
            AfterCategoryRequestEvent::class,
            AfterPostRequestEvent::class,

            PostDataReadyEvent::class,
            CategoryDataReadyEvent::class,

            AfterTranslationEvent::class,
            AfterSpinningEvent::class,

            AfterPostCrawlerReadyEvent::class,
            AfterCategoryCrawlerReadyEvent::class,
        ];
    }

    /**
     * Get the instance of an event class
     *
     * @param string|null $cls Class name of the one of the registered event classes
     * @return AbstractEvent|null If there is an instance of the given class in the registry, it will be returned.
     *                            Otherwise, null.
     * @since 1.11.0
     */
    public function getEvent(?string $cls): ?AbstractEvent {
        if ($cls === null) return null;
        return $this->getEventRegistry()[$cls] ?? null;
    }

    /**
     * Get the instance of an event by its key
     *
     * @param string|null $eventKey One of the constants defined in {@link EventKey}
     * @return AbstractEvent|null If there is an event in the registry that has the given event key, it will be
     *                            returned. Otherwise, null.
     * @since 1.11.0
     */
    public function getEventByKey(?string $eventKey): ?AbstractEvent {
        if ($eventKey === null) return null;
        return $this->getEventKeyRegistry()[$eventKey] ?? null;
    }

    /**
     * Get the name of an event by its key
     *
     * @param string|null $eventKey See {@link getEventByKey()}
     * @return string If found, the name of the event. Otherwise, a default value indicating that the event is not
     *                found.
     * @since 1.11.0
     */
    public function getEventNameByKey(?string $eventKey): string {
        $event = $this->getEventByKey($eventKey);
        return $event ? $event->getName() : _wpcc('Not found');
    }

    /**
     * @param string|null $cls Class of the events. If this is null, all events will be returned. Otherwise, only the
     *                         events having the class or extending to the class will be returned.
     * @return AbstractEvent[] Instances of all registered event classes
     * @since 1.11.0
     */
    public function getAllEvents(?string $cls = null): array {
        $eventInstances = array_values($this->getEventRegistry());

        // If there is a class name, filter out the instances that are not instances of that class.
        if ($cls !== null) {
            $eventInstances = array_filter($eventInstances, function($event) use (&$cls) {
                return is_a($event, $cls);
            });
        }

        return $eventInstances;
    }

    /**
     * Get the event registry
     *
     * @return array See {@link eventRegistry}
     * @since 1.11.0
     */
    public function getEventRegistry(): array {
        $this->maybeInitRegistries();
        return $this->eventRegistry ?: [];
    }

    /**
     * Get the event key registry
     *
     * @return array See {@link eventKeyRegistry}
     * @since 1.11.0
     */
    public function getEventKeyRegistry(): array {
        $this->maybeInitRegistries();
        return $this->eventKeyRegistry ?: [];
    }

    /**
     * Invalidates the registries initialized by {@link maybeInitRegistries()}. This means that the event instances will
     * be invalidated, meaning that their attached observers will be invalidated as well. The next time a registry
     * method is called, such as {@link getEventRegistry()}, the registries will be created again. So, fresh registries
     * will be created.
     *
     * @since 1.11.0
     */
    public function invalidateRegistries(): void {
        $this->eventRegistry       = null;
        $this->eventKeyRegistry    = null;
        $this->registryInitialized = false;
    }

    /*
     *
     */

    /**
     * Initialize {@link eventRegistry} and {@link eventKeyRegistry} if they are not initialized
     * @since 1.11.0
     */
    protected function maybeInitRegistries(): void {
        if ($this->registryInitialized) return;
        $this->registryInitialized = true;

        $this->eventRegistry    = [];
        $this->eventKeyRegistry = [];

        foreach($this->getEventClasses() as $cls) {
            try {
                // Create an instance of the event
                $instance = new $cls();

            } catch (Exception $e) {
                error_log(sprintf(
                    "Instance of ${cls} event class could not be created in %s: " . $e->getMessage(),
                    static::class
                ));
                continue;
            }

            // Make sure it is of the right type
            if (!is_a($instance, AbstractEvent::class)) continue;
            /** @var AbstractEvent $instance */

            // Add the instance to the registries
            $this->eventRegistry[$cls]                   = $instance;
            $this->eventKeyRegistry[$instance->getKey()] = $instance;
        }
    }
}