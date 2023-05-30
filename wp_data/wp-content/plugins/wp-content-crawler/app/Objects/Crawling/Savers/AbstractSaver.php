<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 18/01/17
 * Time: 09:38
 */

namespace WPCCrawler\Objects\Crawling\Savers;


abstract class AbstractSaver {

    /** @var bool True if the URL request has been made after an event is executed, false otherwise. */
    private $requestMade = false;

    /**
     * @param bool $bool True if the request is made, false otherwise.
     */
    protected function setRequestMade($bool): void {
        $this->requestMade = $bool;
    }

    /**
     * @return bool See {@link requestMade}
     */
    public function isRequestMade() {
        return $this->requestMade;
    }
}