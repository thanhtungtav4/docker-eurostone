<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/12/2018
 * Time: 18:47
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\WooCommerce;


use WPCCrawler\PostDetail\Base\BasePostDetailDuplicateChecker;
use WPCCrawler\Objects\Crawling\Data\PostSaverData;

class WooCommerceDuplicateChecker extends BasePostDetailDuplicateChecker {

    const TYPE_SKU = 'wc-sku';

    /**
     * Create options that will be shown in "duplicate check types" option.
     *
     * @return null|array A key-value pair. Keys are the keys of the options, values are the names that will be shown
     *                    to the user.
     * @since 1.8.0
     */
    protected function createOptions(): ?array {
        return [
            "values" => [
                static::TYPE_SKU => _wpcc("SKU")
            ],
            "defaults" => [
                static::TYPE_SKU => 1
            ],
        ];
    }

    /**
     * Implement the logic for checking if the post is duplicate.
     *
     * @param PostSaverData $saverData Data that stores information that can be used for duplicate checking
     * @param array         $values    An array that stores the duplicate check options selected by the user.
     * @return int|false ID of the post if this is duplicate. Otherwise, false.
     * @since 1.8.0
     */
    public function checkForDuplicate(PostSaverData $saverData, array $values) {
        global $wpdb;

        // Do not check for duplicate SKU if it is not enabled.
        if (!isset($values[static::TYPE_SKU])) return false;

        /** @var WooCommerceData|null $data */
        $data = $this->getDetailData();

        // Make sure the product has a SKU. Otherwise, return false since this is not a duplicate.
        if (!$data || !$data->getSku()) return false;

        // Try to find a product with the same SKU.
        $query = $wpdb->prepare(
            "SELECT posts.ID
				FROM $wpdb->posts AS posts
				LEFT JOIN $wpdb->postmeta AS postmeta ON ( posts.ID = postmeta.post_id )
				WHERE posts.post_type IN ('product', 'product_variation')
					AND posts.post_status != 'trash'
					AND postmeta.meta_key = '_sku'
					AND postmeta.meta_value = %s
				LIMIT 1",
            $data->getSku()
        );

        // Get the ID
        $id = $wpdb->get_var($query);

        // If there is not an ID, then return false to indicate that this is not a duplicate product. Otherwise,
        // return the ID of the duplicate post.
        return $id === null ? false : $id;
    }


}