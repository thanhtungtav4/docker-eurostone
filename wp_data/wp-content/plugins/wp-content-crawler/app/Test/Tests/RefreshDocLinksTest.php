<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/11/2019
 * Time: 13:23
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Test\Tests;


use WPCCrawler\Objects\Docs;
use WPCCrawler\Test\Base\AbstractTest;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Utils;

class RefreshDocLinksTest extends AbstractTest {

    /**
     * @param TestData $data
     * @return array
     */
    protected function createResults($data): array {
        $refreshed = Docs::getInstance()->createLocalLabelIndexFile(30);
        $message = $refreshed
            ? _wpcc('Documentation links have been refreshed successfully')
            : _wpcc('Documentation links could not have been refreshed');

        return [$message];
    }

    /**
     * @inheritDoc
     */
    protected function createView() {
        return Utils::view('partials/test-result')
            ->with("results", $this->getResults())
            ->with("message", _wpcc('Results for refreshing documentation links') . ':');
    }
}