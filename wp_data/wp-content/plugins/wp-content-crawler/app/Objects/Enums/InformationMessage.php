<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/10/2018
 * Time: 18:55
 */

namespace WPCCrawler\Objects\Enums;


class InformationMessage extends EnumBase {

    const INFO                              = 'info';
    const ERROR                             = 'error';
    const FAIL                              = 'fail';
    const URL_TUPLE_NOT_EXIST               = 'url_tuple_does_not_exist';
    const URL_LOCKED                        = 'url_is_locked';
    const URL_COULD_NOT_BE_FETCHED          = 'url_could_not_be_fetched';
    const DUPLICATE_POST                    = 'duplicate_post';
    const TRANSLATION_ERROR                 = 'translation_error';
    const SPINNING_ERROR                    = 'spinning_error';
    const VALUE_NOT_NUMERIC_ERROR           = 'value_not_numeric_error';
    const URL_NOT_FOUND                     = 'url_not_found';
    const REMOTE_SERVER_ERROR               = 'remote_server_error';
    const FILE_COULD_NOT_BE_SAVED_ERROR     = 'file_cannot_be_saved';
    const REQUEST_ERROR                     = 'request_error';
    const HTML_COULD_NOT_BE_RETRIEVED_ERROR = 'html_could_not_be_retrieved';
    const CONNECTION_ERROR                  = 'connection_error';
    const CSS_SELECTOR_SYNTAX_ERROR         = 'css_selector_syntax_error';
    const WOOCOMMERCE_ERROR                 = 'woocommerce_error';
    const TAXONOMY_DOES_NOT_EXIST           = 'taxonomy_does_not_exist';
    const FILE_NOT_EXIST                    = 'file_not_exist';
    const URI_COULD_NOT_BE_RESOLVED         = 'uri_could_not_be_resolved';

    /**
     * @param string $informationMessage One of the constants in {@link InformationMessage}
     * @return string
     */
    public static function getDescription($informationMessage) {
        if(!static::isValidValue($informationMessage)) return '';

        switch($informationMessage) {
            case static::INFO:
                return _wpcc('Information');

            case static::ERROR:
                return _wpcc('Error');

            case static::FAIL:
                return _wpcc('Fail');

            case static::URL_TUPLE_NOT_EXIST:
                return _wpcc("URL does not exist in the database.");

            case static::URL_LOCKED:
                return _wpcc("Current URL is locked. It means it is being used by another process.");

            case static::URL_COULD_NOT_BE_FETCHED:
                return _wpcc("Data could not be retrieved from the URL.");

            case static::DUPLICATE_POST:
                return _wpcc("Duplicate post.");

            case static::TRANSLATION_ERROR:
                return _wpcc("Translation error.");

            case static::SPINNING_ERROR:
                return _wpcc("Spinning error.");

            case static::VALUE_NOT_NUMERIC_ERROR:
                return _wpcc("Value is not numeric.");

            case static::URL_NOT_FOUND:
                return _wpcc("URL is not found");

            case static::FILE_COULD_NOT_BE_SAVED_ERROR:
                return _wpcc("File could not be saved");

            case static::REQUEST_ERROR:
                return _wpcc("Request error");

            case static::HTML_COULD_NOT_BE_RETRIEVED_ERROR:
                return _wpcc("HTML could not be retrieved");

            case static::CONNECTION_ERROR:
                return _wpcc("Connection error");

            case static::CSS_SELECTOR_SYNTAX_ERROR:
                return _wpcc("CSS selector syntax error");

            case static::WOOCOMMERCE_ERROR:
                return _wpcc("WooCommerce error");

            case static::TAXONOMY_DOES_NOT_EXIST:
                return _wpcc("Taxonomy does not exist");

            case static::FILE_NOT_EXIST:
                return _wpcc("File does not exist");

            case static::URI_COULD_NOT_BE_RESOLVED:
                return _wpcc("URI could not be resolved");

            default:
                return '';
        }
    }

}