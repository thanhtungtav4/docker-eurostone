<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 15/03/2020
 * Time: 08:06
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Views\Base;


use \Exception;
use WPCCrawler\Interfaces\Arrayable;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Utils;

abstract class AbstractView implements Arrayable {

    /**
     * @var string[]|null Names of the variables existing in the view. If there is no variable in the view, this is
     *                    null. Variable names can be retrieved from the constants defined in {@link ViewVariableName}.
     */
    private $variableNames = null;

    /**
     * @return string Dot notation key for the Blade view existing under the root directory of the Blade views
     * @since 1.11.0
     */
    abstract public function getKey(): string;

    /**
     * @return string[]|null See {@link $variableNames}
     * @since 1.11.0
     */
    abstract protected function createVariableNames(): ?array;

    /**
     * @return array|null An associative array where keys are variable names defined in the Blade view and the values
     *                    are their values. If no injection should be done, returns null.
     * @since 1.11.0
     */
    public function getInjectVariables(): ?array {
        return null;
    }

    /*
     *
     */

    /**
     * @return string[] See {@link $variableNames}
     * @since 1.11.0
     */
    public function getVariableNames(): array {
        if ($this->variableNames === null) {
            $this->variableNames = array_merge(
                [
                    ViewVariableName::ID,
                    ViewVariableName::CLAZZ,
                ],
                $this->createVariableNames() ?: []
            );
        }

        return $this->variableNames;
    }

    /**
     * @return string|null The view rendered as a JavaScript template by replacing the variable names with the variable
     *                     name format used in JS templates
     * @since 1.11.0
     */
    public function renderAsJsTemplate(): ?string {
        try {
            $v = Utils::view($this->getKey());

        } catch (Exception $e) {
            Informer::addError("View '{$this->getKey()}' could not be created.")
                ->setException($e)->addAsLog();
            return null;
        }

        $with = $this->getInjectVariables() ?: [];
        foreach($this->getVariableNames() as $name) {
            $with[$name] = sprintf('{{{%s}}}', $name);
        }

        return $v->with($with)->render();
    }

    public function toArray(): array {
        return [
            'key'      => $this->getKey(),
            'template' => $this->renderAsJsTemplate(),
        ];
    }

}