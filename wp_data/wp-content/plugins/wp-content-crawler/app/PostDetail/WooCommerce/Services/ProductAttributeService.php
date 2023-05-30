<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 30/01/2019
 * Time: 14:05
 *
 * @since 1.9.0
 */

namespace WPCCrawler\PostDetail\WooCommerce\Services;

use Exception;
use WC_Product_Attribute;
use WP_Error;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\PostDetail\WooCommerce\Data\ProductAttribute;
use WPCCrawler\PostDetail\WooCommerce\Enums\ProductAttributeKeyType;
use WPCCrawler\Utils;

/**
 * @since 1.9.0
 */
class ProductAttributeService {

    /** @var ProductAttributeService|null */
    private static $instance = null;

    /** @var int Maximum WooCommerce attribute slug length, defined in {@link wc_create_attribute()} */
    private $maxSlugLength = 27;

    /** @var int Maximum length of the name of an attribute */
    private $maxAttributeNameLength = 200;

    /**
     * @var array|null Stores existing WooCommerce attribute taxonomies. An array of objects, where each object is a row
     *      of woocommerce_attribute_taxonomies table.
     */
    private $existingAttributeTaxonomies = null;

    /**
     * Get the instance
     *
     * @return ProductAttributeService
     * @since 1.9.0
     * @throws Exception See {@link __construct()}.
     */
    public static function getInstance() {
        if (!static::$instance) static::$instance = new ProductAttributeService();
        return static::$instance;
    }

    /**
     * This is a singleton.
     *
     * @since 1.9.0
     * @throws Exception See {@link makeSureWCProductAttributeClassExists()}.
     */
    private function __construct() {
        // Make sure WC_Product_Attribute class exists, since we cannot do anything without it.
        $this->makeSureWCProductAttributeClassExists();
    }

    /**
     * Create a WooCommerce product attribute instance from the given product attribute.
     *
     * @param ProductAttribute $attribute
     * @return WC_Product_Attribute|null If successfully created, the attribute. Otherwise, null.
     * @since 1.9.0
     */
    public function createWooCommerceProductAttribute(ProductAttribute $attribute) {
        $name       = $attribute->getKey();
        $options    = $attribute->getValues();
        $asTaxonomy = $attribute->isTaxonomy();

        $wcAttribute = new WC_Product_Attribute();

        // If the attribute is not a taxonomy
        if (!$asTaxonomy) {
            // Assign values directly.
            $wcAttribute->set_id(0);
            $wcAttribute->set_name($name);
            $wcAttribute->set_options($options);

        // Otherwise
        } else {
            // Try to set taxonomy attribute details by creating product attribute taxonomies and product attribute terms.
            try {
                $this->setTaxonomyAttributeDetails($wcAttribute, $attribute);

            } catch (Exception $e) {
                // Inform the user about the error and return null in case of errors.
                Informer::addError($e->getMessage())->addAsLog();

                return null;
            }
        }

        $wcAttribute->set_visible(true);
        $wcAttribute->set_variation(false);

        return $wcAttribute;
    }

