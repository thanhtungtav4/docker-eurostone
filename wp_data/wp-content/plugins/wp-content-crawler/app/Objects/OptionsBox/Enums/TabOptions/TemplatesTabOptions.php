<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 22/12/2018
 * Time: 11:02
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\OptionsBox\Enums\TabOptions;


use WPCCrawler\Objects\Enums\EnumBase;

class TemplatesTabOptions extends EnumBase {

    // These will be used by OptionsBox module in the front end. So, the values must match the implementations there.
    const ALLOWED_SHORT_CODES = 'allowedShortCodes';
}