<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/10/2019
 * Time: 11:10
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Exceptions;


use Exception;

class AlreadySavedPageException extends Exception {

    /**
     * @var array WordPress post data that can be directly used to update the post
     */
    private $wpPostData;

    /**
     * @return array See {@link $wpPostData}
     * @since 1.9.0
     */
    public function getWpPostData(): array {
        return $this->wpPostData;
    }

    /**
     * @param array $wpPostData See {@link $wpPostData}
     * @return AlreadySavedPageException
     * @since 1.9.0
     */
    public function setWpPostData(array $wpPostData): AlreadySavedPageException {
        $this->wpPostData = $wpPostData;
        return $this;
    }


}