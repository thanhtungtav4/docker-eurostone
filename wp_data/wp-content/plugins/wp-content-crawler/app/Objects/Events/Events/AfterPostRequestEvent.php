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
class AfterPostRequestEvent extends AbstractCrawlingEvent {

    public function getEventGroup(): string {
        return EventGroupKey::POST_REQUEST;
    }

    public function getKey(): string {
        return EventKey::AFTER_POST_REQUEST;
    }

    public function getName(): string {
        return _wpcc('After post request is made');
    }

    public function getDescription(): string {
        return _wpcc('Executed after the request to crawl a post page is made');
    }

}