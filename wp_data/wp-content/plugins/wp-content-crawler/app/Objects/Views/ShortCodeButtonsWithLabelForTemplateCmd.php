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

/**
 * Creates a view that contains a label and a list of short codes that can be used when defining templates for
 * "template" command.
 *
 * @since 1.11.0
 */
class ShortCodeButtonsWithLabelForTemplateCmd extends AbstractViewWithLabel {

    public function getKey(): string {
        return 'form-items.combined.short-code-buttons-with-label-for-template-cmd';
    }

    protected function createViewVariableNames(): ?array {
        return null;
    }
}