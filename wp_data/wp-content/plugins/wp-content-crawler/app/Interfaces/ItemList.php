<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 18/02/2021
 * Time: 17:30
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Interfaces;


interface ItemList {

    /**
     * Add an item to the list
     *
     * @param mixed $item
     * @return $this
     * @since 1.11.0
     */
    public function add($item);

    /**
     * Add many items to the list
     *
     * @param array $items
     * @return $this
     * @since 1.11.0
     */
    public function addAll(array $items);

    /**
     * @return array All items in the list
     * @since 1.11.0
     */
    public function getAll(): array;

    /**
     * @return bool True if the list is empty. Otherwise, false.
     * @since 1.11.0
     */
    public function isEmpty(): bool;

}