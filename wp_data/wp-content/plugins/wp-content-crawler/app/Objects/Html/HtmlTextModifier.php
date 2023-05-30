<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 28/05/2020
 * Time: 19:40
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Html;


use DOMNode;
use Exception;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;
use WPCCrawler\Utils;

class HtmlTextModifier {

    // TODO: Write unit tests of this class.
    
    /** @var string|null */
    private $html;

    /** @var callable|null */
    private $modifier;

    /**
     * @param string $html The HTML code
     * @since 1.11.0
     */
    public function __construct(?string $html) {
        $this->html = $html;
    }

    /**
     * Modify {@link html}'s texts with a function
     *
     * @param callable|null $modifier A function that modifies the text. For example:
     *                                <b>func(string $text) { return $modifiedText; }</b> If the recursion should stop,
     *                                you can throw an {@link Exception}.
     * @return string|null If there is no modifier or HTML code, returns null. Otherwise, the modified HTML code.
     * @since 1.11.0
     */
    public function modify(?callable $modifier): ?string {
        if ($this->html === null || !$modifier) return null;

        $this->modifier = $modifier;
        
        // Create a crawler to be able to traverse the nodes
        $dummyBot = new DummyBot([]);
        $dummyCrawler = $dummyBot->createDummyCrawler($this->html);
        
        // Get the parent element of the given HTML
        $node = $dummyCrawler->filter('body > div')->first();
        if (!$node) return null; // @phpstan-ignore-line
        
        // Get the DOM node from the crawler node
        $domNode = $node->getNode(0);
        if (!$domNode) return null;

        try {
            $this->modifyTexts($domNode);

        } catch (Exception $e) {
            // Do nothing.
        }

        return $dummyBot->getContentFromDummyCrawler($dummyCrawler);
    }
    
    /*
     * 
     */

    /**
     * Modify all text nodes inside the given node and inside its siblings
     *
     * @param DOMNode|null $node Seed node
     * @since 1.11.0
     */
    protected function modifyTexts(?DOMNode $node): void {
        if (!$node) return;

        // Start from the inner-most element, then process the siblings starting from the top going to the bottom so
        // that we traverse the texts in the order they are defined.

        // First, the children
        if ($node->hasChildNodes()) {
            $this->modifyTexts($node->childNodes->item(0));

        // Text nodes cannot have any children. If the node does not have any children, then it might be a text node.
        } else if ($node->nodeName === '#text') {
            // If this is a text node, modify it and assign the new node to $node. We replace the node with a new text
            // node when modifying  it. Hence, the current node will not have any next siblings after modification. The
            // new node will have a next sibling, on the other hand. So, we reassign $node in order to be able to get
            // the next sibling.
            $node = $this->modifyTextNode($node);
        }

        // Now, process the siblings
        if ($node && $node->nextSibling) {
            $this->modifyTexts($node->nextSibling);
        }
    }

    /**
     * Modify the text of a text node by using {@link modifier}
     *
     * @param DOMNode|null $node A text node whose text should be modified
     * @return DOMNode|null If there was no node or {@link modifier}, the given $node. Otherwise, the modified text
     *                      node. The modified node will be a different object. The old node will not have any siblings
     *                      since it will be removed from the document. So, if you need siblings, retrieve it from the
     *                      returned node element.
     * @since 1.11.0
     */
    protected function modifyTextNode(?DOMNode $node): ?DOMNode {
        if (!$node || !$this->modifier) return $node;

        $document = $node->ownerDocument;
        if (!$document || !$node->parentNode) {
            return null;
        }

        // First, modify the text with the given modifier
        $modifiedText = call_user_func($this->modifier, Utils::getDomNodeHtmlString($node));

        // Create a text node from the modified text
        $newTextNode = $document->createTextNode($modifiedText !== null ? $modifiedText : '');

        if (!$newTextNode) { // @phpstan-ignore-line
            error_log(sprintf('Modified text node could not be created in %s', HtmlTextModifier::class));
            return $node;
        }

        // Import the new text node into the document and replace the old node with the modified node
        $document->importNode($newTextNode);
        $node->parentNode->replaceChild($newTextNode, $node);
        return $newTextNode;
    }
}