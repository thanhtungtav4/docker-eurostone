<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 14/11/2019
 * Time: 22:07
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Chunk\Offset;


use WPCCrawler\Objects\Chunk\LengthStrategy\AbstractLengthStrategy;

class ClosestOffsetFinder {

    /** @var string The text in which the offsets will be found */
    private $text;

    /** @var OffsetList|null The list storing the found offsets */
    private $offsetList = null;

    /** @var AbstractLengthStrategy Strategy that will be used to find the lengths. */
    private $strategy;

    /**
     * @var array $matches An array of arrays. Each inner array must have 2 values. Index 0 stores the matched text,
     *      and index 1 stores the offset of the matched text. So, this array stores the byte locations of certain
     *      texts inside the main {@link $text}. The values in this array will be used to calculate the length offsets.
     */
    private $matches;

    /**
     * @var int Maximum length of text between two offsets. This will be used to come up with the most suitable offsets
     *      satisfying this condition.
     */
    private $maxLength;

    /*
     *
     */

    /**
     * @var array An associative array where keys are the byte offsets and the values are instances of {@link Offset}s.
     *            The purpose of this is to cache the length locations of byte locations. The array is ordered by byte
     *            locations in ascending. In other wrods, the smallest byte location is at 0th index while the biggest
     *            is the last item of the array.
     */
    private $lengthOffsetCache = [];

    /**
     * @param string                 $text      See {@link text}
     * @param AbstractLengthStrategy $strategy  See {@link strategy}
     * @param array                  $matches   See {@link matches}
     * @param int                    $maxLength See {@link maxLength}
     * @since 1.9.0
     */
    public function __construct(string $text, AbstractLengthStrategy $strategy, array $matches, int $maxLength) {
        $this->text      = $text;
        $this->strategy  = $strategy;
        $this->matches   = $matches;
        $this->maxLength = $maxLength;
    }

