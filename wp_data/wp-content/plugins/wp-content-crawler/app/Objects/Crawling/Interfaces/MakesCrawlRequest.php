<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 17/11/2020
 * Time: 11:25
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Crawling\Interfaces;


interface MakesCrawlRequest {

    /**
     * @return string|null The URL being crawled or having been crawled
     * @since 1.11.0
     */
    public function getCrawlingUrl(): ?string;

    /**
     * @return int|null HTTP status code of the response of the crawling request
     * @since 1.11.0
     */
    public function getResponseHttpStatusCode(): ?int;

}