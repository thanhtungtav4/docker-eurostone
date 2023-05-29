<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 17/02/2021
 * Time: 18:36
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Element;


use DOMElement;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractBotActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Views\CheckboxWithLabel;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\InputWithLabel;
use WPCCrawler\Utils;

class ElementClone extends AbstractBotActionCommand {

    /**
     * @var null|string[] IDs that should be assigned to the cloned elements. This caches the IDs provided by the
     *      command's options.
     */
    private $elementIds = null;

    /** @var int Keeps the number of elements cloned by this command. */
    private $cloneCount = 0;

    /**
     * @var bool True if the log message about that only one element is cloned because "clone all" option is not set is
     *      added to the logger.
     */
    private $singleCloneMessageAdded = false;

    public function getKey(): string {
        return CommandKey::ELEMENT_CLONE;
    }

    public function getName(): string {
        return _wpcc('Clone');
    }

    protected function createViews(): ?ViewDefinitionList {
        return (new ViewDefinitionList())
            ->add((new ViewDefinition(CheckboxWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Clone all found elements?'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('Check this if all the elements found by the provided
                    CSS selectors must be cloned. If this is not checked, only the first one in the found elements will 
                    be cloned.'))
                ->setVariable(ViewVariableName::NAME,  InputName::CLONE_ALL_FOUND_ELEMENTS))

            ->add((new ViewDefinition(InputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Element ID'))
                ->setVariable(ViewVariableName::INFO,  sprintf(_wpcc('Enter the ID that will be assigned to the 
                    cloned element. You can use this ID to interact with the cloned element later. An ID attribute 
                    cannot have a space character in it, according to the specifications. However, if you enter a 
                    space-separated list, it will be considered as multiple IDs. In case of cloning multiple elements,
                    the given ID(s) will be suffixed a number for each cloned element. For example, if the given ID is 
                    %1$s, and there are three cloned elements, while the first clone will have %1$s as its ID, the 
                    second and third ones will have %2$s and %3$s as their IDs.'),
                    '<span class="highlight id">my-element my-clone</span>',
                    '<span class="highlight id">my-element2 my-clone2</span>',
                    '<span class="highlight id">my-element3 my-clone3</span>'
                ))
                ->setVariable(ViewVariableName::NAME,  InputName::ELEMENT_ID)
                ->setVariable(ViewVariableName::TYPE,  'text'));
    }

    protected function onExecuteCommand($node): void {
        if (!($node instanceof Crawler)) return;

        // If the user does not want to clone all found elements, and an element has already been cloned, stop.
        if (!$this->isCloneAllElements() && $this->cloneCount > 0) {
            $this->onOnlyOneCloneIsAllowed();
            return;
        }

        // Get the element IDs. The element IDs must exist. Otherwise, we cannot clone the element.
        $elementIds = $this->getElementIds();
        if (!$elementIds) {
            $this->onNoElementIdExists();
            return;
        }

        // Get the source node
        $sourceNode = $node->getNode(0);
        if (!($sourceNode instanceof DOMElement) || $sourceNode->parentNode === null) return;

        // Deep-clone the source node. If the clone could not be created, notify the user and stop.
        $cloned = $this->cloneNode($sourceNode);
        if (!$cloned) {
            $this->onCloneUnsuccessful($sourceNode);
            return;
        }

        // Set the clone's ID attribute
        $cloned->setAttribute('id', implode(' ', $elementIds));

        // We want to insert the clone after the source node, but DOMNode does not have this feature. Instead, insert
        // the clone before the source node, then move the source before the clone.
        $sourceNode->parentNode->insertBefore($cloned, $sourceNode);
        $sourceNode->parentNode->insertBefore($sourceNode, $cloned);

        // Increase the clone count, since we are done with this node.
        $this->cloneCount++;
    }

    /*
     * PROTECTED HELPERS
     */

    /**
     * @param DOMElement $sourceNode The node to be cloned
     * @return DOMElement|null The deep-clone of the source node, if it could be cloned. Otherwise, null.
     * @since 1.11.0
     */
    protected function cloneNode(DOMElement $sourceNode): ?DOMElement {
        $clone = $sourceNode->cloneNode(true);
        return $clone instanceof DOMElement ? $clone : null;
    }

    /**
     * Adds a log message saying that the elements other than the first one is not cloned because of the configuration
     * of the command.
     *
     * @return $this
     * @since 1.11.0
     */
    protected function onOnlyOneCloneIsAllowed(): self {
        if (!$this->singleCloneMessageAdded) {
            $this->singleCloneMessageAdded = true;

            $logger = $this->getLogger();
            if ($logger) $logger->addMessage(_wpcc('Multiple elements are found, but only the first one is cloned 
                because the command is configured that way.'));
        }

        return $this;
    }

    /**
     * Adds a log and information messages about the failed clone operation
     *
     * @param DOMElement $sourceNode The element that could not be cloned
     * @return $this
     * @since 1.11.0
     */
    protected function onCloneUnsuccessful(DOMElement $sourceNode): self {
        $nodeHtml = Utils::getDomNodeHtml($sourceNode);
        $message = sprintf(_wpcc('An element (%2$s) could not be cloned via "%1$s" command.'),
            $this->getName(),
            $nodeHtml === null ? _wpcc('unknown') : Str::limit($nodeHtml)
        );

        $logger = $this->getLogger();
        if ($logger) $logger->addMessage($message);

        Informer::addInfo($message)->addAsLog();
        return $this;
    }

    /**
     * Adds a log and information messages about the not-existing element IDs
     *
     * @return $this
     * @since 1.11.0
     */
    protected function onNoElementIdExists(): self {
        $message = sprintf(
            _wpcc('No element ID is provided for "%1$s" command. The element is not cloned. ID for the cloned 
                    element must be provided.'),
            $this->getName()
        );

        $logger = $this->getLogger();
        if ($logger) $logger->addMessage($message);

        Informer::addInfo($message)->addAsLog();
        return $this;
    }

    /*
     * OPTIONS
     */

    /**
     * @return bool True if all the elements must be cloned. False if only the first subject element must be cloned.
     * @since 1.11.0
     */
    protected function isCloneAllElements(): bool {
        return $this->getCheckboxOption(InputName::CLONE_ALL_FOUND_ELEMENTS);
    }

    /**
     * @return string[] See {@link elementIds}
     * @since 1.11.0
     */
    protected function getElementIds(): array {
        if ($this->elementIds === null) {
            $ids = $this->getOption(InputName::ELEMENT_ID);
            if ($ids === null || $ids === '') {
                $this->elementIds = [];

            } else {
                // Trim the white-space surrounding the text and explode it from space characters. Then, remove the empty
                // strings from the array.
                $this->elementIds = array_filter(explode(' ', trim($ids)), function($v) {
                    return $v !== '';
                });
            }

        }

        // If the clone count is more than 0, suffix the clone count to the element IDs.
        if ($this->cloneCount > 0) {
            return array_map(function($id) {
                return $id . ($this->cloneCount + 1);
            }, $this->elementIds);
        }

        return $this->elementIds;
    }
}