<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/12/2018
 * Time: 15:22
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects;


class ShortCodeButton {

    /** @var ShortCodeButton[] */
    private static $shortCodeButtons = [];

    /** @var string */
    private $code;

    /** @var string|null */
    private $description;

    /**
     * @param string      $code        Short code without brackets.
     * @param string|null $description Description of the short code.
     */
    private function __construct(string $code, ?string $description = null) {
        $this->code = $code;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCode(): string {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description ?: '';
    }

    /**
     * @return string Short code encapsulated by brackets. E.g. if the short code is "wcc-item", this will return
     *                "[wcc-item]"
     * @since 1.8.0
     */
    public function getCodeWithBrackets(): string {
        return "[{$this->getCode()}]";
    }

    /*
     * STATIC METHODS
     */

    /**
     * Get a short code button. This method caches the buttons using their "code" as the key. If a short code button
     * was created before for the given code, it will be returned from the cache. Otherwise, a new instance will be
     * created.
     *
     * @param string      $code        See {@link ShortCodeButton::__construct}
     * @param null|string $description See {@link ShortCodeButton::__construct}
     * @param bool        $fresh       True if a new fresh instance should be returned even if the code exists in the
     *                                 cache.
     * @return ShortCodeButton
     * @since 1.8.0
     */
    public static function getShortCodeButton(string $code, ?string $description = null, bool $fresh = false): ShortCodeButton {
        if (!isset(static::$shortCodeButtons[$code]) || $fresh) {
            static::$shortCodeButtons[$code] = new ShortCodeButton($code, $description);
        }

        return static::$shortCodeButtons[$code];
    }
}