<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 15/04/2022
 * Time: 21:19
 *
 * @since 1.12.0
 */

namespace WPCCrawler\Objects\Views;

use WPCCrawler\Objects\Views\Base\AbstractViewWithLabel;

class MultipleExchangeAttrsWithLabel extends AbstractViewWithLabel {

    public function getKey(): string {
        return 'form-items.combined.multiple-exchange-attrs-with-label';
    }

    protected function createViewVariableNames(): ?array {
        return null;
    }
}