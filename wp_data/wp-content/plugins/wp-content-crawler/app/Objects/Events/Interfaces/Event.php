<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 01/04/2020
 * Time: 22:05
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Events\Interfaces;


use Exception;

interface Event {

    /**
     * Attach an observer to this event
     *
     * @param Observer|null $observer
     * @since 1.11.0
     */
    public function attach(?Observer $observer): void;

    /**
     * Detach a previously attached observer from this event
     *
     * @param Observer|null $observer
     * @since 1.11.0
     */
    public function detach(?Observer $observer): void;

    /**
     * Notify all attached observers
     *
     * @since 1.11.0
     * @throws Exception
     */
    public function notify(): void;

}