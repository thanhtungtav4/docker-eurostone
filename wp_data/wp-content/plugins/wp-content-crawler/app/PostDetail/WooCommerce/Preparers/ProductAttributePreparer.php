<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/01/2019
 * Time: 15:06
 *
 * @since 1.9.0
 */

namespace WPCCrawler\PostDetail\WooCommerce\Preparers;


use Illuminate\Support\Arr;
use WPCCrawler\PostDetail\Base\BasePostDetailPreparer;
use WPCCrawler\PostDetail\WooCommerce\Data\ProductAttribute;
use WPCCrawler\PostDetail\WooCommerce\Enums\ProductAttributeKeyType;
use WPCCrawler\PostDetail\WooCommerce\WooCommerceData;
use WPCCrawler\PostDetail\WooCommerce\WooCommerceSettings;
use WPCCrawler\Utils;

/**
 * @since 1.9.0
 */
class ProductAttributePreparer extends BasePostDetailPreparer {

    /**
     * @return void
     */
    public function prepare() {
        /** @var WooCommerceData $wcData */
        $wcData = $this->getDetailData();

        // Get the attributes using the settings
        $foundAttributes = $this->getAttributesDefinedBySelectors();

        /** @var ProductAttribute[] $customAttributes */
        $customAttributes = array_merge($this->getCustomAttributesWithSelectors(), $this->getCustomAttributes());

        // If the custom attributes have an attribute name that exists in the attributes retrieved using selectors,
        // combine them.
        if ($foundAttributes && $customAttributes) {
            foreach($customAttributes as $customAttributeIndex => $customAttribute) {
                foreach($foundAttributes as $foundAttribute) {
                    if (strtolower($customAttribute->getKey()) == strtolower($foundAttribute->getKey())) {
                        $foundAttribute->setValues(array_unique(array_merge($foundAttribute->getValues(), $customAttribute->getValues())));

                        unset($customAttributes[$customAttributeIndex]);
                        continue 2;
                    }
                }
            }
        }

        // Create the product attributes by combining found and custom attributes
        /** @var ProductAttribute[] $productAttributes */
        $productAttributes = array_merge($foundAttributes, $customAttributes);

        // Set the attributes of the product
        $wcData->setAttributes($productAttributes);
    }

    /**
     * @return ProductAttribute[]
     * @since 1.9.0
     */
    private function getAttributesDefinedBySelectors() {
        $nameKey  = 'name';
        $valueKey = 'value';

        // Get the defined selector options for attribute names
        $attributeNameSelectors = $this->getBot()->getSetting(WooCommerceSettings::WC_ATTRIBUTE_NAME_SELECTORS, []);
        $attributeNames = [];

        // Extract attribute names using the selectors
        foreach($attributeNameSelectors as $selectorData) {
            $foundNamesData = $this->getBot()
                ->extractValuesWithSelectorData($this->getBot()->getCrawler(), $selectorData, 'text', $nameKey, false, true);
            if (!$foundNamesData || !is_array($foundNamesData)) continue;

            // Add if the name should be considered as taxonomy by retrieving the value set by the user
            if (isset($selectorData['as_taxonomy'])) {
                foreach($foundNamesData as &$data) {
                    $data['as_taxonomy'] = true;
                }
            }

            // Collect the found names
            $attributeNames[] = $foundNamesData;
        }

        // If there are no attribute names, no need to continue.
        if (!$attributeNames) return [];

        // Get the attribute values
        $attributeValues = $this->getValuesForSelectorSetting(WooCommerceSettings::WC_ATTRIBUTE_VALUE_SELECTORS, 'text', $valueKey, false, true);

        // If there are no attribute values, stop.
        if (!$attributeValues) return [];

        // Combine the names array and the values array.
        // Values retrieved from getValuesForSelectorSetting() are array of arrays. We do not need the first level. We
        // need the 2nd-level arrays. Hence, flatten the arrays with a depth of 1.
        $attributes = array_merge(Arr::flatten($attributeNames, 1), Arr::flatten($attributeValues, 1));

        // If there is no attribute, stop.
        if (!$attributes) return [];

        // Sort the names and values by their position in the source code
        $attributes = array_values(Utils::array_msort($attributes, ["start" => SORT_ASC]));

        // We need the attribute data to be sorted as "name -> values -> name -> values". So, if the first item is
        // not a key, reverse the array, assuming that the values come before the keys in the source code.
        if ($attributes[0]['type'] === 'value') {
            $attributes = array_reverse($attributes, false);
        }

        // Now, match the names with their values.
        $preparedAttributes = [];
        $currentAttrName = null;
        for($i = 0; $i < sizeof($attributes); $i++) {
            // Get the attribute data
            $attr = $attributes[$i];
            $currentValue = trim($attr['data']);
            $currentType = $attr['type'];

            // If there is no value, continue with the next one.
            if (!$currentValue) continue;

            // If this is a name, create an array under the attribute name.
            if ($currentType === $nameKey) {
                $productAttribute = new ProductAttribute($currentValue);
                $this->maybeSetAsTaxonomy($productAttribute, $attr);

                // If this is a taxonomy, indicate that the key stands for name of the taxonomy so that we can later
                // act accordingly when creating/defining taxonomies.
                if ($productAttribute->isTaxonomy()) {
                    $productAttribute->setKeyType(ProductAttributeKeyType::NAME);
                }

                $preparedAttributes[$currentValue] = $productAttribute;
                $currentAttrName = $currentValue;

                continue;
            }

            // If the current attribute name does not exist in the array, continue with the next one.
            if (!isset($preparedAttributes[$currentAttrName])) continue;

            // This is a value. Add it among the values of the current attribute name.
            /** @var ProductAttribute $productAttribute */
            $productAttribute = $preparedAttributes[$currentAttrName];
            $productAttribute->addValue($currentValue);
        }

        return array_values($this->prepareAttributes($preparedAttributes, true));
    }

