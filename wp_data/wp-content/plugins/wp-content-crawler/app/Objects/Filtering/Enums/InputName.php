<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 28/03/2020
 * Time: 11:35
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Enums;


/**
 * Stores the input names of the command views. The purpose is to make sure that we do not hard-code the names and that
 * we do not use the same name for different views. The names of the constants must be the upper-case version of their
 * values. By this way, we can immediately now when there is a duplicate name, since PHP will not allow to define two
 * constants with the same name.
 *
 * @since 1.11.0
 */
abstract class InputName {

    const NUMBER        = 'number';
    const TEST_NUMBER   = 'test_number';

    const TEXT             = 'text';
    const TEST_TEXT        = 'test_text';
    const CASE_INSENSITIVE = 'case_insensitive';
    const TREAT_AS_HTML    = 'treat_as_html';

    const CSS_SELECTOR  = 'css_selector';
    const ELEMENT_ATTR  = 'el_attr';
    const ELEMENT_ATTRS = 'el_attrs';

    const DATE        = 'date';
    const TEST_DATE   = 'test_date';

    const FORMULA = 'formula';

    /*
     *
     */

    const TEMPLATE           = 'template';
    const FIND_REPLACE       = 'find_replace';
    const AUTHOR_ID          = 'author_id';
    const FEATURED_IMAGE_IDS = 'featured_image_ids';
    const POST_STATUS        = 'post_status';

    const DECIMAL_SEPARATOR       = 'decimal_separator';
    const USE_THOUSANDS_SEPARATOR = 'use_thousands_separator';
    const PRECISION               = 'precision';

    const REMOVE_COMMENTS = 'remove_comments';
    const EXCLUDED_TAGS   = 'excluded_tags';

    const CLONE_ALL_FOUND_ELEMENTS = 'clone_all_found_elements';
    const ELEMENT_ID               = 'element_id';

    const NOTIFICATION_TITLE_TEMPLATE   = 'notification_title_template';
    const NOTIFICATION_MESSAGE_TEMPLATE = 'notification_message_template';
    const NOTIFICATION_ID               = 'notification_id';
    const NOTIFICATION_INTERVAL         = 'notification_interval';

    const REASON     = 'reason';
    const DELETE_URL = 'delete_url';

    const INVALID_DOMAIN = 'invalid_domain';
    const VALID_DOMAIN   = 'valid_domain';

    const DELETE_EXISTING = 'delete_existing';
    const CATEGORIES      = 'categories';

}