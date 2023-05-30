<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 01/04/2020
 * Time: 09:04
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Events\Enums;


/**
 * Contains keys that define event groups. These keys are used to group many events. Essentially, when a filter setting
 * is defined in {@link SettingKey}, an event group key is created here. A filter setting is created probably because
 * it needs to have event types different from the other filter settings. Hence, an event group key is created when a
 * new filter setting is created. This is not mandatory, but this is typically the case. Event keys are defined in
 * {@link EventKey}.
 *
 * @since 1.11.0
 */
abstract class EventGroupKey {

    const CATEGORY_REQUEST = 'category_after_request';
    const CATEGORY_DATA    = 'category_data';
    const CATEGORY_PAGE    = 'category_page';
    const POST_REQUEST     = 'post_after_request';
    const POST_DATA        = 'post_data';
    const POST_PAGE        = 'post_page';

}