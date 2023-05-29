<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/04/2020
 * Time: 23:09
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Events\Base;


use WPCCrawler\Objects\Events\Enums\EventGroupKey;

abstract class AbstractPostCrawlingEvent extends AbstractCrawlingEvent {

    public function getEventGroup(): string {
        return EventGroupKey::POST_DATA;
    }

}