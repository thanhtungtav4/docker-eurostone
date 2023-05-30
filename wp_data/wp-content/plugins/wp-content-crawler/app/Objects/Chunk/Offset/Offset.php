<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 14/11/2019
 * Time: 21:56
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Chunk\Offset;


class Offset {

    /**
     * @var int The location of this offset in terms of bytes. The offset is relative to the beginning of the text.
     *      This offset specifies where to cut the text.
     */
    private $byteLoc;

    /** @var int The location of this offset in terms of length. The offset is relative to the beginning of the text. */
    private $lengthLoc;

    /** @var Offset|null The offset that comes before this offset. In other words, this offset's predecessor. */
    private $prevOffset;

    /**
     * @param int         $byteLoc    See {@link byteLoc}
     * @param int         $lengthLoc  See {@link lengthLoc}
     * @param Offset|null $prevOffset See {@link prevOffset}
     * @since 1.9.0
     */
    public function __construct(int $byteLoc, int $lengthLoc, ?Offset $prevOffset = null) {
        $this->byteLoc      = $byteLoc;
        $this->lengthLoc    = $lengthLoc;
        $this->prevOffset   = $prevOffset;
    }

    /**
     * @return int See {@link byteLoc}
     * @since 1.9.0
     */
    public function getByteLoc(): int {
        return $this->byteLoc;
    }

    /**
     * @return int See {@link lengthLoc}
     * @since 1.9.0
     */
    public function getLengthLoc(): int {
        return $this->lengthLoc;
    }

    /**
     * @return Offset|null See {@link prevOffset}
     * @since 1.9.0
     */
    public function getPrevOffset(): ?Offset {
        return $this->prevOffset;
    }

    /**
     * @param Offset|null $prevOffset See {@link prevOffset}
     * @return Offset
     * @since 1.9.0
     */
    public function setPrevOffset(?Offset $prevOffset): Offset {
        $this->prevOffset = $prevOffset;
        return $this;
    }

    /**
     * @return int The difference in length between this offset and the previous one.
     * @since 1.9.0
     */
    public function getLengthDiff(): int {
        if (!$this->prevOffset) return $this->getLengthLoc();

        return $this->getLengthLoc() - $this->prevOffset->getLengthLoc();
    }

}