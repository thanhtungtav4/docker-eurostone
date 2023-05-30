<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 22/11/2020
 * Time: 20:13
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Html;


use DOMNode;

/**
 * Unwraps the children of a container element to the container's parent and removes the container. For example, if the
 * tag element in "<parent><tag>some <b>content</b></tag></parent>" is unwrapped, it becomes
 * "<parent>some <b>content</b></parent>". PHPStorm renders the HTML in the example, making it incomprehensible. See the
 * not-rendered PHPDoc to understand the example.
 *
 * @since 1.11.0
 */
class ElementUnwrapper {

    /**
     * @param DOMNode|null $container The container element whose contents should be unwrapped
     * @since 1.11.0
     */
    public function unwrap(?DOMNode $container): void {
        if (!$container) return;

        // Get the parent node of the container. We will insert the container's child to the container's parent. If the
        // parent does not exist, we cannot do this.
        $parentNode = $container->parentNode;
        if (!$parentNode) return;

        // Insert the children of the container into the children's parent.
        foreach($container->childNodes as $childNode) {
            /** @var DOMNode $childNode */

            // Clone the node and insert it to the parent of the container, just before the container.
            $parentNode->insertBefore($childNode->cloneNode(true), $container);
        }

        // Remove the container
        $parentNode->removeChild($container);
    }

}