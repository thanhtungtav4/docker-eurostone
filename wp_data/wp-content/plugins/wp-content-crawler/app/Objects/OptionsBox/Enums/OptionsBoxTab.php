<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/12/2018
 * Time: 10:20
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\OptionsBox\Enums;


use WPCCrawler\Objects\Enums\EnumBase;

class OptionsBoxTab extends EnumBase {

    // These must be the same as the state key defined in each child of TabBase.ts
    const CALCULATIONS     = 'calculations';
    const FIND_REPLACE     = 'findReplace';
    const GENERAL          = 'general';
    const IMPORT_EXPORT    = 'importExport';
    const NOTES            = 'notes';
    const TEMPLATES        = 'templates';
    const FILE_TEMPLATES   = 'fileTemplates';

}