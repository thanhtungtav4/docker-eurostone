<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/03/2020
 * Time: 11:37
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Base;


use WPCCrawler\Interfaces\Arrayable;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Interfaces\HasViewDefinitions;
use WPCCrawler\Objects\Filtering\Property\Objects\CalculationResult;

abstract class AbstractProperty implements Arrayable, HasViewDefinitions {

    /** @var ViewDefinitionList|false|null Views that should be rendered for this property */
    private $views = false;

    /**
     * @return string One of the constants defined in {@link PropertyKey}
     * @since 1.11.0
     */
    abstract public function getKey(): string;

    /**
     * @return string Human-readable name of this property
     * @since 1.11.0
     */
    abstract public function getName(): string;

    /**
     * @return string|null Human-friendly description of this property. This will be shown in the UI for the users to
     *                     understand what this property is for.
     * @since 1.11.0
     */
    public function getDescription(): ?string {
        return null;
    }

    /**
     * @return bool If this property requires the extracted values one by one, returns true. If it requires the
     *              extracted values as an array, returns false.
     * @since 1.11.0
     */
    public function doesRequireRawExtractedValues(): bool {
        return false;
    }

    /**
     * Get the data types of the values that can be input to this property
     *
     * @return int[] Constants defined in {@link ValueType}
     * @since 1.11.0
     */
    abstract public function getInputDataTypes(): array;

    /**
     * Get the data types this property can return via its {@link calculate()} method
     *
     * @return int[] Explanation is the same as the explanation of {@link getInputDataTypes()}
     * @since 1.11.0
     */
    abstract public function getOutputDataTypes(): array;

    /**
     * @param mixed               $key    See {@link calculate()}
     * @param mixed               $source See {@link calculate()}
     * @param AbstractBaseCommand $cmd    See {@link calculate()}
     * @return null|CalculationResult|CalculationResult[] For example, if this property calculates the length, then the
     *                                                    length of $source should be returned inside a
     *                                                    {@link CalculationResult}
     * @since 1.11.0
     */
    abstract protected function onCalculate($key, $source, AbstractBaseCommand $cmd);

    /*
     *
     */

    /**
     * @param mixed               $key    The key for the source. This value can be used to identify the source when
     *                                    needed.
     * @param mixed               $source Source value. This must be used to calculate the property's value.
     * @param AbstractBaseCommand $cmd    The command for which this property should be calculated
     * @return CalculationResult[]|null Calculated values for the given source
     * @since 1.11.0
     */
    public function calculate($key, $source, AbstractBaseCommand $cmd): ?array {
        $result = $this->onCalculate($key, $source, $cmd);
        if ($result === null) return null;

        return is_array($result) ? $result : [$result];
    }

    /**
     * @return ViewDefinitionList|null See {@link $views}
     * @since 1.11.0
     */
    public function getViewDefinitions(): ?ViewDefinitionList {
        if ($this->views === false) {
            $this->views = $this->createViews();
        }

        return $this->views;
    }

    /*
     *
     */

    /**
     * @return ViewDefinitionList|null See {@link $views}
     * @since 1.11.0
     */
    protected function createViews(): ?ViewDefinitionList {
        return null;
    }

    /**
     * Creates a null calculation result.
     *
     * @param mixed $key
     * @return CalculationResult
     * @since 1.11.0
     */
    protected function createNullResult($key): CalculationResult {
        return new CalculationResult($key, null);
    }

    /*
     *
     */

    /**
     * @return array Array representation of this property
     * @since 1.11.0
     */
    public function toArray(): array {
        $viewList = $this->getViewDefinitions();
        return [
            'name'        => $this->getName(),
            'description' => $this->getDescription(),
            'key'         => $this->getKey(),
            'inputTypes'  => $this->getInputDataTypes(),
            'outputTypes' => $this->getOutputDataTypes(),
            'views'       => $viewList ? $viewList->toArray() : null,
        ];
    }

}