<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/04/2020
 * Time: 09:27
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Views;


use WPCCrawler\Objects\Views\Base\AbstractViewWithLabel;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;

class MultipleSelectorWithLabel extends AbstractViewWithLabel {

    public function getKey(): string {
        return 'form-items.combined.multiple-selector';
    }

    protected function createViewVariableNames(): ?array {
        return [ViewVariableName::URL_SELECTOR];
    }

}