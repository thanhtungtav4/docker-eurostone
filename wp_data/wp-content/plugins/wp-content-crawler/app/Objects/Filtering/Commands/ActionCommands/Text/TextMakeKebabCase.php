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
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractTextActionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

class TextMakeKebabCase extends AbstractTextActionCommand {

    public function getKey(): string {
        return CommandKey::TEXT_MAKE_KEBAB_CASE;
    }

    public function getName(): string {
        return _wpcc('Make kebab case');
    }

    protected function onModifyText(string $text): ?string {
        return Str::kebab($text);
    }

}