<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 19/11/2020
 * Time: 13:23
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text;


use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractTextActionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

class TextClear extends AbstractTextActionCommand {

    public function getKey(): string {
        return CommandKey::TEXT_CLEAR;
    }

    public function getName(): string {
        return _wpcc('Clear');
    }

    protected function hasTreatAsHtmlOption(): bool {
        return false;
    }

    protected function onModifyText(string $text): ?string {
        return '';
    }
}