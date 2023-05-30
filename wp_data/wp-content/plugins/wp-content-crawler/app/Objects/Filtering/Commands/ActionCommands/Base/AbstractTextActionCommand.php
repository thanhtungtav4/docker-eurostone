<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/05/2020
 * Time: 19:59
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base;


use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionFactory;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Html\HtmlTextModifier;
use WPCCrawler\Objects\ValueType\Interfaces\OutputsString;
use WPCCrawler\Objects\ValueType\TypeCaster;

abstract class AbstractTextActionCommand extends AbstractActionCommand implements OutputsString {

    public function getInputDataTypes(): array {
        return [ValueType::T_STRING];
    }

    /**
     * Modify the text
     *
     * @param string $text The text that should be modified
     * @return string|null The modified text
     * @since 1.11.0
     */
    abstract protected function onModifyText(string $text): ?string;

    /**
     * Create the views of this text action command.
     *
     * @return ViewDefinitionList|null
     * @since 1.11.0
     */
    protected function createViewDefinitionList(): ?ViewDefinitionList {
        return null;
    }

    /**
     * @return bool True if this command should have "treat as HTML" option. Otherwise, false.
     * @since 1.11.0
     */
    protected function hasTreatAsHtmlOption(): bool {
        return true;
    }

    protected function createViews(): ?ViewDefinitionList {
        // Let the child create a view definition list
        $list = $this->createViewDefinitionList();

        // If "treat as HTML" option should be added, add it to the list
        if ($this->hasTreatAsHtmlOption()) {
            if ($list === null) $list = new ViewDefinitionList();

            $viewDefinitionFactory = ViewDefinitionFactory::getInstance();
            $list->add($viewDefinitionFactory->createTreatAsHtmlInput());
        }

        return $list;
    }

    protected function onExecute($key, $subjectValue) {
        if ($key === null || $subjectValue === null) return $subjectValue;

        // Modify the text
        return $this->isTreatAsHtml()
            ? $this->onModifyHtml($subjectValue)
            : $this->onModifyText($subjectValue);
    }

    /*
     * INTERFACE METHODS
     */

    public function onCastToString($newValue): ?string {
        return TypeCaster::getInstance()->toString($newValue);
    }

    /*
     *
     */

    /**
     * Modify the subject value by treating it as HTML code
     *
     * @param string $html The subject value
     * @return string The modified value
     * @since 1.11.0
     */
    protected function onModifyHtml(string $html): string {
        // Modify only the texts inside the HTML
        $modifier = new HtmlTextModifier($html);

        $modifiedHtml = $modifier->modify(function($text) {
            return $this->onModifyText($text);
        });

        return $modifiedHtml ?: $html;
    }

    /**
     * @return bool True if "treat as HTML" option is checked by the user
     * @since 1.11.0
     */
    protected function isTreatAsHtml(): bool {
        if (!$this->hasTreatAsHtmlOption()) return false;

        return $this->getCheckboxOption(InputName::TREAT_AS_HTML);
    }
}