<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 12/07/2020
 * Time: 10:17
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text;


use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractTextActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Html\EmptyHtmlTagRemover;
use WPCCrawler\Objects\Views\CheckboxWithLabel;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\InputWithLabel;

class TextRemoveEmptyHtmlElements extends AbstractTextActionCommand {

    public function getKey(): string {
        return CommandKey::TEXT_REMOVE_EMPTY_HTML_ELEMENTS;
    }

    public function getName(): string {
        return _wpcc('Remove empty HTML elements');
    }

    protected function hasTreatAsHtmlOption(): bool {
        return false;
    }

    protected function createViewDefinitionList(): ?ViewDefinitionList {
        return (new ViewDefinitionList())
            ->add((new ViewDefinition(CheckboxWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Remove comments?'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('Check this if the HTML comments should be removed.'))
                ->setVariable(ViewVariableName::NAME,  InputName::REMOVE_COMMENTS))

            ->add((new ViewDefinition(InputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Excluded tag names'))
                ->setVariable(ViewVariableName::INFO,  sprintf(
                    _wpcc('Comma-separated tag names that should not be removed even if they are empty. E.g. %1$s.
                        Note that HTML tags that should not have any content by default, such as %2$s, are already excluded.'),
                    '<b>span, i, strong</b>',
                    '<span class="highlight selector">img</span>'
                ))
                ->setVariable(ViewVariableName::NAME,  InputName::EXCLUDED_TAGS)
                ->setVariable(ViewVariableName::TYPE,  'text'))
            ;
    }

    protected function onModifyText(string $text): ?string {
        if ($text === '') return $text;

        $remover = new EmptyHtmlTagRemover($text, $this->getExcludedTagNames(), $this->shouldRemoveComments());
        return $remover->removeEmptyTags();
    }

    /*
     *
     */

    /**
     * @return bool True if the HTML comments should be removed from HTML code
     * @since 1.11.0
     */
    protected function shouldRemoveComments(): bool {
        return $this->getCheckboxOption(InputName::REMOVE_COMMENTS);
    }

    /**
     * @return string[] Names of the tags that should not be removed even if they are empty
     * @since 1.11.0
     */
    protected function getExcludedTagNames(): array {
        $commaSeparatedNames = $this->getOption(InputName::EXCLUDED_TAGS);
        if ($commaSeparatedNames === null) return [];

        $exploded = explode(',', $commaSeparatedNames);

        // Remove whitespace surrounding the tag names
        $trimmed = array_map(function ($tagName) {
            return trim($tagName);
        }, $exploded);

        // Remove empty tag names
        $filtered = array_filter($trimmed, function($tagName) {
            return $tagName !== '';
        });

        // Make it a sequential array just in case.
        return array_values($filtered);
    }
}