<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/03/2020
 * Time: 11:57
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Strings;


use WPCCrawler\Objects\Chunk\LengthStrategy\AbstractLengthStrategy;
use WPCCrawler\Objects\Chunk\LengthStrategy\CharLengthStrategy;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Strings\Base\AbstractStringLengthProperty;

class StringCharLengthProperty extends AbstractStringLengthProperty {

    public function getKey(): string {
        return PropertyKey::STR_CHAR_LENGTH;
    }

    public function getName(): string {
        return _wpcc("Character length");
    }

    protected function createLengthStrategy(): AbstractLengthStrategy {
        return new CharLengthStrategy();
    }

}