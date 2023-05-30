<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 27/06/2020
 * Time: 08:01
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Strings;


use DOMElement;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionFactory;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractActionProperty;
use WPCCrawler\Objects\Filtering\Property\Objects\AttributeValue;
use WPCCrawler\Objects\Filtering\Property\Objects\CalculationResult;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;

class StringElementAttributeProperty extends AbstractActionProperty {

    /**
     * @var array Structured as [string => string]. Maps property keys to original subject keys. The values are actual
     *      dot keys of the subject items. The keys are the keys of the property values calculated from the original
     *      subject item. This is used to know which property key belongs to which subject key so that we can recreate
     *      the original structure of the subject item by putting the new values back into their specific place.
     */
    private $keyMap = [];

    /**
     * @var array Structured as [string => {@link Crawler}]. The keys are the keys of the sources. The values are
     *      {@link Crawler} versions of the sources.
     */
    private $crawlersIndex = [];

    /**
     * @var array Structured as [string => array]. The keys are the keys of sources. The values are arrays structured
     *      as [string => {@link Crawler}]. The keys are structured as "$key.{@link KEY_SEPARATOR}.(index as int)"
     */
    private $extractedElementsIndex = [];

    /** @var null|string Stores the attribute option's value */
    private $attribute = null;

    public function getKey(): string {
        return PropertyKey::STRING_ELEMENT_ATTR_VALUE;
    }

    public function getName(): string {
        return _wpcc('Element attribute value');
    }

    public function getInputDataTypes(): array {
        return [ValueType::T_STRING];
    }

    public function getOutputDataTypes(): array {
        return [ValueType::T_STRING];
    }

    protected function createViews(): ?ViewDefinitionList {
        $viewDefinitionFactory = ViewDefinitionFactory::getInstance();
        return (new ViewDefinitionList())
            ->add($viewDefinitionFactory->createMultipleCssSelectorInput())
            ->add($viewDefinitionFactory->createElementAttributeInput());
    }

    protected function onCalculate($key, $source, AbstractBaseCommand $cmd): ?array {
        // If the source is not a string, do not try to create a Crawler from it.
        if (!is_string($source)) return null;

        // Get the attribute
        $this->attribute = $this->getAttributeOption($cmd);
        if ($this->attribute === null) return null;

        // Get the CSS selectors
        $selectorOption = $this->getCssSelectorsOption($cmd);
        if ($selectorOption === null) return null;

        // Cast the given value to string and create a Crawler from it
        $bot = new DummyBot([]);
        $crawler = $bot->createDummyCrawler((string) $source);

        // Find the elements matching the selectors and collect them in an array, as well as their attributes
        $elements   = [];
        $attributes = [];
        $results    = [];

        foreach($selectorOption as $selectorData) {
            $nodes = $bot->getElementsFromCrawler($crawler, $selectorData);
            if ($nodes === null) continue;

            foreach($nodes as $node) {
                $domNode = $node->getNode(0);
                if (!$domNode) continue;

                // Use the node's path in the DOM document as a unique identifier for this node.
                $elementKey = $key . static::KEY_SEPARATOR . $domNode->getNodePath();

                // If this node was processed earlier, do not process it again. Continue with the next one.
                if (isset($this->keyMap[$elementKey])) continue;

                // Get the attribute value. If it is null continue with the next node.
                $attrValue = $this->getAttributeValue($node, $this->attribute);
                if ($attrValue === null) continue;

                $elementAttr = $attrValue->getValue();

                $this->keyMap[$elementKey] = $key;
                $elements[$elementKey]     = $node;
                $attributes[$elementKey]   = $elementAttr;

                // Add a new result
                $results[] = new CalculationResult($elementKey, $elementAttr);
            }

        }

        // Store the crawler and the elements since we need them to remap the new values into their original places.
        $this->crawlersIndex[$key]          = $crawler;
        $this->extractedElementsIndex[$key] = $elements;

        return $results;
    }

    protected function onRevertStructure(array $newSubjectValues): ?array {
        if ($this->attribute === null) return null;

        // Put the new values into their specific places
        foreach($newSubjectValues as $elementKey => $newSubjectValue) {
            // Get the actual subject key
            $subjectKey = $this->keyMap[$elementKey] ?? null;

            // If we could not retrieve the subject key, continue with the next one.
            if ($subjectKey === null) continue;

            // Get the elements of the current subject key
            $extractedElements = $this->extractedElementsIndex[$subjectKey] ?? null;
            if ($extractedElements === null) continue;

            // Get the Crawler object of the element for the current property key
            /** @var Crawler|null $node */
            $node = $extractedElements[$elementKey] ?? null;
            if ($node === null) continue;

            // Now, set the node's new attribute value.
            /** @var DOMElement|null $domNode */
            $domNode = $node->getNode(0);
            if ($domNode === null) continue;

            try {
                if ($newSubjectValue !== null) {
                    $domNode->setAttribute($this->attribute, $newSubjectValue);

                } else {
                    $domNode->removeAttribute($this->attribute);
                }

            } catch (Exception $e) {
                continue;
            }
        }

        // All attributes are set. Now, get the HTML from the Crawlers.
        $bot = new DummyBot([]);
        $results = [];
        foreach($this->crawlersIndex as $subjectKey => $crawler) {
            /** @var Crawler $crawler */
            $results[$subjectKey] = $bot->getContentFromDummyCrawler($crawler);
        }

        return $results;
    }

    /*
     *
     */

    /**
     * @param AbstractBaseCommand $cmd The command storing the options
     * @return string|null If there is an attribute, it is returned. Otherwise, null.
     * @since 1.11.0
     */
    protected function getAttributeOption(AbstractBaseCommand $cmd): ?string {
        $value = $cmd->getStringOption(InputName::ELEMENT_ATTR);
        if ($value === null) return null;

        $value = trim($value);
        return $value === '' ? null : $value;
    }

    /**
     * @param AbstractBaseCommand $cmd The command storing the options
     * @return array|null The CSS selectors, if available. This array does not contain the items with empty CSS
     *                    selectors. If no CSS selector is available, returns null.
     * @since 1.11.0
     */
    protected function getCssSelectorsOption(AbstractBaseCommand $cmd): ?array {
        $cssSelectors = $cmd->getArrayOption(InputName::CSS_SELECTOR);
        if (!$cssSelectors) return null;

        return array_filter($cssSelectors, function($data) {
            $selector = is_array($data)
                ? ($data[SettingInnerKey::SELECTOR] ?? null)
                : null;
            return $selector !== null && trim($selector) !== '';
        });
    }

    /**
     * Get value of an attribute of an element
     *
     * @param Crawler|null $node The node whose attribute is wanted
     * @param string|null  $attr The attribute's name
     * @return AttributeValue|null If found, an {@link AttributeValue}. Otherwise, null. If this is null, the attribute
     *                             will not be considered. If an {@link AttributeValue} with its value set to null is
     *                             returned, the attribute will probably be removed from its element. So, if the
     *                             attribute should be removed from its element, return an {@link AttributeValue} by
     *                             setting its value to null. If you want to skip this attribute, return null.
     * @since 1.11.0
     */
    protected function getAttributeValue(?Crawler $node, ?string $attr): ?AttributeValue {
        if ($node === null || $attr === null) return null;
        return new AttributeValue($node->attr($attr));
    }
}