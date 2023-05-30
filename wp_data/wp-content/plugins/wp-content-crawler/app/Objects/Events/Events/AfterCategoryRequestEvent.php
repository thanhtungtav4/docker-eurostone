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
class AfterCategoryRequestEvent extends AbstractCrawlingEvent {

    public function getEventGroup(): string {
        return EventGroupKey::CATEGORY_REQUEST;
    }

    public function getKey(): string {
        return EventKey::AFTER_CATEGORY_REQUEST;
    }

    public function getName(): string {
        return _wpcc('After category request is made');
    }

    public function getDescription(): string {
        return _wpcc('Executed after the request to crawl a category page is made');
    }

}