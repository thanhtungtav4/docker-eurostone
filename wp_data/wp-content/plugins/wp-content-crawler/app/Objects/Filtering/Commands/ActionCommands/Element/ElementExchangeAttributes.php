<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 15/04/2022
 * Time: 20:16
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
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\MultipleExchangeAttrsWithLabel;

class ElementExchangeAttributes extends AbstractBotActionCommand {

    public function getKey(): string {
        return CommandKey::ELEMENT_EXCHANGE_ATTRS;
    }

    public function getName(): string {
        return _wpcc('Exchange attributes');
    }

    protected function createViews(): ViewDefinitionList {
        return (new ViewDefinitionList())
            ->add((new ViewDefinition(MultipleExchangeAttrsWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Attribute names'))
                ->setVariable(ViewVariableName::INFO, _wpcc('Enter the names of the attributes whose values will'
                    . ' be exchanged with each other. If the value of attribute 2 does not exist, the values will not'
                    . ' be exchanged.'))
                ->setVariable(ViewVariableName::NAME, InputName::ELEMENT_ATTRS))
            ;
    }

    protected function onExecuteCommand($node): void {
        if (!($node instanceof Crawler)) return;

        $child = $node->getNode(0);
        if (!($child instanceof DOMElement)) return;

        $attrNameData = $this->getAttributeNamesOption();
        foreach($attrNameData as $item) {
            if (!is_array($item)) continue;

            $firstAttrName  = $item[SettingInnerKey::ATTRIBUTE_1] ?? null;
            $secondAttrName = $item[SettingInnerKey::ATTRIBUTE_2] ?? null;
            if (!is_string($firstAttrName) || !is_string($secondAttrName)) continue;

            $this->exchangeSingle($child, $firstAttrName, $secondAttrName);
        }
    }

    /**
     * Replaces the values of two attributes of an element with each other. If the second attribute's value does not
     * exist, the exchange does not happen.
     *
     * @param DOMElement $element        The element whose attribute values will be exchanged
     * @param string     $firstAttrName  The first attribute's name
     * @param string     $secondAttrName The second attribute's name
     * @since 1.12.0
     */
    protected function exchangeSingle(DOMElement $element, string $firstAttrName, string $secondAttrName): void {
        $firstAttrName  = trim($firstAttrName);
        $secondAttrName = trim($secondAttrName);
        if ($firstAttrName === '' || $secondAttrName === '') return;

        // Get values of the attributes
        $firstAttrVal  = $element->getAttribute($firstAttrName);
        $secondAttrVal = $element->getAttribute($secondAttrName);

        // If the second attribute's value is an empty string, stop.
        if($secondAttrVal === "") return;

        // Exchange the values
        $element->setAttribute($firstAttrName,  $secondAttrVal);
        $element->setAttribute($secondAttrName, $firstAttrVal);
    }

    /*
     *
     */

    /**
     * @return array The values assigned to the "attribute names" option. An array of arrays. Each inner array is
     *               structured as:
     *                  [{@link SettingInnerKey::ATTRIBUTE_1} => string, {@link SettingInnerKey::ATTRIBUTE_2} => string]
     * @since 1.12.0
     */
    protected function getAttributeNamesOption(): array {
        $attrNames = $this->getOption(InputName::ELEMENT_ATTRS);
        return is_array($attrNames)
            ? $attrNames
            : [];
    }
}