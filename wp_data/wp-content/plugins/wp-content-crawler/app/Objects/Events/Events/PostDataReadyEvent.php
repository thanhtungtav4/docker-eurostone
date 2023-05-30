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


use WPCCrawler\Objects\Events\Base\AbstractPostCrawlingEvent;
use WPCCrawler\Objects\Events\Enums\EventKey;

/**
 * @since 1.11.0
 */
class PostDataReadyEvent extends AbstractPostCrawlingEvent {

    public function getKey(): string {
        return EventKey::POST_DATA_READY;
    }

    public function getName(): string {
        return _wpcc('When the post data is ready');
    }

    public function getDescription(): string {
        return _wpcc('Executed just after the post data is ready, before translating or spinning the post');
    }

}