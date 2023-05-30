<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/11/2019
 * Time: 09:15
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Chunk\LengthStrategy;


use WPCCrawler\Objects\Chunk\Enum\ChunkRegex;
use WPCCrawler\Objects\Chunk\Offset\ClosestOffsetFinder;

class WordLengthStrategy extends AbstractLengthStrategy {

    /** @var WordLengthStrategy|null */
    private static $instance;

    /**
     * @return WordLengthStrategy
     * @since 1.11.1
     */
    public static function getInstance() {
        if (static::$instance === null) {
            static::$instance = new WordLengthStrategy();
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
        $split = preg_split(ChunkRegex::WORD_MATCH_REGEX, $text);
        return is_array($split) ? count($split) : 1;
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
     *                               created with cuts satisfy $maxLength constraint.
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
        ];

        $tryCount = 0;
        while(!$offsets) {
            // Find the offset locations
            if (isset($regexes[$tryCount])) {
                preg_match_all($regexes[$tryCount], $text, $matches, PREG_OFFSET_CAPTURE);

                if(isset($matches) && $matches && $matches = $matches[0]) {
                    $finder = new ClosestOffsetFinder($text, $this, $matches, $maxLength);
                    $offsets = $finder->find()->getByteOffsets();
                    $this->validateByteOffsets($offsets, $minOffsetCount);
                }

            } else {
                preg_match_all(ChunkRegex::WORD_MATCH_REGEX, $text, $matches, PREG_OFFSET_CAPTURE);

                $offsets = $this->findClosestOffsetsFromMatches($matches, $maxLength);
                $this->validateByteOffsets($offsets, $minOffsetCount);
                break;
            }

            // Invalidate the matches.
            $matches = [];

            // Increase the try count.
            $tryCount++;
        }

        return $offsets;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Finds offsets that can be used to divide a text so that a max-length constraint is satisfied.
     *
     * @param array $matches   An array of arrays. Each inner array must have 2 values. Index 0 stores the matched
     *                         text, and index 1 stores the offset of the matched text.
     * @param int   $maxLength Maximum length of text between two offsets.
     * @return int[] The offsets
     */
    private function findClosestOffsetsFromMatches(array $matches, int $maxLength): array {
        $offsets = [];

        // Each match is a word. So, we can directly use the indices to understand how many words we get. Also, we know
        // at most how many groups we can create. Each match contains the byte offset of the word. So, we can quickly
        // calculate using these information.

        $matchCount = sizeof($matches);
        $increment = $maxLength;
        $completeBatchCount = floor($matchCount/$maxLength);

        for($i = 1; $i <= $completeBatchCount; $i++) {
            $currentMatch = $matches[$i * $increment - 1];

            // Add the match's byte length since we want to take the end location of the word as the offset, to cut the
            // word from its end.
            $offsets[] = $currentMatch[1] + mb_strlen($currentMatch[0], '8bit');
        }

        return $offsets;
    }
}