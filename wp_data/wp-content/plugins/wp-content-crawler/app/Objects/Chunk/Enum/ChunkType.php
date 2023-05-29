<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/11/2019
 * Time: 09:01
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Chunk\Enum;


use Exception;
use WPCCrawler\Objects\Chunk\LengthStrategy\AbstractLengthStrategy;
use WPCCrawler\Objects\Chunk\LengthStrategy\ByteLengthStrategy;
use WPCCrawler\Objects\Chunk\LengthStrategy\CharLengthStrategy;
use WPCCrawler\Objects\Chunk\LengthStrategy\WordLengthStrategy;

class ChunkType {

    const T_BYTES = 1;
    const T_CHARS = 2;
    const T_WORDS = 3;

    /** @var array|null */
    private static $classMap = null;

    /**
     * @param int $chunkType One of the constants defined in this class
     * @return AbstractLengthStrategy An {@link ItemLength} instance
     * @throws Exception If $chunkType is not valid
     * @since 1.9.0
     */
    public static function getLengthStrategyForType(int $chunkType): AbstractLengthStrategy {
        if (static::$classMap === null) {
            static::$classMap = [
                static::T_BYTES => ByteLengthStrategy::class,
                static::T_CHARS => CharLengthStrategy::class,
                static::T_WORDS => WordLengthStrategy::class,
            ];
        }

        // If the given type does not exist, return null.
        if (!isset(static::$classMap[$chunkType])) {
            throw new Exception("Chunk type does not exist");
        }

        /** @var AbstractLengthStrategy $clz */
        $clz = static::$classMap[$chunkType];
        return $clz::getInstance();
    }
}