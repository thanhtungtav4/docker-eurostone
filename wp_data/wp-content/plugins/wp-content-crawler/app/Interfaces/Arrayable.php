<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/03/2020
 * Time: 19:56
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Interfaces;


interface Arrayable {

    /**
     * @return array Array representation of this
     * @since 1.11.0
     */
    public function toArray(): array;

}