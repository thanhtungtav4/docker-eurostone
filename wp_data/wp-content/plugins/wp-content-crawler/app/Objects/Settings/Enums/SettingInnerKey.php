<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 06/05/2019
 * Time: 13:49
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Settings\Enums;

/**
 * Stores the inner key types of multiple-input settings
 *
 * @since   1.8.1
 */
class SettingInnerKey {

    // TODO: REPLACE ALL HARD_CODED INNER KEYS WITH THE CONSTANTS BELOW
    // After replacing, add a checkbox under one of the columns on the right. "PHP Replaced" means that all values in
    // PHP classes are replaced. "All Replaced" means that hard-coded values in both PHP classes and Blade views are
    // replaced.

    //                                                                              PHP Replaced    All Replaced
    const SELECTOR      = "selector";   //                                              √               √
    const ATTRIBUTE     = "attr";       //                                              √               √
    const ATTRIBUTE_1   = "attr1";      //                                              √               √
    const ATTRIBUTE_2   = "attr2";      //                                              √               √

    const FIND          = "find";       //                                              √               √
    const REPLACE       = "replace";    //                                              √               √
    const REGEX         = "regex";      //                                              √               √

    const URL           = "url";        //                                              √               √
    const CATEGORY_ID   = "cat_id";
    const TITLE         = "title";
    const CONTENT       = "content";
    const SHORT_CODE    = "short_code";

    const SINGLE        = "single";
    const MULTIPLE      = "multiple";
    const APPEND        = "append";

    const KEY           = "key";
    const VALUE         = "value";

    const META_KEY      = "meta_key";
    const TAXONOMY      = "taxonomy";

    const DESCRIPTION   = 'description';
    const DOMAIN        = 'domain';

    const AS_TAXONOMY   = 'as_taxonomy';
    const ATTR_NAME     = 'attr_name';

    const OPTIONS_BOX_FIND_REPLACE              = 'find_replace';
    const OPTIONS_BOX_TREAT_AS_JSON             = 'treat_as_json';
    const OPTIONS_BOX_DECIMAL_SEPARATOR_AFTER   = 'decimal_separator_after';
    const OPTIONS_BOX_USE_THOUSANDS_SEPARATOR   = 'use_thousands_separator';
    const OPTIONS_BOX_REMOVE_IF_NOT_NUMERIC     = 'remove_if_not_numeric';
    const OPTIONS_BOX_PRECISION                 = 'precision';
    const OPTIONS_BOX_FORMULAS                  = 'formulas';
    const OPTIONS_BOX_FORMULAS_FORMULA          = 'formula';
    const OPTIONS_BOX_REMOVE_IF_EMPTY           = 'remove_if_empty';
    const OPTIONS_BOX_TEMPLATES                 = 'templates';
    const OPTIONS_BOX_TEMPLATES_TEMPLATE        = 'template';
    const OPTIONS_BOX_NOTE                      = 'note';
    const OPTIONS_BOX_IMPORT_SETTINGS           = 'import_settings';

    const OPTIONS_BOX_FILE_FIND_REPLACE         = 'file_find_replace';
    const OPTIONS_BOX_FILE_MOVE                 = 'move';
    const OPTIONS_BOX_FILE_COPY                 = 'copy';
    const OPTIONS_BOX_FILE_MOVE_COPY_PATH       = 'path'; // Inner key of the path input of "move" and "copy" inner keys

    const OPTIONS_BOX_FILE_NAME_TEMPLATES           = 'templates_file_name';
    const OPTIONS_BOX_FILE_MEDIA_TITLE_TEMPLATES    = 'templates_media_title';
    const OPTIONS_BOX_FILE_MEDIA_DESC_TEMPLATES     = 'templates_media_description';
    const OPTIONS_BOX_FILE_MEDIA_CAPTION_TEMPLATES  = 'templates_media_caption';
    const OPTIONS_BOX_FILE_MEDIA_ALT_TEXT_TEMPLATES = 'templates_media_alt_text';

    const POST_URL  = 'postUrl';
    const IMAGE_URL = 'imageUrl';
    const ITEM_ID   = 'item_id';
}
