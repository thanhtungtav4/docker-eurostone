<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 18/02/2021
 * Time: 17:23
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Crawling\Data\Meta;


use WPCCrawler\Interfaces\ItemList;

class PostMetaList implements ItemList {

    /** @var PostMeta[] */
    private $list = [];

    /**
     * Add an item to the list
     *
     * @param PostMeta $item
     * @return $this
     * @since 1.11.0
     */
    public function add($item): self {
        $this->list[] = $item;
        return $this;
    }

    /**
     * Add many post meta items to the list
     *
     * @param PostMeta[] $items
     * @return $this
     * @since 1.11.0
     */
    public function addAll(array $items): self {
        foreach($items as $postMeta) {
            $this->add($postMeta);
        }

        return $this;
    }

    /**
     * @return PostMeta[] All items in the list
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