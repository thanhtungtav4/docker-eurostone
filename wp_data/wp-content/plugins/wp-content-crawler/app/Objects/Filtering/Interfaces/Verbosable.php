<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/05/2020
 * Time: 09:51
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Interfaces;


interface Verbosable {

    /**
     * @return bool True if this object should log extra details about its operations. Otherwise, false.
     * @since 1.11.0
     */
    public function isVerbose(): bool;

    /**
     * @param bool $verbose True if this object should log extra details about its operations. Otherwise, false.
     * @return self
     * @since 1.11.0
     */
    public function setVerbose(bool $verbose);

}