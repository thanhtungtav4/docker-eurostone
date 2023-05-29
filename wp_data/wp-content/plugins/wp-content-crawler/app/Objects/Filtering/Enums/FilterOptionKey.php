<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/04/2020
 * Time: 11:32
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Enums;


abstract class FilterOptionKey {

    const TYPE              = 'type';
    const TYPE_COMMAND      = 'command';
    const TYPE_CONDITION    = 'condition';
    const ITEMS             = 'items';
    const OPTIONS           = 'options';
    const CONFIG            = 'config';
    const OPERATOR          = 'operator';
    const COMMAND           = 'command';
    const EVENT             = 'event';
    const SUBJECT           = 'subject';
    const PROPERTY          = 'property';
    const TITLE             = 'title';
    const FILTER_IF         = 'filterIf';
    const FILTER_THEN       = 'filterThen';

    const FILTER_COLLAPSED  = 'filterCollapsed';
    const SUMMARY_EXPANDED  = 'summaryExpanded';
    const SIDE_BY_SIDE      = 'sideBySide';

    const CMD_OPTION_STOP_AFTER_FIRST_MATCH = 'stopAfterFirstMatch';
    const CMD_OPTION_ONLY_MATCHED_ITEMS     = 'onlyMatchedItems';

    // Keys of CONFIG object
    const CONFIG_ENABLED = 'enabled';

}