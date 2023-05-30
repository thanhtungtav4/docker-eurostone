<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/03/2020
 * Time: 20:11
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Views;


use Exception;
use WPCCrawler\Objects\Views\Base\AbstractView;
use WPCCrawler\Objects\Views\Select\SelectAuthorWithLabel;
use WPCCrawler\Objects\Views\Select\SelectDecimalSeparatorWithLabel;
use WPCCrawler\Objects\Views\Select\SelectPostStatusWithLabel;

class ViewService {

    /** @var ViewService */
    private static $instance = null;

    /**
     * @var array Key-value pairs where keys are the view keys (See {@link AbstractView::getKey()}) and the values are
     *      {@link AbstractView} instances
     */
    private $registry = null;

    /**
     * @return ViewService
     * @since 1.11.0
     */
    public static function getInstance(): ViewService {
        if (static::$instance === null) {
            static::$instance = new ViewService();
        }

        return static::$instance;
    }

    /**
     * This is a singleton
     * @since 1.11.0
     */
    protected function __construct() { }

    /**
     * @return string[] Class names of the views
     * @since 1.11.0
     */
    protected function getViewClasses(): array {
        // Register all view classes here
        return [
            InputWithLabel::class,
            MultipleInputWithLabel::class,
            MultipleMediaItemWithLabel::class,
            NumericInputWithLabel::class,
            CheckboxWithLabel::class,
            MaxLengthInputWithLabel::class,
            MultipleExchangeAttrsWithLabel::class,
            MultipleFindReplaceWithLabelForCmd::class,
            SelectAuthorWithLabel::class,
            SelectPostStatusWithLabel::class,
            SelectDecimalSeparatorWithLabel::class,
            MultipleSelectorWithLabel::class,
            ShortCodeButtonsWithLabelForEmailCmd::class,
            ShortCodeButtonsWithLabelForTemplateCmd::class,
            TestResultContainer::class,
            TextAreaWithLabel::class,
        ];
    }

    /**
     * Get the instance of a view class existing in the registry
     *
     * @param string $viewClass Name of a view class that exists in the registry
     * @return AbstractView|null If an instance exists for the given view class, it will be returned. Otherwise, null.
     * @since 1.11.0
     */
    public function getViewInstance(string $viewClass): ?AbstractView {
        $instances = array_values($this->getRegistry());
        foreach($instances as $instance) {
            if (is_a($instance, $viewClass)) {
                return $instance;
            }
        }

        return null;
    }

    /**
     * @return AbstractView[] See {@link $registry}
     * @since 1.11.0
     */
    public function getRegistry(): array {
        if ($this->registry === null) {
            $this->registry = [];

            foreach($this->getViewClasses() as $cls) {
                try {
                    $instance = new $cls();

                } catch (Exception $e) {
                    continue;
                }

                if (!is_a($instance, AbstractView::class)) continue;

                /** @var AbstractView $instance */
                $this->registry[$instance->getKey()] = $instance;
            }

        }

        return $this->registry;
    }

}