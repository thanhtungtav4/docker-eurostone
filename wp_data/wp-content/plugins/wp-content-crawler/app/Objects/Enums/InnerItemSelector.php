<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 27/01/2020
 * Time: 15:12
 *
 * @since 1.10.0
 */

namespace WPCCrawler\Objects\Enums;


class InnerItemSelector {

    const BUTTON_DEV_TOOLS          = 'button.wcc-dev-tools';
    const BUTTON_TEST               = 'button.wcc-test';
    const BUTTON_ADD_CATEGORY_URLS  = 'button.wcc-test.wcc-category-map';
    const BUTTON_OPTIONS_BOX_IMPORT = 'button.options-box-import';
    const BUTTON_OPTIONS_BOX        = 'button.wcc-options-box';

    const TEST_RESULTS_CONTAINER    = '.test-results';

    const DEV_TOOLS_IFRAME              = 'iframe.source';
    const DEV_TOOLS_BTN_USE_SELECTOR    = '.css-selector-use';
    const DEV_TOOLS_SELECTION_BEHAVIOR  = '[name="selection_behavior"]';

    const TEST_TRANSLATION_OPTIONS_TEXTAREA = 'textarea';
}