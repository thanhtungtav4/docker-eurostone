<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 14/03/2020
 * Time: 14:18
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Enums;


/**
 * These keys are used to define what property of a subject should be used as the value in the commands. For example,
 * LENGTH means that the subject's length should be used. In case of a command that checks whether the length of the
 * post title is greater than some value, the subject is "post title", and the property is "length", it means that the
 * "length" of the "post title" should be used as the value when checking if it is "greater than" the defined value or
 * not.
 *
 * @since 1.11.0
 */
abstract class PropertyKey {

    const STR_CHAR_LENGTH = 'str_char_length';
    const STR_WORD_LENGTH = 'str_word_length';

    const COUNT  = 'count';

    const NUMERIC_VALUE = 'str_numeric_value';

    const ELEMENT_ATTR_VALUE         = 'el_attr_val';
    const ELEMENT_NUMERIC_ATTR_VALUE = 'el_numeric_attr_val';
    const ELEMENT_HTML               = 'el_html';
    const ELEMENT_TEXT               = 'el_text';
    const ELEMENT_NUMERIC_TEXT       = 'el_numeric_text';
    const ELEMENT_TAG_NAME           = 'el_tag_name';

    const STRING_ELEMENT_ATTR_VALUE         = 'str_el_attr_val';
    const STRING_ELEMENT_NUMERIC_ATTR_VALUE = 'str_el_numeric_attr_val';

    const REQUEST_ERROR    = 'request_error';
    const HTTP_STATUS_CODE = 'http_status_code';

}