<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 19/11/2020
 * Time: 19:50
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Exceptions;


use Exception;

class CancelSavingAndDeleteException extends Exception {

    /**
     * @var bool True if the URL should be deleted from the database. False if the URL should remain in the database
     *      and never be crawled again.
     */
    private $deleteUrl;

    /**
     * @return bool See {@link deleteUrl}
     * @since 1.11.0
     */
    public function isDeleteUrl(): bool {
        return $this->deleteUrl;
    }

    /**
     * @param bool $deleteUrl See {@link deleteUrl}
     * @return CancelSavingAndDeleteException
     * @since 1.11.0
     */
    public function setDeleteUrl(bool $deleteUrl): CancelSavingAndDeleteException {
        $this->deleteUrl = $deleteUrl;
        return $this;
    }

}