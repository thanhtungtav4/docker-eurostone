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
class AfterSpinningEvent extends AbstractPostCrawlingEvent {

    public function getKey(): string {
        return EventKey::AFTER_SPINNING;
    }

    public function getName(): string {
        return _wpcc('After spinning');
    }

    public function getDescription(): string {
        return _wpcc('Executed after the post is spun, whether the spinning is active or not');
    }

}