    /**
     * Find offsets that satisfies the given {@link maxLength} condition.
     *
     * @return OffsetList The list. If the condition could not be satisfied, the list will be empty.
     * @since 1.9.0
     */
    public function find(): OffsetList {
        // If the list already exists, return it
        if ($this->offsetList) return $this->offsetList;

        $this->offsetList = new OffsetList();

        $matchCount = count($this->matches);

        // This keeps the total length between two offsets. We will use this to decide if we should an offset to the
        // list.
        $currentLengthDiff = 0;

        // Stores if the list should be invalidated or not.
        $invalidate = false;

        $prevOffset = null;
        for($i = 0; $i < $matchCount; $i++) {
            // Create an offset for current match
            $offset = $this->createOffsetFromMatch($this->matches[$i]);

            // Now find out if we should add this to the list

            // If the total length will be greater than the maximum length when this offset is added, add the previous
            // offset to the list to satisfy the max length constraint.
            if ($offset->getLengthDiff() + $currentLengthDiff > $this->maxLength) {
                // - If there is no previous offset, stop. We cannot satisfy the maximum length constraint.
                // - If the current length difference without the current offset is greater than the max length, then we
                //   cannot satisfy the max length constraint, because a single item's length is greater than the
                //   constraint. Hence, it is impossible to satisfy the constraint.
                if (!$prevOffset || $currentLengthDiff > $this->maxLength) {
                    $invalidate = true;
                    break;
                }

                // Add the previous offset to the list and reset the current length difference
                $this->offsetList->add($prevOffset);
                $currentLengthDiff = 0;
            }

            // Add the length difference of the current offset since this offset is not added to the list yet
            $currentLengthDiff += $offset->getLengthDiff();
            $prevOffset = $offset;
        }

        // If the current length difference, which currently includes the length difference using the last offset, does
        // not satisfy the maximum length constraint, invalidate the list.
        if (!$invalidate && $currentLengthDiff > $this->maxLength) {
            $invalidate = true;
        }

        // If the list should be invalidated, make the list an empty array.
        if ($invalidate) {
            $this->offsetList->clear();
        }

        return $this->offsetList;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * @param array $match An item of {@link matches}
     * @return Offset The offset created from the given match
     * @since 1.9.0
     */
    private function createOffsetFromMatch(array $match) {
        $currentText        = $match[0];
        $currentByteOffset  = $match[1] + mb_strlen($currentText, '8bit');
        $closestPrevOffset  = $this->findClosestOffsetFromCache($currentByteOffset);

        // We need to find the length offset using the byte offset. Then, we need to create an Offset instance with
        // these data and add it to the list.

        // If there is a previous offset, it is enough to calculate the length of the text that comes after that offset.
        // We do this in order not to calculate the length offsets from the start of the text over and over again. We
        // just calculate the length of the difference.
        $textSegment = $closestPrevOffset !== null
            ? mb_strcut($this->text, $closestPrevOffset->getByteLoc(), $currentByteOffset - $closestPrevOffset->getByteLoc())
            : mb_strcut($this->text, 0, $currentByteOffset);

        $textLength = $this->strategy->getLengthFor($textSegment);
        $currentLengthOffset = $closestPrevOffset
            ? $closestPrevOffset->getLengthLoc() + $textLength
            : $textLength;

        // Create an offset
        $offset = new Offset($currentByteOffset, $currentLengthOffset, $closestPrevOffset);

        // Cache the offset so that we can use it later
        $this->cacheOffset($offset);

        return $offset;
    }

    /**
     * Add an {@link Offset} to the cache
     *
     * @param Offset $offset Offset to be added to the cache
     * @since 1.9.0
     */
    private function cacheOffset(Offset $offset): void {
        // If a cache with the given offset's byte location exists, no need to add it again. Stop.
        if (isset($this->lengthOffsetCache[$offset->getByteLoc()])) {
            return;
        }

        // Find out the last item's byte location so that we can understand if we need to reorder the cache array. The
        // cache array should be ordered according to the keys in ascending.
        $lastByteLoc = 0;
        if (count($this->lengthOffsetCache) > 0) {
            /** @var Offset $lastOffset */
            $lastOffset = array_values(array_slice($this->lengthOffsetCache, -1))[0];
            $lastByteLoc = $lastOffset->getByteLoc();
        }

        $this->lengthOffsetCache[$offset->getByteLoc()] = $offset;

        // If the new offset breaks the order, then reorder the cache in ascending according to the byte locations
        if ($lastByteLoc > $offset->getByteLoc()) {
            ksort($this->lengthOffsetCache);
        }
    }

    /**
     * Find an offset that has the closest to the given byte location and that has smaller byte location than the given
     * byte location. In other words, find the closest smaller offset by using a byte location.
     *
     * @param int $byteLoc The resultant offset will be closest to this byte location.
     * @return Offset|null The offset that is smaller than and the closest to the given $byteLoc. If an offset
     *                     satisfying these conditions is not found, then null will be returned.
     * @since 1.9.0
     */
    private function findClosestOffsetFromCache(int $byteLoc): ?Offset {
        // If the cache is empty return null
        if (!$this->lengthOffsetCache) return null;

        $locs       = array_keys($this->lengthOffsetCache);
        $locCount   = count($locs);

        if ($locCount === 1) {
            /** @var Offset $firstOffset */
            $firstOffset = array_values(array_slice($this->lengthOffsetCache, 0, 1))[0];

            return $firstOffset->getByteLoc() < $byteLoc ? $firstOffset : null;
        }

        // The cache is sorted according to byte locations. So, we can use binary search to find the closest offset.
        $minIndex   = 0;
        $maxIndex   = $locCount - 1;
        $targetLoc  = $byteLoc;
        $foundIndex = null;
        $maxLoop    = (int) ceil(log($locCount, 2)) + 1;

        for($i = 0; $i < $maxLoop; $i++) {
            if ($maxIndex - $minIndex < 2) {
                // If the location of maxIndex is less than the target location, what we want is that item. Since we
                // get the item with index (foundIndex - 1), we add 1 to the index so that this method returns
                // locs[maxIndex].
                $foundIndex = $locs[$maxIndex] > $targetLoc ? $maxIndex : $maxIndex + 1;
                break;
            }

            $currentIndex   = (int) floor(($minIndex + $maxIndex) / 2);
            $currentLoc     = $locs[$currentIndex];

            if ($currentLoc < $targetLoc) {
                $minIndex = $currentIndex;

            } else if($currentLoc > $targetLoc) {
                $maxIndex = $currentIndex;

            } else {
                $foundIndex = $currentIndex;
                break;
            }

        }

        // If there is no viable index found, then return null. If the found index is the first item's index, it means
        // there is no smaller offset. So, return null.
        if($foundIndex === null || $foundIndex === 0) return null;

        // Return the closest smaller offset
        return $this->lengthOffsetCache[$locs[$foundIndex - 1]];
    }
}