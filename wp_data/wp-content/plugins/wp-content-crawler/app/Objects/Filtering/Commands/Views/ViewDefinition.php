<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 15/03/2020
 * Time: 08:48
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\Views;


use WPCCrawler\Interfaces\Arrayable;
use WPCCrawler\Objects\Views\Base\AbstractView;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\ViewService;

class ViewDefinition implements Arrayable {

    /** @var string One of the view classes extending to {@link AbstractView} */
    private $viewClass;

    /**
     * @var array Key-value pairs where keys are one of the constants defined in {@link ViewVariableName} and the
     *      values are their values. This stores the variables that will be injected to the view and their values.
     */
    private $variableValues = [];

    /**
     * @param string $viewClass See {@link $viewClass}
     * @since 1.11.0
     */
    public function __construct(string $viewClass) {
        $this->viewClass = $viewClass;
    }

    /**
     * @return string
     * @since 1.11.0
     */
    public function getViewClass(): string {
        return $this->viewClass;
    }

    /**
     * @param string $key   Key of the variable, one of the constants defined in {@link ViewVariableName} and exists in
     *                      the defined {@link $viewClass}
     * @param mixed  $value Value of the variable
     * @return $this
     * @since 1.11.0
     */
    public function setVariable(string $key, $value): self {
        $this->variableValues[$key] = $value;
        return $this;
    }

    /**
     * @return array See {@link $variableValues}
     * @since 1.11.0
     */
    public function getVariableValues(): array {
        return $this->variableValues;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array {
        $view = ViewService::getInstance()->getViewInstance($this->getViewClass());
        return [
            // If the view is not found, set the key as an empty string.
            'viewKey'   => $view ? $view->getKey() : '',
            'variables' => $this->getVariableValues(),
        ];
    }

}