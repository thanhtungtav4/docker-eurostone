<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 01/04/2020
 * Time: 22:02
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Events\Interfaces;


interface Observer {

    /**
     * @param Event $event
     * @since 1.11.0
     */
    public function update(Event $event): void;

}