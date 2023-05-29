<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/01/2019
 * Time: 15:08
 *
 * @since 1.9.0
 */

namespace WPCCrawler\PostDetail\WooCommerce\Data;

use WPCCrawler\PostDetail\WooCommerce\Enums\ProductAttributeKeyType;

/**
 * @since 1.9.0
 */
class ProductAttribute {

    /**
     * @var string Name/slug of the attribute. If {@link isTaxonomy} is true, this stores the slug. Otherwise, name of
     *             the attribute.
     */
    private $key;

    /** @var array A sequential array that stores values of the product attribute */
    private $values;

    /** @var boolean True if this attribute is or should be registered in WooCommerce's Attributes page. */
    private $isTaxonomy;

    /**
     * @var string Type of {@link key} variable. It can be one of the constants defined in {@link ProductAttributeKeyType}
     *             class. This defaults to {@link ProductAttributeKeyType::SLUG} when {@link isTaxonomy} is true,
     *             and to {@link ProductAttributeKeyType::NAME} when it is false.
     */
    private $keyType = null;

    /**
     * ProductAttribute constructor.
     *
     * @param string $key        See {@link key}
     * @param array  $values     See {@link values}
     * @param bool   $isTaxonomy See {@link isTaxonomy}
     * @since 1.9.0
     */
    public function __construct($key, $values = [], $isTaxonomy = false) {
        $this->key = $key;

        $this->setValues($values);
        $this->setIsTaxonomy($isTaxonomy);
    }

    /**
     * @param array|string $values
     * @since 1.9.0
     */
    public function setValues($values): void {
        $this->values = is_array($values) ? $values : [$values];
    }

    /**
     * Add an attribute value.
     *
     * @param string $value
     * @since 1.9.0
     */
    public function addValue($value): void {
        if (!$value) return;

        $value = trim($value);
        if (!$value) return;

        $this->values[] = $value;
    }

    /**
     * @param bool $isTaxonomy
     * @since 1.9.0
     */
    public function setIsTaxonomy($isTaxonomy): void {
        $this->isTaxonomy = (bool) $isTaxonomy;
    }

    /**
     * @return string
     * @since 1.9.0
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * This method exists just to be able to use "keyRelaxed" via {@link ValueSetter}. {@link setKey()} method does not
     * allow modifications of the keys defined as taxonomy so that the key is not transformed via the transformation
     * services. However, the users should be able to change it via the filters. So, we simply need to use a different
     * method to set the key. But, according to the requirements of {@link ValueSetter}, if we define "keyRelaxed",
     * both "getKeyRelaxed()" and "setKeyRelaxed()" method must exist. This method is added to fulfill that requirement
     * only.
     *
     * @return string Returns the value returned by {@link getKey()}
     * @since 1.11.0
     */
    public function getKeyRelaxed(): string {
        return $this->getKey();
    }

    /**
     * Set key of the product attribute. The key will not be changed if the attribute is set as taxonomy. This is to
     * prevent the key from being changed by {@link TranslationSetter} when it is a taxonomy, since the taxonomy key
     * is an already-defined value. If it is changed by a translation service, it will be useless. If you want to change
     * it, set $addAnyway to true.
     *
     * @param string $key       New key
     * @param bool   $addAnyway Set this to true if you want to change the key even if the attribute is a taxonomy.
     * @since 1.9.0
     */
    public function setKey($key, $addAnyway = false): void {
        if (!$addAnyway && $this->isTaxonomy()) return;

        $this->key = $key;
    }

    /**
     * This method sets the key even if the attribute's key is a taxonomy. It is named as "relaxed", because it relaxes
     * (or removes) the constraint that does not allow the key to be set if it is a taxonomy. This method exists,
     * because we want to be able to change the key via the filters. The action commands use {@link ValueSetter} to set
     * the value. So, when a {@link ValueSetter} calls this method, the key will be definitely set, as opposed to
     * {@link setKey()} method. This actually calls {@link setKey()} by providing <code>true</code> to $addAnyway
     * parameter. Also see {@link getKeyRelaxed()}.
     *
     * @param string $key New key. See {@link setKey()}.
     * @since 1.11.0
     */
    public function setKeyRelaxed($key): void {
        $this->setKey($key, true);
    }

    /**
     * @return array
     * @since 1.9.0
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * @return bool
     * @since 1.9.0
     */
    public function isTaxonomy() {
        return $this->isTaxonomy;
    }

    /**
     * @return string See {@link $keyType}
     * @since 1.9.0
     */
    public function getKeyType() {
        if ($this->keyType === null) {
            return $this->isTaxonomy ? ProductAttributeKeyType::SLUG : ProductAttributeKeyType::NAME;
        }

        return $this->keyType;
    }

    /**
     * @param string $keyType See {@link $keyType}
     * @since 1.9.0
     */
    public function setKeyType($keyType): void {
        if (!ProductAttributeKeyType::isValidValue($keyType)) return;
        $this->keyType = $keyType;
    }

}