    /**
     * Get attributes using the settings defined in "Custom Attributes with Selectors" setting
     *
     * @return ProductAttribute[]
     * @since 1.9.0
     */
    private function getCustomAttributesWithSelectors() {
        // Get defined settings
        $selectors = $this->getBot()->getSetting(WooCommerceSettings::WC_CUSTOM_ATTRIBUTES_WITH_SELECTORS, []);

        // Extract data and create product attributes
        $attributes = [];
        foreach($selectors as $selectorData) {
            $name = Utils::array_get($selectorData, 'attr_name');
            if (!$name) continue;

            $isSingle = isset($selectorData['single']);

            $values = $this->getBot()->extractValuesWithSelectorData($this->getBot()->getCrawler(), $selectorData, 'text', false, $isSingle, true);
            if (!$values) continue;

            // Make sure values variable is an array
            if (!is_array($values)) $values = [$values];

            // Make sure the values array is flat.
            $values = Arr::flatten($values);

            $attribute = new ProductAttribute($name, $values);
            $this->maybeSetAsTaxonomy($attribute, $selectorData);

            $attributes[] = $attribute;
        }

        return $this->prepareAttributes($attributes, true);
    }

    /**
     * Get custom attributes defined in the settings
     *
     * @return ProductAttribute[] Custom attributes defined in the settings
     * @since 1.9.0
     */
    private function getCustomAttributes() {
        $customAttributes = $this->getBot()->getSetting(WooCommerceSettings::WC_CUSTOM_ATTRIBUTES, []);
        if (!$customAttributes) return [];

        /** @var ProductAttribute[] $preparedAttributes */
        $preparedAttributes = [];

        foreach($customAttributes as $data) {
            // Get the key and the value
            $key   = trim(Utils::array_get($data, 'key', ''));
            $value = trim(Utils::array_get($data, 'value', ''));

            // Both the key and the value must exist
            if (!$key || !$value) continue;

            // Add the attribute with the given key. Separate the value from commas.
            $values = array_unique(array_filter(array_map(function($v) {
                $trimmed = trim($v);
                return $trimmed ? $trimmed : null;
            }, explode(',', $value))));

            $productAttribute = new ProductAttribute($key, $values);
            $this->maybeSetAsTaxonomy($productAttribute, $data);
            
            $preparedAttributes[] = $productAttribute;
        }

        return $this->prepareAttributes($preparedAttributes, false);
    }

    /*
     *
     */

    /**
     * Prepares the attributes by removing the ones that do not have at least 1 value and by separating them using the
     * given separators. When there are separators, values of the attributes will be separated. The items created after
     * separation will be added as new values to the attribute.
     *
     * @param ProductAttribute[] $attributes      A key-value pair where values are ProductAttribute instances.
     * @param bool               $applySeparators True if separators defined in the settings should be applied.
     *
     * @return ProductAttribute[] Prepared attributes. The resultant array will be of the same structure as the given
     *                             $attributes array. In other words, if its an associative array, the keys will be kept.
     * @since 1.9.0
     */
    private function prepareAttributes($attributes, $applySeparators) {
        if (!$attributes) return [];
        $separators = $applySeparators ? $this->getSeparators() : null;

        foreach($attributes as $k => $attribute) {
            /** @var ProductAttribute $attribute */
            if (!$attribute->getValues()) {
                unset($attributes[$k]);
                continue;
            }

            // Separate the values and make sure the result contains unique items
            if (!$separators) continue;
            $attribute->setValues(array_unique(Utils::getSeparated($attribute->getValues(), $separators)));
        }

        return $attributes;
    }

    /**
     * Set the attribute as a taxonomy if it is configured as a taxonomy in the settings.
     * 
     * @param ProductAttribute $attribute   The attribute
     * @param array            $settingData See {@link asTaxonomy()}
     * @since 1.9.0
     */
    private function maybeSetAsTaxonomy(ProductAttribute $attribute, $settingData): void {
        $attribute->setIsTaxonomy($this->isSettingSetAsTaxonomy($settingData));
    }

    /**
     * Check whether the setting's "As taxonomy?" checkbox is checked or not.
     *
     * @param array $settingData Data of a single attribute setting. For example, one of the items defined in "Attribute
     *                           Name Selectors" setting.
     * @return bool
     * @since 1.9.0
     */
    private function isSettingSetAsTaxonomy($settingData) {
        return isset($settingData['as_taxonomy']);
    }

    /**
     * @return array An array of strings that should be used to separate a single attribute value string into different
     *               values. For example, [".", "|", ","].
     * @since 1.9.0
     */
    private function getSeparators() {
        return $this->getBot()->getSetting(WooCommerceSettings::WC_ATTRIBUTE_VALUE_SEPARATORS);
    }
}
