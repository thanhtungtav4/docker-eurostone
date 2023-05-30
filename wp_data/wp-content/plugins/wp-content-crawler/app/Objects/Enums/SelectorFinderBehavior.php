<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 03/02/2019
 * Time: 22:10
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Enums;


class SelectorFinderBehavior extends EnumBase {

    const UNIQUE            = 'unique';
    const SIMILAR           = 'similar';
    const SIMILAR_SPECIFIC  = 'similar_specific';
    const CONTAINS          = 'contains';

}