    /**
     * Sanitize a taxonomy name by making it lowercase and removing unnecessary chars, spaces, and so on.
     *
     * @param string $name Name to be sanitized
     * @return null|string If successful, the sanitized value. Otherwise, null.
     * @since 1.9.0
     * @uses wc_sanitize_taxonomy_name()
     */
    public function sanitizeTaxonomyName($name) {
        $sanitizeFunctionName = 'wc_sanitize_taxonomy_name';
        if (!function_exists($sanitizeFunctionName)) {
            Informer::addError(
                sprintf('%1$s function does not exist. Taxonomy name cannot be sanitized.', $sanitizeFunctionName)
            )->addAsLog();

            return null;
        }

        return wc_sanitize_taxonomy_name($name);
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Set details of a WooCommerce product attribute when it should be added as taxonomy.
     *
     * @param WC_Product_Attribute $wcAttribute
     * @param ProductAttribute     $attribute
     * @throws Exception Throws what {@link maybeCreateAttributeTaxonomy()} throws. Also, throws an exception when a
     *                   provided taxonomy slug does not exist.
     * @since 1.9.0
     */
    private function setTaxonomyAttributeDetails(WC_Product_Attribute $wcAttribute, ProductAttribute $attribute): void {
        if (!$attribute->isTaxonomy() || !$attribute->getKey() || !$attribute->getValues()) return;

        /** @noinspection PhpUnusedLocalVariableInspection */
        $attributeTaxonomyId = null;

        // Create a slug for the attribute name only if the given key stands for the name of the attribute. If it stands
        // for the slug, no need to do anything, since it means the user has a defined slug in WooCommerce's Attributes
        // page.
        if($attribute->getKeyType() === ProductAttributeKeyType::NAME) {
            // Create the slug for the given attribute name.
            $slug = wc_sanitize_taxonomy_name($attribute->getKey());

            // Sanitize the name of the attribute
            $attribute->setKey($this->sanitizeAttributeName($attribute->getKey()), true);

            // Check the existence with the name of the attribute.
            $attributeTaxonomyId = $this->getExistingAttributeTaxonomyIdByName($attribute->getKey());
            if ($attributeTaxonomyId) {
                $slug = $this->getExistingAttributeTaxonomySlugById($attributeTaxonomyId);
            }

            // If there is not an existing attribute
            if (!$attributeTaxonomyId) {
                // Check if there is an attribute taxonomy with this slug
                $attributeTaxonomyId = $this->doesAttributeTaxonomyExist($slug);
            }

            // If there is still not an existing attribute
            if (!$attributeTaxonomyId) {
                // Create a unique slug
                $slug = $this->createUniqueSlugFromAttributeName($attribute->getKey());

                // If a slug could not be created or it does not exist, stop.
                if (!$slug) return;
            }

        } else {
            // If the given key stands for a slug, make sure it exists.
            $slug = $attribute->getKey();
            $attributeTaxonomyId = $this->doesAttributeTaxonomyExist($slug);

            if (!$attributeTaxonomyId) {
                // Throw an exception
                throw new Exception(sprintf(
                    _wpcc('Given attribute taxonomy slug "%1$s" does not exist. Please make sure you created an 
                        attribute taxonomy with the given slug in the product attributes page of WooCommerce.'),
                    $slug)
                );
            }

        }

        // If there is no slug, we cannot proceed. This will probably never happen at this point of execution. We are
        // just being cautious.
        if (!$slug) {
            throw new Exception(sprintf(
                _wpcc('A taxonomy slug does not exist for "%1$s" product attribute.'),
                $attribute->getKey()
            ));
        }

        // If the attribute taxonomy ID does not exist, try to create it.
        if (!$attributeTaxonomyId) {
            // This throws an exception when an error occurs. Hence, no need to check if the ID could be retrieved.
            $attributeTaxonomyId = $this->maybeCreateAttributeTaxonomy($attribute->getKey(), $slug);
        }

        $wcAttribute->set_id($attributeTaxonomyId);

        // WooCommerce wants the taxonomy name for the name of the attribute when the attribute is a taxonomy.
        // @phpstan-ignore-next-line
        $wcAttribute->set_name(wc_attribute_taxonomy_name($this->getExistingAttributeTaxonomySlugById($attributeTaxonomyId)));

        // Get the taxonomy name. WooCommerce stores the attribute taxonomies by their slug. However, when using them,
        // it prepends 'pa_' (standing for "product attribute", I guess) them. So, we need to insert the terms under
        // a taxonomy whose name is prepended 'pa_'. We do this by using WooCommerce's built-in function that does this.
        // This function does not have @since value. Hence, we assume it exists starting from very early versions of
        // WooCommerce.
        $taxonomyKey = wc_attribute_taxonomy_name($slug);

        // Create/get attribute value (option) taxonomies.
        $optionIds = [];
        foreach($attribute->getValues() as $value) {
            $termId = Utils::insertTerm($value, $taxonomyKey);
            if (!$termId) continue;

            $optionIds[] = $termId;
        }

        // Assign the option term IDs to WooCommerce product attribute
        $wcAttribute->set_options($optionIds);
    }

    /**
     * Prepare the attribute name so that its length is within the limits.
     *
     * @param string $name Desired attribute name
     * @return string Sanitized attribute name
     * @since 1.9.0
     */
    private function sanitizeAttributeName($name) {
        if (mb_strlen($name) > $this->maxAttributeNameLength) {
            return mb_substr($name, 0, $this->maxAttributeNameLength);
        }

        return $name;
    }

    /**
     * If the attribute taxonomy does not exist, creates it.
     *
     * @param string $name Name of the attribute taxonomy
     * @param string $slug Slug of the attribute taxonomy. This will be used when checking if the taxonomy exists.
     * @return int ID of the existing or just-created attribute taxonomy
     * @throws Exception If an attribute taxonomy could not be created.
     * @since 1.9.0
     */
    private function maybeCreateAttributeTaxonomy($name, $slug) {
        // If the taxonomy exists, return its ID.
        if ($taxonomyId = $this->doesAttributeTaxonomyExist($slug)) {
            return $taxonomyId;
        }

        // Taxonomy does not exist. Create it.
        $taxonomyId = wc_create_attribute([
            'name' => $this->sanitizeAttributeName($name),
            'slug' => $slug,
        ]);

        if (is_wp_error($taxonomyId)) {
            /** @var WP_Error $taxonomyId */
            throw new Exception($taxonomyId->get_error_message());
        }

        if (!$taxonomyId) {
            throw new Exception(sprintf(_wpcc('Attribute taxonomy could not be created. Slug: "%1$s", Name: "%2$s"'),
                $slug, $name
            ));
        }

        // We just created a new attribute taxonomy. Invalidate the existing attribute taxonomy cache so that when
        // required, a fresh version of it will be retrieved from the database, including the one we have just created.
        $this->existingAttributeTaxonomies = null;

        // Since the taxonomy has just been created, it is currently not registered to WordPress. This causes values
        // not to be added into the taxonomy. So, let's just register the taxonomy to WordPress with the minimum
        // requirements. Taxonomies are registered every time WordPress loads. We do not need to define a fully-featured
        // taxonomy because there is no UI at this point of script execution and there won't be any. When WordPress is
        // loaded again, a fully-featured taxonomy will be registered by WooCommerce. Hence, this is safe to do.
        $taxonomyName = wc_attribute_taxonomy_name($slug);
        if (!taxonomy_exists($taxonomyName)) {
            // The 'product' here is taken from WC_Post_Types::register_taxonomies(). WooCommerce registers the
            // attributes under 'product'. There is no variable that can be used for this. Hence, we hard-core it,
            // unfortunately.
            register_taxonomy($taxonomyName, ['product']);
        }

        return $taxonomyId;
    }

    /**
     * Create a unique slug for a to-be-created attribute taxonomy using its name.
     *
     * @param string $name Name of the attribute
     * @return string|null Created slug. If there was something wrong, returns null.
     * @since 1.9.0
     */
    private function createUniqueSlugFromAttributeName($name) {
        // Try to create the slug.
        $slug = $this->sanitizeTaxonomyName($name);
        return $this->generateUniqueAttributeTaxonomySlug($slug);
    }

    /**
     * Generate a unique attribute taxonomy slug by considering already-existing attribute taxonomy slugs.
     *
     * @param null|string $desiredSlug
     * @return null|string
     * @since 1.9.0
     */
    private function generateUniqueAttributeTaxonomySlug($desiredSlug = null) {
        $slug = $desiredSlug ?: $this->generateRandomString($this->maxSlugLength);

        // If the length of the slug is greater than the max length, shorten it.
        if (strlen($slug) > $this->maxSlugLength) {
            $oldSlug = $slug;
            $slug = substr($slug, 0, $this->maxSlugLength);

            Informer::addInfo(sprintf(
                _wpcc('Length of the slug is greater than %2$s. Therefore, it is shortened. Term: "%1$s", Shortened: "%3$s"'),
                $oldSlug,
                $this->maxSlugLength,
                $slug
            ))->addAsLog();
        }

        $try = 0;           // Current trial count
        $maxTry = 100;      // Maximum number of trials
        $postfixLength = 4; // Length of the number of chars that will be appended to the slug to make it unique
        while($this->doesAttributeTaxonomyExist($slug) || $this->isAttributeTaxonomySlugReserved($slug)) {
            $try++;

            if ($try >= $maxTry) return null;

            if (strlen($slug) > $this->maxSlugLength - $postfixLength) {
                $slug = substr($slug, 0, $this->maxSlugLength - $postfixLength);

                // Return null if an error occurs.
                if ($slug === false) return null;
            }

            // In case of 16 different characters, 16^($postfixLength) unique postfixes are available.
            $slug .= $this->generateRandomString($postfixLength);
        }

        return $slug;
    }

    /**
     * Check if an attribute taxonomy already exists using its slug.
     *
     * @param string|null $slug Slug of the attribute taxonomy to be checked for existence.
     * @return false|int Taxonomy ID if attribute taxonomy exists. Otherwise, false.
     * @since 1.9.0
     */
    private function doesAttributeTaxonomyExist($slug) {
        // WooCommerce allows the same attribute name to be used as long as its slug is unique. Hence, checking the
        // existence using the slug is the right way to do this.

        // If the slug is null or an empty string, return false.
        if ($slug === null || strlen((string) $slug) === 0) return false;

        // Get the existing taxonomies
        $existingTaxonomies = $this->getExistingAttributeTaxonomies();

        // If there are no existing taxonomies, then the slug does not exist.
        if (!$existingTaxonomies) return false;

        // WooCommerce stores the taxonomies lowercase. So, to compare the slug correctly, it should also be lowercase.
        $slug = strtolower($slug);

        // Try to find the slug in the existing attribute taxonomies.
        foreach($existingTaxonomies as $taxonomy) {
            if ($slug == $taxonomy->attribute_name) {
                return $taxonomy->attribute_id;
            }
        }

        return false;
    }

    /**
     * Get existing WooCommerce attribute taxonomies as an array.
     *
     * @return array See {@link existingAttributeTaxonomies}.
     * @since 1.9.0
     */
    private function getExistingAttributeTaxonomies() {
        if ($this->existingAttributeTaxonomies === null) {
            // This function does not have an @since value. So, it is probably available from the very early versions
            // of WooCommerce.
            $results = wc_get_attribute_taxonomies();

            // Make sure the result is an array.
            $this->existingAttributeTaxonomies = $results ?: [];
        }

        return $this->existingAttributeTaxonomies;
    }

    /**
     * Get attribute taxonomy slug by using its ID.
     *
     * @param int $id ID of the attribute taxonomy.
     * @return null|string If found, slug of the taxonomy attribute. Otherwise, null.
     * @since 1.9.0
     */
    private function getExistingAttributeTaxonomySlugById($id) {
        foreach($this->getExistingAttributeTaxonomies() as $taxonomy) {
            if ($taxonomy->attribute_id == $id) {
                return $taxonomy->attribute_name;
            }
        }

        return null;
    }

    /**
     * Get attribute taxonomy slug by using its ID.
     *
     * @param string $name Name of the attribute taxonomy.
     * @return null|int If found, slug of the taxonomy attribute. Otherwise, null.
     * @since 1.9.0
     */
    private function getExistingAttributeTaxonomyIdByName($name) {
        foreach($this->getExistingAttributeTaxonomies() as $taxonomy) {
            if ($taxonomy->attribute_label == $name) {
                return $taxonomy->attribute_id;
            }
        }

        return null;
    }

    /**
     * Checks if {@link \WC_Product_Attribute} class exists and throws an exception if it does not.
     *
     * @throws Exception If the class does not exist.
     * @since 1.9.0
     */
    private function makeSureWCProductAttributeClassExists(): void {
        if (!class_exists(WC_Product_Attribute::class)) {
            throw new Exception(sprintf('Class %1$s does not exist.', WC_Product_Attribute::class));
        }
    }

    /**
     * Generate a random string with desired length
     *
     * @param int $length Length of string to be generated. Max: 32
     * @return string Generated string
     * @since 1.9.0
     * @see https://stackoverflow.com/a/19590978/2883487
     */
    private function generateRandomString($length) {
        return substr(str_shuffle(md5(microtime())), 0, max(0, min($length, 32)));
    }

    /**
     * Check if a taxonomy slug is reserved, which means it cannot be assigned to an attribute.
     *
     * @param string $slug Slug to be checked
     * @return bool True if the slug is reserved. Otherwise, false.
     * @since 1.9.0
     */
    private function isAttributeTaxonomySlugReserved($slug) {
        // The function below exists since WooCommerce 2.4.0. Hence, it is safe to use.
        return wc_check_if_attribute_name_is_reserved($slug);
    }
}