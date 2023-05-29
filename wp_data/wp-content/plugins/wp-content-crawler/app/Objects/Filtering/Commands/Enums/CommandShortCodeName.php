<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 17/11/2020
 * Time: 12:21
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\Enums;


abstract class CommandShortCodeName {

    const REQUEST_URL   = 'wcc-request-url';    // string       URL of the request made to crawl a page
    const STATUS_CODE   = 'wcc-status-code';    // int|string   HTTP status code of the response
    const SITE_NAME     = 'wcc-site-name';      // string       Name of the site
    const SITE_ID       = 'wcc-site-id';        // int|string   ID of the site
    const SITE_EDIT_URL = 'wcc-site-edit-url';  // string       URL that points to the edit page of the site
    const CURRENT_TIME  = 'wcc-current-time';   // string       Current date and time

    const ITEM          = 'wcc-item';           // mixed        Current item's value

}