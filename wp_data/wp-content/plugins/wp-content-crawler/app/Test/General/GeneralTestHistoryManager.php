<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 03/11/2018
 * Time: 09:24
 */

namespace WPCCrawler\Test\General;


use WPCCrawler\Environment;
use WPCCrawler\Factory;
use WPCCrawler\Test\Data\GeneralTestData;
use WPCCrawler\Utils;

class GeneralTestHistoryManager {

    /** @var string */
    private $testHistoryOptionName = '_wpcc_test_history';

    /** @var int Maximum number of test items that can be stored */
    private $maxTestItemCount = 200;

    /**
     * GeneralTestHistoryManager constructor.
     *
     * @since 1.9.0
     */
    public function __construct() {
        // If this is demo, lower the maximum allowed test history items
        if (Environment::isDemo()) {
            $this->maxTestItemCount = 100;
        }
    }

    public function getTestHistory(): array {
        $history    = get_option($this->testHistoryOptionName, []);
        $tests      = array_flip(Factory::testController()->getGeneralTestTypes());
        $sitesArr   = Utils::getSitesForSelect();

        // Make sure the option value is an array
        if (!is_array($history)) {
            $history = [];
        }

        foreach($history as $k => &$h) {
            // If the test key exists, add test name to the history item.
            if(isset($tests[$h['testKey']])) {
                $h['testName'] = $tests[$h['testKey']];
                $h['siteName'] = Utils::array_get($sitesArr, $h['siteId'], null);

            // Otherwise, remove the item.
            } else {
                unset($history[$k]);
            }
        }

        return $history;
    }

    /**
     * @param int $siteId       ID of the tested site.
     * @param string $testKey   Key of the test type.
     * @param string $testUrl   Test URL.
     * @return array|null       If item is successfully added, new history. Otherwise, null.
     */
    public function addItemToHistory($siteId, $testKey, $testUrl) {
        $newItem = [
            'siteId'    => $siteId,
            'testKey'   => $testKey,
            'testUrl'   => $testUrl
        ];

        // First, remove the item from the history if it exists.
        $history = $this->removeItemFromHistory($siteId, $testKey, $testUrl, false) ?: [];

        // Now, prepend new item to the history.
        array_unshift($history, $newItem);

        // Make sure the count is less than the maximum
        if (sizeof($history) > $this->maxTestItemCount) {
            $history = array_slice($history, 0, $this->maxTestItemCount);
        }

        return update_option($this->testHistoryOptionName, $history) ? $history : null;
    }

    /**
     * Adds a new item to test history using the given general test data object
     *
     * @param GeneralTestData $data
     * @return array|null Returns what {@link addItemToHistory} returns.
     */
    public function addItemToHistoryWithGeneralTestData($data) {
        return $this->addItemToHistory($data->getSiteId(), $data->getTestType(), $data->getTestUrl());
    }

    /**
     * @param int    $siteId  ID of the tested site.
     * @param string $testKey Key of the test type.
     * @param string $testUrl Test URL.
     * @param bool   $update  True if you want to update the database with the new history.
     * @return array|null       If item is successfully added, new history. Otherwise, null.
     */
    public function removeItemFromHistory($siteId, $testKey, $testUrl, $update = true) {
        $history = $this->getTestHistory();

        foreach($history as $k => $h) {
            if($h['siteId'] == $siteId && $h['testKey'] == $testKey && $h['testUrl'] == $testUrl) {
                unset($history[$k]);
            }
        }

        // If the value should not be updated in the database, return the history.
        if(!$update) return $history;

        // Update and return the history
        return update_option($this->testHistoryOptionName, $history) ? $history : null;
    }

    /**
     * Clears the test history.
     *
     * @return bool True if the history is cleared. Otherwise, false.
     */
    public function clearTestHistory() {
        return update_option($this->testHistoryOptionName, []);
    }

}