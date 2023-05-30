<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/06/2020
 * Time: 22:11
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\ValueType;


use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;

/**
 * Provides methods to cast values into different types
 *
 * @since 1.11.0
 */
class TypeCaster {

    /** @var TypeCaster|null */
    private static $instance = null;

    /**
     * @return TypeCaster
     * @since 1.11.0
     */
    public static function getInstance(): TypeCaster {
        if (static::$instance === null) static::$instance = new TypeCaster();
        return static::$instance;
    }

    /**
     * Cast a value to string
     *
     * @param mixed $value
     * @return string|null If the value is null, returns null. Otherwise, returns string equivalent of the value.
     * @since 1.11.0
     */
    public function toString($value): ?string {
        if (is_a($value, Crawler::class)) {
            // TODO: Test that this does not fail when the given Crawler is not a dummy crawler
            $bot = new DummyBot([]);
            return $bot->getContentFromDummyCrawler($value);
        }

        return $value === null ? null : (string) $value;
    }

    /**
     * Cast a value to a numeric value
     *
     * @param mixed $value
     * @return string|null If the value is null, returns null. Otherwise, returns the numeric equivalent of the value.
     * @since 1.11.0
     */
    public function toNumeric($value): ?string {
        // If the value is null or an object, we cannot cast it. Return null.
        if ($value === null || is_object($value)) return null;

        // If the value is already numeric, return it as-is.
        if (is_numeric($value)) return (string) $value;

        // The value is not numeric. We can try to get rid of non-numeric parts of the value but it is not necessary
        // for now. Return null.
        return null;
    }

    /**
     * @param mixed $value
     * @return float|null If the value is null or not numeric, returns null. Otherwise, returns the float equivalent of
     *                    the value.
     * @since 1.11.0
     */
    public function toFloat($value): ?float {
        if ($value === null || !is_numeric($value)) return null;

        return (float) $value;
    }

    /**
     * @param mixed $value
     * @return int|null If the value is null or not numeric, returns null. Otherwise, returns the integer equivalent of
     *                  the value. The value is rounded via {@link PHP_ROUND_HALF_UP} mode.
     * @since 1.11.0
     */
    public function toInteger($value): ?int {
        if ($value === null || !is_numeric($value)) return null;

        return (int) round((float) $value);
    }
}