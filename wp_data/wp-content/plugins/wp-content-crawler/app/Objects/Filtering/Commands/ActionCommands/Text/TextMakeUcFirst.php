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


use Exception;
use Illuminate\Support\Str;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractTextActionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;
use WPCCrawler\Objects\Html\HtmlTextModifier;

class TextMakeUcFirst extends AbstractTextActionCommand {

    public function getKey(): string {
        return CommandKey::TEXT_MAKE_UC_FIRST;
    }

    public function getName(): string {
        return _wpcc('Make first letter upper case');
    }

    protected function onModifyText(string $text): ?string {
        return Str::ucfirst($text);
    }

    protected function onModifyHtml(string $html): string {
        $modifier = new HtmlTextModifier($html);

        $textModified = false;
        $modifiedHtml = $modifier->modify(function($text) use (&$textModified) {
            // We need to make the first text's first letter uppercase. The other texts should not be changed. To make
            // this happen, we throw an exception to stop the recursion if a text has already been modified. The HTML
            // text modifier visits the nodes from top to bottom. So, the first text that is modified is the first text
            // of the HTML document. By modifying only one text, we achieve what we want, due to the order of
            // text node processing.
            if ($textModified) throw new Exception();

            if (mb_strlen(trim($text)) > 0) {
                $textModified = true;
                return $this->onModifyText($text);
            }

            return $text;
        });

        return $modifiedHtml ?: $html;
    }

}