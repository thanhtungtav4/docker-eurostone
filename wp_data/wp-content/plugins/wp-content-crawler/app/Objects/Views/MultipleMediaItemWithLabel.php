<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 05/04/2022
 * Time: 16:57
 *
 * @since 1.12.0
 */

namespace WPCCrawler\Objects\Views;

use WPCCrawler\Objects\Views\Base\AbstractViewWithLabel;

class MultipleMediaItemWithLabel extends AbstractViewWithLabel {

    public function getKey(): string {
        return 'form-items.combined.multiple-media-item-with-label';
    }

    protected function createViewVariableNames(): ?array {
        return null;
    }
}