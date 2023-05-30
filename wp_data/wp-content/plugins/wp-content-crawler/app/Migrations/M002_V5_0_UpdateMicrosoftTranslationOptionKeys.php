<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 28/01/2019
 * Time: 20:46
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Migrations;


use WPCCrawler\Migrations\Base\AbstractMigration;
use wpdb;

/**
 * @since 1.9.0
 */
class M002_V5_0_UpdateMicrosoftTranslationOptionKeys extends AbstractMigration {

    /**
     * Get the target database version. The target database version is the database version for which this migration is
     * intended for. If the value returned in this method is greater than the site's database version, the migration
     * will be applied.
     *
     * @return string
     */
    protected function getTargetDbVersion(): string {
        return "5.0";
    }

    /**
     * @return array A key-value pair where keys are old meta keys, and the values are the new meta keys.
     * @since 1.9.0
     */
    private function getPostKeyRestructuringMap(): array {
        // From now on, each translation API client class has a unique key. These keys are used to standardize the
        // operations that are done using the API clients. By standardization, we can decouple implementation from
        // specific translation API clients. By this way, we apply open/close principle for translation API
        // implementations. We will be able to add new translation APIs to the plugin without modifying the
        // implementation.
        //
        // The identifier used for Microsoft Translator Text API is 'microsoft_translator_text'. However, in the
        // settings, post meta keys or option keys are defined using 'microsoft_translate' identifier. Here, we replace
        // those settings that do not use the identifier with the correct identifier so that the same identifier is
        // used everywhere.
        //
        // The identifier for Google Cloud Translation API is the same everywhere. Hence, no need to make any changes to
        // them.

        $oldPrefix = 'microsoft_translate';
        $newPrefix = 'microsoft_translator_text';
        $metaKeyFormat = '_wpcc_translation_%1$s_%2$s';

        $map = [];
        $postfixes = ['from', 'to', 'client_secret', 'test'];
        foreach($postfixes as $postfix) {
            $oldKey = sprintf($metaKeyFormat, $oldPrefix, $postfix);
            $newKey = sprintf($metaKeyFormat, $newPrefix, $postfix);

            $map[$oldKey] = $newKey;
        }
        
        return $map;
    }

    /**
     * Perform the migration.
     *
     * @return void
     */
    protected function migrate(): void {
        foreach($this->getPostKeyRestructuringMap() as $oldKey => $newKey) {
            $this->migrateSingleItem($oldKey, $newKey);
        }
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function reverse(): void {
        foreach($this->getPostKeyRestructuringMap() as $oldKey => $newKey) {
            $this->reverseSingleItem($oldKey, $newKey);
        }
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Changes a single key with its new name.
     *
     * @param string $oldKey Old key of the item. This will be changed with the new key.
     * @param string $newKey New key of the item
     * @since 1.9.0
     */
    public function migrateSingleItem($oldKey, $newKey): void {
        $this->updateSingleItem($oldKey, $newKey);
    }

    /**
     * Reverses the change made in the key using {@link migrateSingleItem()}.
     *
     * @param string $oldKey Old key of the item
     * @param string $newKey New key of the item. This will be reversed to the old key.
     * @since 1.9.0
     */
    public function reverseSingleItem($oldKey, $newKey): void {
        $this->updateSingleItem($newKey, $oldKey);
    }

    /**
     * Change a key.
     *
     * @param string $find    The key to find in both post_meta and options tables
     * @param string $replace New value of the key
     * @since 1.9.0
     * @noinspection PhpRedundantVariableDocTypeInspection
     */
    private function updateSingleItem($find, $replace): void {
        // Consider both post meta keys and option keys since translation options are available both in site settings
        // and general settings.

        /** @var wpdb $wpdb */
        global $wpdb;

        // Update post meta keys of the sites
        $siteIdsPrepared = $this->getSiteIdsPreparedForWhereIn();
        if ($siteIdsPrepared) {
            // @phpstan-ignore-next-line
            $result = $wpdb->query($wpdb->prepare(
                "UPDATE $wpdb->postmeta SET meta_key = '%s' WHERE meta_key = '%s' AND post_id IN ($siteIdsPrepared)",
                $replace,
                $find
            ));

            // Log if there was an error
            if ($result === false) {
                error_log("An error occurred when updating '$find' to '$replace' for sites with IDs $siteIdsPrepared.");
            }
        }

        // Update options
        // @phpstan-ignore-next-line
        $result = $wpdb->query($wpdb->prepare(
            "UPDATE $wpdb->options SET option_name = '%s' WHERE option_name = '%s'",
            $replace,
            $find
        ));

        // Log if there was an error
        if ($result === false) {
            error_log("An error occurred when updating '$find' to '$replace' in options table.");
        }
    }
}