<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 15/11/2019
 * Time: 01:58
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Chunk\Enum;


class ChunkRegex {

    /**
     * @var string Regex that matches the ends of the words. This just matches the spaces. If a language does not
     *      contain spaces, then this regex is of no use. This regex is used for spinning APIs, which can only spin
     *      English. So, this is good for now. The regex /[^\p{L}\p{N}\']+/u matches the words in many languages, but
     *      it also counts HTML tags, their attribute names and values, and tags' opening and closing characters as
     *      words as well. Therefore, it is not useful for now. Spinning APIs count words by separating them from
     *      spaces. Therefore, we do so as well. Counting words inaccurately high results in unnecessary API calls,
     *      which is costly. So, we count words by separating the text from spaces.
     * @see https://www.php.net/manual/en/function.str-word-count.php#107363
     */
    const WORD_MATCH_REGEX = "/\s+/u";

    /** @var string Regex that matches the new line characters */
    const NEW_LINE_MATCH_REGEX = '/\n/u';

    /**
     * @var string Regex that matches the characters generally placed at the end of the sentences. Matches:
     *             ..., !, ?, ., :, ", ', ], ), } etc
     */
    const SENTENCE_END_MATCH_REGEX = '/\.{2,}|[.?!:][]\"\')}]*/u';

    /** @var string Regex that matches every character */
    const CHAR_MATCH_REGEX = '/.|\n/u';

}