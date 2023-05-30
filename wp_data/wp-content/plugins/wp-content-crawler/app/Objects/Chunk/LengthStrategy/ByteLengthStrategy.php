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


class ByteLengthStrategy extends CharLengthStrategy {

    /** @var ByteLengthStrategy|null */
    private static $instance;

    /**
     * @return ByteLengthStrategy
     * @since 1.11.1
     */
    public static function getInstance() {
        if (static::$instance === null) {
            static::$instance = new ByteLengthStrategy();
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
        // https://stackoverflow.com/a/9718273/2883487
        return mb_strlen($text, '8bit');
    }

}