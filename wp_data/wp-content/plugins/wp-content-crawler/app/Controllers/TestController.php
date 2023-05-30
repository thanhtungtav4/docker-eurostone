<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 14:58
 */

namespace WPCCrawler\Controllers;


use Illuminate\Contracts\View\View;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Enums\PageSlug;
use WPCCrawler\Objects\Page\AbstractMenuPage;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Permission;
use WPCCrawler\Test\Enums\TestType;
use WPCCrawler\Test\General\GeneralTestHistoryManager;
use WPCCrawler\Test\Test;
use WPCCrawler\Utils;

class TestController extends AbstractMenuPage {

    /** @var array|null */
    private static $GENERAL_TESTS;

    /**
     * @return string Menu title for the page
     */
    public function getMenuTitle(): string {
        return _wpcc('Tester');
    }

    /**
     * @return string Page title
     */
    public function getPageTitle(): string {
        return _wpcc('Tester');
    }

    /**
     * @return string Slug for the page
     */
    public function getPageSlug(): string {
        return PageSlug::SITE_TESTER;
    }

    /**
     * @return bool True if the page is allowed for the current user. Otherwise, false.
     * @since 1.9.0
     */
    protected function isAllowed(): bool {
        return Permission::canViewTester();
    }

    /**
     * Get view for the page.
     *
     * @return View Not-rendered blade view for the page
     */
    public function getView() {
        // Register assets
        Factory::assetManager()->addGuides();
        Factory::assetManager()->addTooltip();
        Factory::assetManager()->addBootstrapGrid();
        Factory::assetManager()->addSiteTester();

        return Utils::view('site-tester/main')->with([
            'sites'         => Utils::getSites(),
            'categoryUrls'  => $this->getCategoryUrlsForSites(),
            'testHistory'   => (new GeneralTestHistoryManager())->getTestHistory()
        ]);
    }

    public function handleAJAX(): void {
        parent::handleAJAX();
        $data = $this->getAjaxData();

        // If the data has 'cmd', handle it.
        $cmd = Utils::array_get($data, 'cmd', null);
        if ($cmd) {
            switch ($cmd) {
                // Delete the history item
                case 'delete_history_item':
                    // Get the item data.
                    $itemData = Utils::array_get($data, 'item');

                    // If there is no item data, return null.
                    if(!$itemData) return;

                    $siteId  = Utils::array_get($itemData, 'siteId');
                    $testKey = Utils::array_get($itemData, 'testKey');
                    $testUrl = Utils::array_get($itemData, 'testUrl');

                    // All information must exist.
                    if (!$siteId || !$testKey || !$testUrl) return;

                    // Remove the item from history
                    $historyHandler = new GeneralTestHistoryManager();
                    $historyHandler->removeItemFromHistory($siteId, $testKey, $testUrl);

                    // Create test history view
                    $testHistoryView = Utils::view('site-tester.test-history')
                        ->with('testHistory', $historyHandler->getTestHistory());

                    echo json_encode([
                        'view' => $testHistoryView->render()
                    ]);

                    return;

                case 'delete_all_test_history':
                    // Clear the history
                    $historyHandler = new GeneralTestHistoryManager();
                    $historyHandler->clearTestHistory();

                    // Create test history view
                    $testHistoryView = Utils::view('site-tester.test-history')
                        ->with('testHistory', $historyHandler->getTestHistory());

                    echo json_encode([
                        'view' => $testHistoryView->render()
                    ]);

                    return;

                default:
                    // We could not find the command. Stop.
                    return;
            }
        }

        // Show the test results
        $testResult = Test::respondToGeneralTestRequest($data["site_id"], $data["test_type"], $data["test_url_part"]);
        if ($testResult !== null) {
            echo $testResult;
        }
    }

    /*
     * HELPERS
     */

    /**
     * Get general test types. This method exists, because translations are not ready before the page renders.
     *
     * @return array General test types as title,value pair
     */
    public function getGeneralTestTypes(): array {
        if(!static::$GENERAL_TESTS) static::$GENERAL_TESTS = [
            _wpcc('Post Page')      =>  TestType::POST,
            _wpcc('Category Page')  =>  TestType::CATEGORY,
        ];

        return static::$GENERAL_TESTS;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Get category URLs defined in the site settings.
     *
     * @return array An associative array where keys are site IDs and the values are array of category URLs.
     * @since 1.9.0
     */
    private function getCategoryUrlsForSites(): array {
        // Get category URLs defined in the settings for the sites
        global $wpdb;

        // Get category map settings' values from the database
        $metaKey = SettingKey::CATEGORY_MAP;
        $query = "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '{$metaKey}'";

        $results = $wpdb->get_results($query);
        if (!$results) return [];

        $categoryUrls = [];
        foreach($results as $result) {
            // Get the value of the setting by unserializing it since it is stored in serialized.
            $setting = unserialize($result->meta_value);

            // Create an array of URLs and store it under the site's ID.
            $categoryUrls[$result->post_id] = array_column($setting, SettingInnerKey::URL);
        }

        return $categoryUrls;
    }
}
