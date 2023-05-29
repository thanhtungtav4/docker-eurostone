<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 03/02/2021
 * Time: 11:25
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Enums;


/**
 * Stores the dot keys of special transformable fields
 *
 * @since 1.11.0
 */
abstract class SpecialFieldKey {

    const NOTIFICATION = 'notification';
    const REQUEST      = 'request';
    const CRAWLING     = 'crawling';
    const ELEMENT      = 'element';
    const POST_RELATED = 'postRelated';

}