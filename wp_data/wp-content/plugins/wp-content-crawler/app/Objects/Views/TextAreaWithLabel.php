<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 17/11/2020
 * Time: 12:05
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Views;


use WPCCrawler\Objects\Views\Base\AbstractViewWithLabel;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;

class TextAreaWithLabel extends AbstractViewWithLabel {

    public function getKey(): string {
        return 'form-items.combined.textarea-with-label';
    }

    protected function createViewVariableNames(): ?array {
        return [ViewVariableName::ROWS];
    }
}