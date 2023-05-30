<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/03/2020
 * Time: 08:02
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Transformation\Objects;


use WPCCrawler\Interfaces\Arrayable;
use WPCCrawler\Objects\Filtering\Objects\FieldConfig;

class TransformableFieldList implements Arrayable {

    /** @var TransformableField[] The transformable fields */
    private $items = [];

    /** @var array|null Caches the value returned via {@link toAssociativeArray()} */
    private $assocArrayCache = null;

    /**
     * @var null|array Key-value pairs created from the items where keys are {@link TransformableField::getDotKey()}
     *      and the values are {@link TransformableField} instances
     */
    private $keyMap = null;

    /**
     * @var FieldConfig[]|null See {@link TransformableField::$fieldConfigs}. When this value exists, field configs of
     *      all of the fields added to the list will be set to this value.
     */
    private $fieldConfigs;

    /**
     * @param TransformableField[]           $items        See {@link items}
     * @param FieldConfig|FieldConfig[]|null $fieldConfigs See {@link fieldConfigs}
     * @since 1.11.0
     */
    public function __construct(?array $items = null, $fieldConfigs = null) {
        $this->fieldConfigs = $fieldConfigs instanceof FieldConfig ? [$fieldConfigs] : $fieldConfigs;
        if ($items) $this->addAll($items);
    }

    /**
     * @return TransformableField[]
     * @since 1.11.0
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * Get a {@link TransformableField} by its dot key
     *
     * @param string $key The dot key of one of the fields in the list
     * @return TransformableField|null If the key exists, its value. Otherwise, null.
     * @since 1.11.0
     */
    public function getByKey(string $key): ?TransformableField {
        return $this->getKeyMap()[$key] ?? null;
    }

    /**
     * Replace an item having a key with another item.
     *
     * @param string             $key     The dot key of the item that will be replaced. If an item with this key does
     *                                    not exist, the replacement will not occur.
     * @param TransformableField $newItem The replacement that will be put in place of the item having the specified key
     * @return $this
     * @since 1.11.0
     */
    public function replaceByKey(string $key, TransformableField $newItem): self {
        // Replace the items having the specified key with the new item
        foreach($this->items as $k => $item) {
            if ($item->getDotKey() !== $key) continue;

            $this->items[$k] = $newItem;
        }

        // If a key map exists
        if ($this->keyMap) {
            // If the key map contains an item with the provided key, remove it. That item no longer exists in the list.
            if (isset($this->keyMap[$key])) {
                unset($this->keyMap[$key]);
            }

            // Add the new item to the key map.
            $this->keyMap[$newItem->getDotKey()] = $newItem;
        }

        return $this;
    }

    /*
     * ADDITION METHODS
     */

    /**
     * @param TransformableField $field
     * @return $this
     * @since 1.11.0
     */
    public function add(TransformableField $field): self {
        $this->invalidateCaches();
        $this->maybeAssignFieldConfigs($field);
        $this->items[] = $field;
        return $this;
    }

    /**
     * @param TransformableField[] $fields
     * @return $this
     * @since 1.11.0
     */
    public function addAll(array $fields): self {
        $this->invalidateCaches();
        $this->maybeAssignFieldConfigs($fields);
        $this->items = array_merge($this->items, $fields);
        return $this;
    }

    /**
     * @param TransformableFieldList $list
     * @return $this
     * @since 1.11.0
     */
    public function addAllFromList(TransformableFieldList $list): self {
        $this->invalidateCaches();
        return $this->addAll($list->getItems());
    }

    /*
     *
     */

    /**
     * @return array An associative array where the keys are the values returned by
     *               {@link TransformableField::getDotKey()} and the values are human-readable titles of the fields.
     * @since 1.11.0
     */
    public function toAssociativeArray(): array {
        if ($this->assocArrayCache === null) {
            $this->assocArrayCache = [];

            foreach($this->getItems() as $item) {
                $this->assocArrayCache[$item->getDotKey()] = $item->getTitle() ?: '';
            }
        }

        return $this->assocArrayCache;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array {
        $result = [];
        foreach($this->getItems() as $item) {
            $result[] = $item->toArray();
        }

        return $result;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Invalidates the value of {@link $assocArrayCache} and {@link $keyMap}
     * @since 1.11.0
     */
    private function invalidateCaches(): void {
        $this->assocArrayCache = null;
        $this->keyMap = null;
    }

    /**
     * Assign the field configs of the given fields if {@link $fieldConfigs} is not null.
     *
     * @param TransformableField[]|TransformableField $fields
     * @since 1.11.0
     */
    private function maybeAssignFieldConfigs($fields): void {
        if ($this->fieldConfigs === null) return;
        $this->assignFieldConfigs($fields, $this->fieldConfigs);
    }

    /**
     * Assign field configs of all of the given fields to the given value
     *
     * @param TransformableField[]|TransformableField $fields
     * @param FieldConfig[]|null                      $fieldConfigs See {@link $fieldConfigs}
     * @since 1.11.0
     */
    private function assignFieldConfigs($fields, ?array $fieldConfigs): void {
        if (!$fields) return;
        if (!is_array($fields)) $fields = [$fields];

        foreach($fields as $field) {
            $field->setFieldConfigs($fieldConfigs);
        }
    }

    /**
     * @return array See {@link $keyMap}
     * @since 1.11.0
     */
    private function getKeyMap(): array {
        if ($this->keyMap === null) {
            $this->keyMap = [];
            foreach($this->getItems() as $item) {
                $this->keyMap[$item->getDotKey()] = $item;
            }
        }

        return $this->keyMap;
    }

}