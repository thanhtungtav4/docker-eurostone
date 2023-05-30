<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 01/04/2020
 * Time: 13:00
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Views\Base;


abstract class AbstractSelectWithLabel extends AbstractViewWithLabel {

    protected function createViewVariableNames(): ?array {
        return null;
    }

}