<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 08/07/2020
 * Time: 11:31
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Views;


use WPCCrawler\Objects\Views\Base\AbstractViewWithLabel;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;

class MultipleInputWithLabel extends AbstractViewWithLabel {

    public function getKey(): string {
        return 'form-items.combined.multiple-text-with-label';
    }

    protected function createViewVariableNames(): ?array {
        return [ViewVariableName::PLACEHOLDER];
    }
}