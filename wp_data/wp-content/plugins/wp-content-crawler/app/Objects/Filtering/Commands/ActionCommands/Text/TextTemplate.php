<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 18/02/2021
 * Time: 12:42
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text;


use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractTextActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Enums\CommandShortCodeName;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Traits\ShortCodeReplacer;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\ShortCodeButtonsWithLabelForTemplateCmd;
use WPCCrawler\Objects\Views\TextAreaWithLabel;

class TextTemplate extends AbstractTextActionCommand {

    use ShortCodeReplacer;

    public function getKey(): string {
        return CommandKey::TEXT_TEMPLATE;
    }

    public function getName(): string {
        return _wpcc('Template');
    }

    protected function hasTreatAsHtmlOption(): bool {
        return false;
    }

    protected function createViews(): ?ViewDefinitionList {
        $list = parent::createViews();
        if ($list === null) {
            $list = new ViewDefinitionList();
        }

        $list
            // Add the short code buttons to the top of the view list
            ->prepend((new ViewDefinition(ShortCodeButtonsWithLabelForTemplateCmd::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Short codes'))
                ->setVariable(ViewVariableName::INFO,  _wpcc("Short codes that can be used in the template. You 
                    can hover over the short codes to see what they do. You can click to the short code buttons to copy 
                    the short codes. Then, you can paste the short codes into the template to include them. They will be 
                    replaced with their actual values."))
            )
            ->add((new ViewDefinition(TextareaWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Template'))
                ->setVariable(ViewVariableName::INFO,  _wpcc("Enter your template. What you define here will be 
                    used as the new value of the text. The original value will be replaced with this template."))
                ->setVariable(ViewVariableName::NAME,  InputName::TEMPLATE)
                ->setVariable(ViewVariableName::ROWS,  4));

        return $list;
    }

    protected function onModifyText(string $text): ?string {
        // Get the template. If the template does not exist, return the original value.
        $template = $this->getTemplate();
        if ($template === null) {
            return $text;
        }

        // The template exists. Replace the ITEM short code with the original text.
        $this->replaceShortCode($template, CommandShortCodeName::ITEM, $text);

        // Return the template as the modified text
        return $template;
    }

    /*
     * HELPERS
     */

    /**
     * @return string|null Value of the template option
     * @since 1.11.0
     */
    protected function getTemplate(): ?string {
        $template = $this->getStringOption(InputName::TEMPLATE);
        return $template === null || $template === ''
            ? null
            : $template;
    }

}