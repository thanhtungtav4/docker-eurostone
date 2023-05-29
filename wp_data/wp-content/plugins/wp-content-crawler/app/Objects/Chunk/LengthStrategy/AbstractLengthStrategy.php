<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/11/2019
 * Time: 09:11
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Chunk\LengthStrategy;


abstract class AbstractLengthStrategy {

    /** @var AbstractLengthStrategy[] */
    protected static $instances = [];

    /**
     * Get the instance
     *
     * @return static
     * @since 1.9.0
     */
    abstract public static function getInstance();

    /**
     * Get length of a text
     *
     * @param string $text Text whose length is wanted
     * @return int The length of the text
     * @since 1.9.0
     */
    abstract public function getLengthFor(string $text): int;

    /**
     * Get byte offsets that will be used to cut the text to satisfy maximum length constraint
     *
     * @param string $text           Text that will be cut
     * @param int    $maxLength      Maximum length that should be satisfied by offsets. In other words, when the text
     *                               is divided into parts using the resultant offsets, each part can only have this
     *                               number of items in it.
     * @param int    $textLength     Length of $text, calculated by using {@link getLengthFor()}.
     * @param int    $minOffsetCount Minimum number of offsets that must be returned by this method.
     * @return int[] An array of byte locations indicating the cut locations for the given text such that each part
     *                               created with cuts satisfy $maxLength constraint.
     * @since 1.9.0
     */
    abstract public function getByteOffsetsForCuts(string $text, int $maxLength, int $textLength, int $minOffsetCount): array;

    /*
     * PROTECTED METHODS
     */

    /**
     * Validate offsets. If the offsets are not valid, $offsets array is set to an empty array.
     *
     * @param int[] $offsets An integer array storing the byte offsets of cut locations
     * @param int   $minOffsetCount Minimum number of offsets that there should be
     * @since 1.9.0
     */
    protected function validateByteOffsets(array &$offsets, int $minOffsetCount): void {
        // If not enough number of offsets is found, then invalidate the offsets. We need at least a
        // certain number of offsets so that the text will be divided into parts satisfying the max
        // length constraint. If this condition is not met, invalidate the offsets so that a more
        // detailed search is performed to find offsets that will satisfy the maximum length constraint.
        if (sizeof($offsets) < $minOffsetCount) {
            $offsets = [];
        }
    }
}