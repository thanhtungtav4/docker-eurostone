<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/07/2020
 * Time: 18:46
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text;


use Illuminate\Support\Str;
use WPCCrawler\Objects\Chunk\LengthStrategy\AbstractLengthStrategy;
use WPCCrawler\Objects\Chunk\LengthStrategy\CharLengthStrategy;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\Base\AbstractTextLimitLengthCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

class TextLimitChars extends AbstractTextLimitLengthCommand {

    public function getKey(): string {
        return CommandKey::TEXT_LIMIT_CHARS;
    }

    public function getName(): string {
        return _wpcc('Limit characters');
    }

    protected function getLengthOptionName(): string {
        return _wpcc('Maximum character count');
    }

    protected function getLengthOptionDescription(): string {
        return _wpcc('Define the maximum number of characters that can be in the text.');
    }

    protected function onCutText(string $text, int $length, string $endText): string {
        return Str::limit($text, $length, $endText);
    }

    protected function createLengthStrategy(): AbstractLengthStrategy {
        return new CharLengthStrategy();
    }

}