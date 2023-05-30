<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 01/04/2020
 * Time: 19:36
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Events\Enums;


abstract class EventKey {

    const POST_DATA_READY       = 'post_data_ready';
    const CATEGORY_DATA_READY   = 'category_data_ready';

    const AFTER_TRANSLATION     = 'after_translation';
    const AFTER_SPINNING        = 'after_spinning';

    const AFTER_POST_CRAWLER_READY      = 'after_post_crawler_ready';
    const AFTER_CATEGORY_CRAWLER_READY  = 'after_category_crawler_ready';

    const AFTER_CATEGORY_REQUEST  = 'after_category_request';
    const AFTER_POST_REQUEST      = 'after_post_request';

}