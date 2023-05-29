<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/12/2018
 * Time: 09:53
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Interfaces;


interface Preparer {

    /**
     * Prepare.
     *
     * @return mixed
     */
    public function prepare();

}