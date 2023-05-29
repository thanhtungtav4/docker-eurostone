<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 15/03/2020
 * Time: 09:06
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\Views;


use WPCCrawler\Interfaces\Arrayable;

class ViewDefinitionList implements Arrayable {

    /** @var ViewDefinition[] */
    private $items;

    /**
     * @param ViewDefinition[]|null $items
     * @since 1.11.0
     */
    public function __construct(?array $items = null) {
        $this->items = $items === null ? [] : $items;
    }

    public function add(ViewDefinition $definition): self {
        $this->items[] = $definition;
        return $this;
    }

    /**
     * @param ViewDefinition $definition The view definition that will be added to the beginning of the list
     * @return $this
     * @since 1.11.0
     */
    public function prepend(ViewDefinition $definition): self {
        array_unshift($this->items, $definition);
        return $this;
    }

    /**
     * @return ViewDefinition[]
     * @since 1.11.0
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array {
        $result = [];
        foreach($this->getItems() as $item) {
            $result[] = $item->toArray();
        }

        return $result;
    }

}