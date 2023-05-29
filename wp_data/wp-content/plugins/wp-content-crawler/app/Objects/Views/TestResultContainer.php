<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/04/2020
 * Time: 09:27
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Views;


use WPCCrawler\Objects\Views\Base\AbstractView;
use WPCCrawler\Objects\Views\Enums\ViewKey;

class TestResultContainer extends AbstractView {

    public function getKey(): string {
        return ViewKey::TEST_RESULT_CONTAINER;
    }

    protected function createVariableNames(): ?array {
        return null;
    }

}