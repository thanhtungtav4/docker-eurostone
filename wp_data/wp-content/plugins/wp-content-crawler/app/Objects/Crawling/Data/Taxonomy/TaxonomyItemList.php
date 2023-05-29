<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 19/02/2021
 * Time: 11:45
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Crawling\Data\Taxonomy;


use WPCCrawler\Interfaces\ItemList;

class TaxonomyItemList implements ItemList {

    /** @var TaxonomyItem[] */
    private $list = [];

    /**
     * Add an item to the list
     *
     * @param TaxonomyItem $item
     * @return $this
     * @since 1.11.0
     */
    public function add($item): self {
        $this->list[] = $item;
        return $this;
    }

    /**
     * Add many taxonomy items to the list
     *
     * @param TaxonomyItem[] $items
     * @return $this
     * @since 1.11.0
     */
    public function addAll(array $items): self {
        foreach($items as $TaxonomyItem) {
            $this->add($TaxonomyItem);
        }

        return $this;
    }

    /**
     * @return TaxonomyItem[] All items in the list
     * @since 1.11.0
     */
    public function getAll(): array {
        return $this->list;
    }

    /**
     * @return bool True if the list is empty. Otherwise, false.
     * @since 1.11.0
     */
    public function isEmpty(): bool {
        return empty($this->list);
    }
}