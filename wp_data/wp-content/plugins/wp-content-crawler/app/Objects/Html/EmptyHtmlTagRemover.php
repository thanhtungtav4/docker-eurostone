<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 16/11/2019
 * Time: 20:53
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Html;


use DOMNode;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;
use WPCCrawler\Utils;

/**
 * Removes empty HTML tags from the given HTML code.
 *
 * @package WPCCrawler\Objects
 * @since   1.8.1
 */
class EmptyHtmlTagRemover {

    /** @var string HTML code whose empty tags should be cleared */
    private $html;

    /**
     * @var string[] Names of tags that should not be cleared
     * @see https://www.w3.org/TR/html4/index/elements.html (Deprecated tags are not included in this variable)
     */
    private $excludedTagNames = [
        'area',
        'base',
        'br',
        'col',
        'frame',
        'hr',
        'img',
        'input',
        'link',
        'meta',
        'param',
    ];

    /** @var bool True if all comments should be removed from {@link html}. */
    private $removeComments;

    /**
     * @var string Regex format that can be used to match HTML tags having no body, such as "<p></p>" or
     *             "<div id="x" style="font-size: 12px"></div>"
     *             %1$s: HTML tag name
     */
    private const EMPTY_TAG_REGEX_FORMAT = '/<%1$s(?:.|\n)*?><\/%1$s>/';

    /**
     * @param string   $html             See {@link html}
     * @param string[] $excludedTagNames See {@link excludedTagNames}
     * @param bool     $removeComments   See {@link removeComments}
     * @since 1.9.0
     */
    public function __construct(string $html, array $excludedTagNames = [], $removeComments = true) {
        $this->html             = $html;
        $this->excludedTagNames = array_merge($this->excludedTagNames, $excludedTagNames);
        $this->removeComments   = $removeComments;
    }

    /**
     * Removes empty HTML tags
     *
     * @return string HTML code after empty HTML tags are removed
     * @since 1.9.0
     */
    public function removeEmptyTags(): string {
        $dummyBot = new DummyBot([]);
        $crawler = $dummyBot->createDummyCrawler($this->html);

        $this->recursivelyRemove($crawler->filter("body > div")->first()->getNode(0));

        return $dummyBot->getContentFromDummyCrawler($crawler);
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Recursively remove all empty tags of the given node. {@link excludedTagNames} will not be removed.
     *
     * @param DOMNode|null $node
     * @since 1.9.0
     * @since 1.11.1 Accepts null for $node
     */
    private function recursivelyRemove(?DOMNode $node): void {
        if (!$node) {
            return;
        }
        
        // The removal should be depth-first. Also, handle the siblings first.

        // If this node has a sibling, process it.
        if($node->nextSibling) $this->recursivelyRemove($node->nextSibling);

        // If this node has children, process them
        if($node->hasChildNodes()) {
            $this->recursivelyRemove($node->childNodes->item(0));
        }

        // All siblings and children of this node have been processed. Now, process this node.

        // If this node should not be removed, stop.
        // If this node still has child nodes, stop.
        if (in_array($node->nodeName, $this->excludedTagNames) || $node->hasChildNodes()) {
            return;
        }

        // If the comments should be removed and this is a comment, remove it and stop.
        if ($this->removeComments && $node->nodeName === '#comment') {
            $this->removeNode($node);
            return;
        }

        // Get the HTML of this node by trimming.
        $html = trim(Utils::getDomNodeHtmlString($node));

        // If the trimmed $html is empty, remove this node.
        // If this is an element with no content, remove this node.
        if ($html === '' || preg_match(sprintf(static::EMPTY_TAG_REGEX_FORMAT, $node->nodeName), $html) === 1) {
            $this->removeNode($node);
        }
    }

    /**
     * Remove a DOM node from its document
     *
     * @param DOMNode $node Node that will be removed from its document
     * @since 1.9.0
     */
    private function removeNode(DOMNode $node): void {
        if ($node->parentNode === null) return;

        $node->parentNode->removeChild($node);
    }
}