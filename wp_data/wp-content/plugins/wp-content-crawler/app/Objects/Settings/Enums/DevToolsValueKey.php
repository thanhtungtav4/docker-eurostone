<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 31/05/2019
 * Time: 18:47
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Settings\Enums;


class DevToolsValueKey {

    const TEST_BUTTON_BEHAVIOR_PHP  = 'php';
    const TEST_BUTTON_BEHAVIOR_JS   = 'js';
    const TEST_BUTTON_BEHAVIOR_BOTH = 'both';

    const SELECTION_BEHAVIOR_UNIQUE             = 'unique';
    const SELECTION_BEHAVIOR_SIMILAR            = 'similar';
    const SELECTION_BEHAVIOR_SIMILAR_SPECIFIC   = 'similar_specific';
    const SELECTION_BEHAVIOR_CONTAINS           = 'contains';

}