<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 15/03/2020
 * Time: 08:08
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Views;


use WPCCrawler\Objects\Views\Base\AbstractViewWithLabel;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;

class InputWithLabel extends AbstractViewWithLabel {

    public function getKey(): string {
        return 'form-items.combined.input-with-label';
    }

    protected function createViewVariableNames(): ?array {
        return [ViewVariableName::TYPE];
    }
}