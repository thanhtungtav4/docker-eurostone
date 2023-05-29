<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 19/04/2020
 * Time: 08:42
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Crawling\Data\Url;


use WPCCrawler\Interfaces\Arrayable;

class PostUrlList implements Arrayable {
    
    /** @var PostUrl[] */
    private $items;

    /**
     * @param PostUrl[] $items
     * @since 1.11.0
     */
    public function __construct(?array $items = null) {
        $this->setItems($items);
    }

    /**
     * @return bool True if the list is empty. Otherwise, false.
     * @since 1.11.0
     */
    public function isEmpty() {
        return empty($this->items);
    }

    /**
     * @return PostUrl[]
     * @since 1.11.0
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * @param PostUrl[] $items
     * @since 1.11.0
     */
    public function setItems(?array $items): void {
        $this->items = $items === null ? [] : $items;
    }

    /**
     * Add a URL to the list
     *
     * @param PostUrl $url
     * @since 1.11.0
     */
    public function addItem(PostUrl $url): void {
        $this->items[] = $url;
    }

    /**
     * Remove an item with its index
     *
     * @param mixed $index Index of the item
     * @since 1.11.0
     */
    public function removeItem($index): void {
        if (!isset($this->items[$index])) return;
        unset($this->items[$index]);
    }

    /**
     * Reverse the list
     *
     * @since 1.11.0
     */
    public function reverse(): void {
        $this->setItems(array_reverse($this->getItems()));
    }
    
    /*
     * 
     */

    public function toArray(): array {
        $result = [];
        foreach($this->getItems() as $item) {
            $result[] = $item->toArray();
        }
        
        return $result;
    }

}