<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/11/2019
 * Time: 09:17
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Chunk\LengthStrategy;


use WPCCrawler\Objects\Chunk\Enum\ChunkRegex;
use WPCCrawler\Objects\Chunk\Offset\ClosestOffsetFinder;

class CharLengthStrategy extends AbstractLengthStrategy {

    /** @var CharLengthStrategy|null */
    private static $instance;

    /**
     * @return CharLengthStrategy
     * @since 1.11.1
     */
    public static function getInstance() {
        if (static::$instance === null) {
            static::$instance = new CharLengthStrategy();
        }

        return static::$instance;
    }

    /**
     * Get length of a text
     *
     * @param string $text Text whose length is wanted
     * @return int The length of the text
     * @since 1.9.0
     */
    public function getLengthFor(string $text): int {
        return mb_strlen($text);
    }

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
     *               created with cuts satisfy $maxLength constraint.
     * @since 1.9.0
     */
    public function getByteOffsetsForCuts(string $text, int $maxLength, int $textLength, int $minOffsetCount): array {
        $offsets = [];

        // These regular expressions are used in the given order. They gradually get more granular. In other words, the
        // quality of matches gets lower with the increase of the indices.
        $regexes = [
            0 => ChunkRegex::NEW_LINE_MATCH_REGEX,

            // Try if the chars at the ends of the sentences provide us good division locations
            1 => ChunkRegex::SENTENCE_END_MATCH_REGEX,

            // Match every word here. Before trying to match every single character, trying words is more
            // appropriate since it will give more meaningful results.
            2 => ChunkRegex::WORD_MATCH_REGEX,

            // We could not find a good division location. Match every character. This is bad for translation. However,
            // it is better than no translation at all.
            3 => ChunkRegex::CHAR_MATCH_REGEX
        ];

        $tryCount = 0;
        while(!$offsets) {
            // Find the offset locations
            if (isset($regexes[$tryCount])) {
                preg_match_all($regexes[$tryCount], $text, $matches, PREG_OFFSET_CAPTURE);

            } else {
                // This is the backup plan. This is really bad. Here, we assume that there is a character every
                // $maxLength away from each other, which is not the case because not every character is 1 byte. This
                // will probably never be called. However, it is good to have a backup plan.
                /** @var int[] $offsets */
                $offsets[] = $maxLength;
                while(true) {
                    $newOffset = $offsets[sizeof($offsets) - 1] + $maxLength;
                    if($newOffset >= $textLength) break;

                    $offsets[] = $newOffset;
                };

                break;
            }

            if(isset($matches) && $matches && $matches = $matches[0]) {
                $finder = new ClosestOffsetFinder($text, $this, $matches, $maxLength);
                $offsets = $finder->find()->getByteOffsets();
                $this->validateByteOffsets($offsets, $minOffsetCount);
            }

            // Invalidate the matches.
            $matches = [];

            // Increase the try count.
            $tryCount++;
        }

        return $offsets;
    }

}