<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 18/04/2020
 * Time: 19:07
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Objects;


use WPCCrawler\Interfaces\Arrayable;
use WPCCrawler\Objects\Events\Enums\EventGroupKey;
use WPCCrawler\Objects\Filtering\Enums\CommandType;
use WPCCrawler\Objects\Transformation\Objects\TransformableField;

/**
 * Configuration that defines for what type of event groups and for what type of commands a {@link TransformableField}
 * is available
 *
 * @since 1.11.0
 */
class FieldConfig implements Arrayable {

    /**
     * @var string|null One of the constants defined in {@link EventGroupKey}. This defines an event group a field is
     *      suitable for. If null, it means the field is suitable for all available event groups.
     */
    private $eventGroup;

    /**
     * @var string[]|null One or more of the constants defined in {@link CommandType}. This defines what type of
     *      commands the field is suitable for.
     */
    private $commandTypes;

    /**
     * @var null|array Stores the output of {@link toArray()} so that the same array is not created every time the
     *      method is called.
     */
    private $arrayCache = null;

    /**
     * @param string|null          $eventGroup   See {@link eventGroup}
     * @param string|string[]|null $commandTypes See {@link commandTypes}
     * @since 1.11.0
     */
    public function __construct(?string $eventGroup, $commandTypes = null) {
        $this->eventGroup   = $eventGroup;
        $this->commandTypes = $commandTypes ? (array) $commandTypes : null;
    }

    /**
     * @return string|null See {@link eventGroup}
     * @since 1.11.0
     */
    public function getEventGroup(): ?string {
        return $this->eventGroup;
    }

    /**
     * @param string|null $eventGroup See {@link eventGroup}
     * @return FieldConfig
     * @since 1.11.0
     */
    public function setEventGroup(?string $eventGroup): FieldConfig {
        $this->eventGroup = $eventGroup;
        $this->invalidateArrayCache();
        return $this;
    }

    /**
     * @return string[]|null See {@link commandTypes}
     * @since 1.11.0
     */
    public function getCommandTypes(): ?array {
        return $this->commandTypes;
    }

    /**
     * @param string[]|null $commandTypes See {@link commandTypes}
     * @return FieldConfig
     * @since 1.11.0
     */
    public function setCommandTypes(?array $commandTypes): FieldConfig {
        $this->commandTypes = $commandTypes;
        $this->invalidateArrayCache();
        return $this;
    }

    /*
     *
     */

    public function toArray(): array {
        if ($this->arrayCache === null) {
            $this->arrayCache = [
                'commandTypes' => $this->commandTypes,
                'eventGroup'   => $this->eventGroup
            ];
        }

        return $this->arrayCache;
    }

    /*
     *
     */

    private function invalidateArrayCache(): void {
        $this->arrayCache = null;
    }

}