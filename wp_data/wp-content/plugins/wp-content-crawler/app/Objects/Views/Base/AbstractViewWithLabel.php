<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 15/03/2020
 * Time: 08:09
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Views\Base;


use WPCCrawler\Objects\Views\Enums\ViewVariableName;

abstract class AbstractViewWithLabel extends AbstractView {

    /**
     * @return string[]|null See {@link createVariableNames()}. This must return the variable names other than the ones
     *                       existing for the label view
     * @since 1.11.0
     */
    protected abstract function createViewVariableNames(): ?array;

    protected function createVariableNames(): ?array {
        $namesForLabel = [
            ViewVariableName::NAME,
            ViewVariableName::TITLE,
            ViewVariableName::INFO,
        ];

        $otherVariables = $this->createViewVariableNames();
        if ($otherVariables !== null) {
            $namesForLabel = array_merge($namesForLabel, $otherVariables);
        }

        return $namesForLabel;
    }

}