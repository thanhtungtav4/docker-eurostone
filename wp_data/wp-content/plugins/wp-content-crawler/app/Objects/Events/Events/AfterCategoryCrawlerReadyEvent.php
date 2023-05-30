<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 01/04/2020
 * Time: 19:25
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Events\Events;


use WPCCrawler\Objects\Events\Base\AbstractCrawlingEvent;
use WPCCrawler\Objects\Events\Enums\EventGroupKey;
use WPCCrawler\Objects\Events\Enums\EventKey;

/**
 * @since 1.11.0
 */
class AfterCategoryCrawlerReadyEvent extends AbstractCrawlingEvent {

    public function getEventGroup(): string {
        return EventGroupKey::CATEGORY_PAGE;
    }

    public function getKey(): string {
        return EventKey::AFTER_CATEGORY_CRAWLER_READY;
    }

    public function getName(): string {
        return _wpcc('After category crawler is ready');
    }

    public function getDescription(): string {
        return _wpcc('Executed after all the HTML manipulations are finished and unnecessary elements are removed');
    }

}