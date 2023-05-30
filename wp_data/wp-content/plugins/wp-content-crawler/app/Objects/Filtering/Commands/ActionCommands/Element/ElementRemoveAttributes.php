<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 15/04/2022
 * Time: 19:31
 *
 * @since 1.12.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Element;

use DOMElement;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractBotActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\TextAreaWithLabel;

class ElementRemoveAttributes extends AbstractBotActionCommand {

    public function getKey(): string {
        return CommandKey::ELEMENT_REMOVE_ATTRS;
    }

    public function getName(): string {
        return _wpcc('Remove attributes');
    }

    protected function createViews(): ViewDefinitionList {
        return (new ViewDefinitionList())
            ->add((new ViewDefinition(TextAreaWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Attribute names'))
                ->setVariable(ViewVariableName::INFO, sprintf(_wpcc('Define the names of the attributes that'
                    . ' will be removed from the element. To enter multiple attributes, separate them with commas. For'
                    . ' example, %1$s defines only one attribute, while %2$s defines two attributes.'),
                    '<span class="highlight attribute">attribute1</span>',
                    '<span class="highlight attribute">attribute1, attribute2</span>'
                ))
                ->setVariable(ViewVariableName::NAME, InputName::ELEMENT_ATTR)
                ->setVariable(ViewVariableName::ROWS, 2)
                ->setVariable(ViewVariableName::TYPE, 'text'));
    }

    protected function onExecuteCommand($node): void {
        if (!($node instanceof Crawler)) return;

        $child = $node->getNode(0);
        if (!($child instanceof DOMElement)) return;

        // Remove the attributes
        $attrNames = $this->getAttributeNamesOption();
        foreach($attrNames as $attrName) {
            $child->removeAttribute($attrName);
        }
    }

    /*
     *
     */

    /**
     * @return string[] Names of the attributes that should be removed from the element
     * @since 1.12.0
     */
    protected function getAttributeNamesOption(): array {
        $attrNames = $this->getOption(InputName::ELEMENT_ATTR);
        if (!is_string($attrNames)) return [];

        $separated = array_filter(array_map(function(string $attrName) {
            $trimmed = trim($attrName);
            return $trimmed !== ''
                ? $trimmed
                : null;
        }, explode(',', $attrNames)));

        return array_unique($separated);
    }
}