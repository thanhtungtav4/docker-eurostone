<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 04/11/2018
 * Time: 15:04
 */

namespace WPCCrawler\Migrations\Base;


use WPCCrawler\Environment;
use WPCCrawler\Services\DatabaseService;

abstract class AbstractMigration {

    /** @var DatabaseService */
    private $dbService;

    /** @var string Version of the database in the site. */
    private $currentDbVersion;

    /** @var array|null Stores the IDs of posts of type WPCC site */
    private $siteIds = null;

    /** @var string|null */
    private $siteIdsWhereIn = null;

    /**
     * @param DatabaseService $dbService
     */
    public function __construct(DatabaseService $dbService) {
        $this->dbService = $dbService;
        $this->currentDbVersion = $dbService->getDbVersion();
    }

    /**
     * Get the target database version. The target database version is the database version for which this migration is
     * intended for. If the value returned in this method is greater than the site's database version, the migration
     * will be applied.
     *
     * @return string
     */
    protected abstract function getTargetDbVersion(): string;

    /**
     * Perform the migration.
     *
     * @return void
     */
    protected abstract function migrate(): void;

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public abstract function reverse(): void;

    /*
     * PUBLIC METHODS
     */

    /**
     * Performs the migration if it should be performed.
     */
    public function maybeMigrate(): void {
        // If the migration should not be performed, stop.
        if (!$this->shouldPerformMigration()) return;

        // The migration should be performed.

        $clazz = get_called_class();
        $this->log("Performing migration: {$clazz}");

        // Perform the migration
        $this->migrate();

        $this->log("Migration performed: {$clazz}");
    }

    /**
     * @return bool True if the migration should be performed.
     */
    public function shouldPerformMigration(): bool {
        return version_compare($this->currentDbVersion, $this->getTargetDbVersion(), '<');
    }

    /*
     * GETTERS
     */

    /**
     * @return DatabaseService
     */
    protected function getDatabaseService(): DatabaseService {
        return $this->dbService;
    }

    /*
     * PROTECTED HELPERS
     */

    /**
     * @return string Site IDs as a single string separated via comma. Ready for use in "where in" statement.
     */
    protected function getSiteIdsPreparedForWhereIn(): string {
        if ($this->siteIdsWhereIn === null) {
            $siteIds = $this->getSiteIds();

            // If there are site IDs
            if ($siteIds) {
                // Escape each site ID for SQL query
                $siteIds = array_map(function($v) {
                    return esc_sql($v);
                }, $siteIds);

                // Implode with comma and assign it to the instance variable
                $this->siteIdsWhereIn = implode(',', $siteIds);

                // Otherwise, make sure the instance variable is set to empty string so that this method will not try to
                // perform the same job later on when needed.
            } else {
                $this->siteIdsWhereIn = '';
            }
        }

        return $this->siteIdsWhereIn;
    }

    /**
     * @return array Site IDs. See {@link $siteIds}
     */
    protected function getSiteIds(): array {
        if ($this->siteIds === null) {
            global $wpdb;

            // Find IDs of the posts of site type. This will find all IDs regardless of post status, etc.
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_type = '%s'",
                Environment::postType()
            ), ARRAY_N);

            // If there are posts
            if ($results) {
                // Make sure the array is flat and contains only valid values
                $this->siteIds = array_filter(array_map(function($v) {
                    return isset($v[0]) ? $v[0] : null;
                }, $results));

                // Otherwise, make sure the site IDs is assigned as an empty array so that this method will not try to
                // find site IDs later on.
            } else {
                $this->siteIds = [];
            }

        }

        return $this->siteIds;
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * Adds an error log if this is not a testing environment.
     *
     * @param string $text
     * @since 1.9.0
     */
    private function log($text): void {
        // TODO: Do not hard-code the environment variable name. Assign to a variable. Do this for all environment variables.
        // No hard-coding.
        if (defined('WPCC_UNIT_TEST')) return;
        error_log($text);
    }

}