<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 14/11/2019
 * Time: 22:00
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Chunk\Offset;


class OffsetList {

    /** @var Offset[] */
    private $offsets = [];

    /**
     * Add an offset to the list. The previous offset will be assigned to the new offset as well (See
     * {@link Offset::setPrevOffset()}).
     *
     * @param Offset $offset
     * @return OffsetList
     * @since 1.9.0
     */
    public function add(Offset $offset) {
        $offset->setPrevOffset($this->getLast());
        $this->offsets[] = $offset;

        return $this;
    }

    /**
     * Add many offsets to the list
     *
     * @param Offset[] $offsets
     * @return OffsetList
     * @since 1.9.0
     */
    public function addAll(array $offsets) {
        foreach($offsets as $offset) {
            $this->add($offset);
        }

        return $this;
    }

    /**
     * Clear the list by removing all items. The list will be empty after this.
     *
     * @return $this
     * @since 1.9.0
     */
    public function clear() {
        $this->offsets = [];
        return $this;
    }

    /*
     *
     */

    /**
     * @return int[] Get the byte locations of all offsets as an array
     * @since 1.9.0
     */
    public function getByteOffsets(): array {
        return array_map(function($offset) {
            /** @var Offset $offset */
            return $offset->getByteLoc();
        }, $this->offsets);
    }

    /**
     * @return int Size of the list
     * @since 1.9.0
     */
    public function getSize(): int {
        return count($this->offsets);
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * @return Offset|null The last item in the list
     * @since 1.9.0
     */
    private function getLast(): ?Offset {
        return $this->getSize() > 0 ? $this->offsets[$this->getSize() - 1] : null;
    }

}