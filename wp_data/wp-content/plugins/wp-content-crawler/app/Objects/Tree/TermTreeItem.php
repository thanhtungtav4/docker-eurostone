<?php

namespace WPCCrawler\Objects\Tree;

use Closure;
use WP_Term;

class TermTreeItem {

    /** @var WP_Term A term */
    private $term;

    /** @var TermTreeItem[] Children {@link TermTreeItem}s of this {@link TermTreeItem} */
    private $children = [];

    /** @var int The depth of this tree item */
    private $depth = 0;

    public function __construct(WP_term $term) {
        $this->term = $term;
    }

    /**
     * @return WP_term See {@link $term}
     * @since 1.12.0
     */
    public function getTerm(): WP_term {
        return $this->term;
    }

    /**
     * @return int See {@link $id}
     * @since 1.12.0
     */
    public function getId(): int {
        return $this->getTerm()->term_id;
    }

    /**
     * @return string See {@link $name}
     * @since 1.12.0
     */
    public function getName(): string {
        return $this->getTerm()->name;
    }

    /**
     * @return int See {@link $depth}
     * @since 1.12.0
     */
    public function getDepth(): int {
        return $this->depth;
    }

    /**
     * @param int $depth See {@link $depth}
     * @return TermTreeItem
     * @since 1.12.0
     */
    public function setDepth(int $depth): TermTreeItem {
        $this->depth = $depth;
        foreach($this->getChildren() as $child) {
            $child->setDepth($depth + 1);
        }

        return $this;
    }

    /**
     * @return TermTreeItem[] See {@link $children}
     * @since 1.12.0
     */
    public function getChildren(): array {
        return $this->children;
    }

    /**
     * @param TermTreeItem[] $children See {@link $children}
     * @since 1.12.0
     */
    public function setChildren(array $children): void {
        $this->children = $children;
    }

    /**
     * @return bool True if this {@link TermTreeItem} has at least one child
     * @since 1.12.0
     */
    public function hasChildren(): bool {
        return !empty($this->getChildren());
    }

    /**
     * Add a child or children to this tree item
     *
     * @param TermTreeItem ...$child The child that will be added to this tree item
     * @return self
     * @since 1.12.0
     */
    public function addChild(TermTreeItem ...$child): self {
        $this->children = array_merge($this->children, $child);

        $currentDepth = $this->getDepth();
        foreach($child as $item) {
            $item->setDepth($currentDepth + 1);
        }

        return $this;
    }

    /**
     * Walk this subtree in depth-first fashion. The callback will be called for the children, recursively.
     *
     * @param Closure $callback A function to be called for each item. Signature: <b>fn(TermTreeItem $item): void</b>
     * @return self
     * @since 1.12.0
     */
    public function walkChildren(Closure $callback): self {
        $children = $this->getChildren();
        foreach($children as $child) {
            $callback($child);
            $child->walkChildren($callback);
        }

        return $this;
    }

}
