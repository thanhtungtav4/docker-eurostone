<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 04/11/2018
 * Time: 15:29
 */

namespace WPCCrawler\Migrations;


use WPCCrawler\Migrations\Base\AbstractMigration;
use WPCCrawler\Services\DatabaseService;

/**
 * Performs necessary database migrations.
 *
 * @package WPCCrawler\migrations
 */
class DatabaseMigrator {

    /** @var DatabaseService */
    private $dbService;

    /**
     * DatabaseMigrator constructor.
     *
     * @param DatabaseService $dbService
     */
    public function __construct(DatabaseService $dbService) {
        $this->dbService = $dbService;
    }


    /**
     * @return AbstractMigration[]
     */
    private function getMigrations(): array {
        // Create the migrations and return
        return [
            new M001_V4_0_UpdateSitePostMetaValues($this->dbService),
            new M002_V5_0_UpdateMicrosoftTranslationOptionKeys($this->dbService),
        ];
    }

    /**
     * Performs necessary database migrations.
     */
    public function migrate(): void {
        // Perform the migrations
        foreach($this->getMigrations() as $migration) {
            $migration->maybeMigrate();
        }
    }

}