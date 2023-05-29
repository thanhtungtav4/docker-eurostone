<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 16/04/2019
 * Time: 18:40
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Enums;


/**
 * Defines the constants that can be used to indicate the data structure of a value, i.e. the type of data a setting or
 * any other thing stores.
 *
 * @since 1.8.1
 * @since 1.11.0 Move to WPCCrawler\Objects\Enums namespace, rename to ValueType, make it abstract, add T_NUMERIC and
 *               T_ANY constants
 */
abstract class ValueType {

    const T_ANY          = -1; // The value can be of any type
    const T_NO_VAL       = 0;
    const T_ARRAY        = 1;
    const T_STRING       = 2;
    const T_INTEGER      = 3;  // An int
    const T_BOOLEAN      = 4;  // A bool
    const T_FLOAT        = 5;  // A float
    const T_DATE_STR     = 6;  // A date string in MySQL date format
    const T_NUMERIC      = 7;  // A numeric value. It can be a string, a float or an int
    const T_POST_PAGE    = 8;  // A custom type used to select only post page commands in filters' subject select
    const T_JSON         = 9;  // A JSON string
    const T_ELEMENT      = 10; // A Crawler object
    const T_DATE         = 11; // A DateTime object
    const T_CRAWLING     = 12; // A custom type used to select only crawling commands in filters' subject select
    const T_NOTIFICATION = 13; // A custom type to select only notification commands in filters' subject select
    const T_REQUEST      = 14; // A custom type to select only request commands in filters' subject select
    const T_EXCEPTION    = 15; // An Exception
    const T_COUNTABLE    = 16; // An array or an object implementing Countable interface

}