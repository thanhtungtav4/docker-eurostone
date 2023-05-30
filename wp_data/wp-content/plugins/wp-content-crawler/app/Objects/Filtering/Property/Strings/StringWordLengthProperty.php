<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 12/07/2020
 * Time: 18:42
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Strings;


use WPCCrawler\Objects\Chunk\LengthStrategy\AbstractLengthStrategy;
use WPCCrawler\Objects\Chunk\LengthStrategy\WordLengthStrategy;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Strings\Base\AbstractStringLengthProperty;

class StringWordLengthProperty extends AbstractStringLengthProperty {

    public function getKey(): string {
        return PropertyKey::STR_WORD_LENGTH;
    }

    public function getName(): string {
        return _wpcc('Word length');
    }

    protected function createLengthStrategy(): AbstractLengthStrategy {
        return new WordLengthStrategy();
    }
}