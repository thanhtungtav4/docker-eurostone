<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 31/03/2020
 * Time: 13:30
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text;


use Illuminate\Support\Str;
use WPCCrawler\Objects\Chunk\LengthStrategy\AbstractLengthStrategy;
use WPCCrawler\Objects\Chunk\LengthStrategy\WordLengthStrategy;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\Base\AbstractTextLimitLengthCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

class TextLimitWords extends AbstractTextLimitLengthCommand {

    public function getKey(): string {
        return CommandKey::TEXT_LIMIT_WORDS;
    }

    public function getName(): string {
        return _wpcc('Limit words');
    }

    protected function getLengthOptionName(): string {
        return _wpcc('Maximum word count');
    }

    protected function getLengthOptionDescription(): string {
        return _wpcc('Define the maximum number of words that can be in the text.');
    }

    protected function onCutText(string $text, int $length, string $endText): string {
        return Str::words($text, $length, $endText);
    }

    protected function createLengthStrategy(): AbstractLengthStrategy {
        return new WordLengthStrategy();
    }

    protected function trimBeforeMeasuringLength(): bool {
        return true;
    }

}