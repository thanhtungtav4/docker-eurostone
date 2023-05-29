<?php

namespace WPCCrawler\Objects\Tree;

use Closure;
use WP_Term;

class TermTree {

    /** @var TermTreeItem[] The root items of the tree */
    private $rootItems = [];

    /**
     * This constructor cannot be used. Use {@link TermTree::createTree()} to create a new tree.
     *
     * @since 1.12.0
     */
    protected function __construct() { }

    public function addRootItem(TermTreeItem ...$item): void {
        $this->rootItems = array_merge($this->rootItems, $item);
    }

    /**
     * @return TermTreeItem[] See {@link $rootItems}
     */
    public function getRootItems(): array {
        return $this->rootItems;
    }

    /**
     * Walk the tree in depth-first fashion
     *
     * @param Closure $callback A function to be called for each item. Signature: <b>fn(TermTreeItem $item): void</b>
     * @return self
     * @since 1.12.0
     */
    public function walk(Closure $callback): self {
        $rootItems = $this->getRootItems();
        foreach($rootItems as $rootItem) {
            $callback($rootItem);
            $rootItem->walkChildren($callback);
        }

        return $this;
    }

    /*
     * STATIC METHODS
     */

    /**
     * @param WP_Term[] $terms Terms from which a new {@link TermTree} will be created
     * @return TermTree A {@link TermTree} created from the given terms
     */
    public static function createTree(array $terms): TermTree {
        /** @var array<string, TermTreeItem> $treeItemMap */
        $treeItemMap = [];

        /** @var TermTreeItem[] $rootTreeItems */
        $rootTreeItems = [];

        // Create the tree items and store them in a map. Also, keep the root items in a different array, since we need
        // them to create the tree.
        foreach($terms as $term) {
            $treeItem = new TermTreeItem($term);

            $treeItemMap[$treeItem->getId()] = $treeItem;

            if ($term->parent === 0) {
                $rootTreeItems[] = $treeItem;
            }
        }

        // Associate the parents with their children
        foreach($terms as $term) {
            $parentId = $term->parent;
            if ($parentId === 0) continue;

            $parentTreeItem = $treeItemMap[$parentId] ?? null;
            if ($parentTreeItem === null) continue;

            $treeItem = $treeItemMap[$term->term_id] ?? null;
            if ($treeItem === null) continue;

            $parentTreeItem->addChild($treeItem);
        }

        // Now, create the tree. The root items already contain their children.
        $tree = new TermTree();
        $tree->addRootItem(...$rootTreeItems);

        return $tree;
    }
}
