<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 12/04/2020
 * Time: 09:06
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Element;

use DOMElement;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionFactory;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractActionProperty;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractProperty;
use WPCCrawler\Objects\Filtering\Property\Objects\CalculationResult;

/**
 * @since 1.11.0
 * @since 1.12.0 Extends {@link AbstractActionProperty} instead of {@link AbstractProperty}
 */
class ElementAttributeValueProperty extends AbstractActionProperty {

    /** @var string|null */
    private $attrName = null;

    /**
     * @var array<int|string, Crawler> Stores the elements whose attributes are extracted, so that the elements can be
     *      retrieved to replace the attributes after the processing is done.
     */
    private $elementMap = [];

    public function getKey(): string {
        return PropertyKey::ELEMENT_ATTR_VALUE;
    }

    public function getName(): string {
        return _wpcc('Attribute value');
    }

    public function getInputDataTypes(): array {
        return [ValueType::T_ELEMENT];
    }

    public function getOutputDataTypes(): array {
        return [ValueType::T_STRING];
    }

    public function canAssignNewValue(): bool {
        return true;
    }

    protected function createViews(): ?ViewDefinitionList {
        return (new ViewDefinitionList())
            ->add(ViewDefinitionFactory::getInstance()->createElementAttributeInput());
    }

    protected function onCalculate($key, $source, AbstractBaseCommand $cmd): ?CalculationResult {
        if (!($source instanceof Crawler)) return null;

        $this->attrName = $this->getAttributeOption($cmd);
        if ($this->attrName === null) return null;

        $this->elementMap[$key] = $source;
        return new CalculationResult($key, $source->attr($this->attrName));
    }

    protected function onAssignNewValue(string $key, $newValue, AbstractActionCommand $cmd): void {
        if ($this->attrName === null || !is_string($newValue)) return;

        // Get the element of this key
        $element = $this->elementMap[$key] ?? null;
        if ($element === null) return;

        // Get the DOM element
        $child = $element->getNode(0);
        if (!($child instanceof DOMElement)) return;

        // Assign the new attribute value
        $child->setAttribute($this->attrName, $newValue);
    }

    /*
     *
     */

    /**
     * @param AbstractBaseCommand $cmd The command that has the attribute option
     * @return string|null The value of the "attribute" option
     * @since 1.12.0
     */
    protected function getAttributeOption(AbstractBaseCommand $cmd): ?string {
        $value = $cmd->getOption(InputName::ELEMENT_ATTR);
        if (!is_string($value)) return null;

        $value = trim($value);
        return $value === ''
            ? null
            : $value;
    }

}