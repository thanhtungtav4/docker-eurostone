<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 14/03/2020
 * Time: 14:27
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Enums;


/**
 * The unique identifiers for the commands
 *
 * @since 1.11.0
 */
abstract class CommandKey {

    /*
     * CONDITION COMMANDS
     */

    const GREATER_THAN          = 'greater_than';
    const LESS_THAN             = 'less_than';
    const GREATER_THAN_OR_EQUAL = 'greater_than_or_equal';
    const LESS_THAN_OR_EQUAL    = 'less_than_or_equal';
    const EQUAL_TO              = 'equal_to';
    const NOT_EQUAL_TO          = 'not_equal_to';

    const TEXT_STARTS_WITH          = 'text_starts_with';
    const TEXT_DOES_NOT_START_WITH  = 'text_does_not_start_with';
    const TEXT_ENDS_WITH            = 'text_ends_with';
    const TEXT_DOES_NOT_END_WITH    = 'text_does_not_end_with';
    const TEXT_CONTAINS             = 'text_contains';
    const TEXT_DOES_NOT_CONTAIN     = 'text_does_not_contain';
    const TEXT_IS_UPPERCASE         = 'text_is_uppercase';
    const TEXT_IS_NOT_UPPERCASE     = 'text_is_not_uppercase';
    const TEXT_IS_LOWERCASE         = 'text_is_lowercase';
    const TEXT_IS_NOT_LOWERCASE     = 'text_is_not_lowercase';
    const TEXT_MATCHES_REGEX        = 'text_matches_regex';
    const TEXT_DOES_NOT_MATCH_REGEX = 'text_does_not_match_regex';

    const ELEMENT_EXISTS         = 'element_exists';
    const ELEMENT_DOES_NOT_EXIST = 'element_does_not_exist';

    const DATE_IS_OLDER_THAN_RELATIVE   = 'date_is_older_than_relative';
    const DATE_IS_OLDER_THAN_FIXED      = 'date_is_older_than_fixed';
    const DATE_IS_NEWER_THAN_RELATIVE   = 'date_is_newer_than_relative';
    const DATE_IS_NEWER_THAN_FIXED      = 'date_is_newer_than_fixed';

    const RECRAWLING = 'recrawling';
    const CRAWLING   = 'crawling';
    const FIRST_PAGE = 'first_page';

    const REQUEST_ERROR = 'request_error';
    const HTML_ERROR    = 'html_error';
    const ERROR         = 'error';

    /*
     * ACTION COMMANDS
     */

    const TEXT_CLEAR                      = 'text_clear';
    const TEXT_FIND_REPLACE               = 'text_find_replace';
    const TEXT_MAKE_UPPER_CASE            = 'text_make_upper_case';
    const TEXT_MAKE_LOWER_CASE            = 'text_make_lower_case';
    const TEXT_MAKE_TITLE_CASE            = 'text_make_title_case';
    const TEXT_MAKE_SNAKE_CASE            = 'text_make_snake_case';
    const TEXT_MAKE_KEBAB_CASE            = 'text_make_kebab_case';
    const TEXT_MAKE_CAMEL_CASE            = 'text_make_camel_case';
    const TEXT_MAKE_STUDLY_CASE           = 'text_make_studly_case';
    const TEXT_MAKE_UC_FIRST              = 'text_make_uc_first';
    const TEXT_MAKE_SLUG                  = 'text_make_slug';
    const TEXT_LIMIT_WORDS                = 'text_limit_words';
    const TEXT_LIMIT_CHARS                = 'text_limit_chars';
    const TEXT_REMOVE_EMPTY_HTML_ELEMENTS = 'text_remove_empty_html_elements';
    const TEXT_REMOVE_LINKS               = 'text_remove_links';
    const TEXT_TEMPLATE                   = 'text_template';

    const REMOVE_ELEMENT         = 'remove_element';
    const ELEMENT_EXCHANGE_ATTRS = 'element_exchange_attrs';
    const ELEMENT_REMOVE_LINKS   = 'element_remove_links';
    const ELEMENT_REMOVE_ATTRS   = 'element_remove_attrs';
    const ELEMENT_CLONE          = 'element_clone';

    const CALCULATE = 'calculate';

    const SEND_EMAIL_NOTIFICATION = 'send_email_notification';

    const STOP                 = 'stop';
    const STOP_AND_DELETE_POST = 'stop_and_delete_post';

    // Post page action commands
    const SET_AUTHOR         = 'set_author';
    const SET_POST_STATUS    = 'set_post_status';
    const SET_FEATURED_IMAGE = 'set_featured_image';
    const ADD_TAGS           = 'add_tags';
    const ADD_CATEGORIES     = 'add_categories';
}