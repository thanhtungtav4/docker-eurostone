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
class CategoryDataReadyEvent extends AbstractCrawlingEvent {

    public function getEventGroup(): string {
        return EventGroupKey::CATEGORY_DATA;
    }

    public function getKey(): string {
        return EventKey::CATEGORY_DATA_READY;
    }

    public function getName(): string {
        return _wpcc('When the category data is ready');
    }

    public function getDescription(): string {
        return _wpcc('Executed just after the category data is ready');
    }

}