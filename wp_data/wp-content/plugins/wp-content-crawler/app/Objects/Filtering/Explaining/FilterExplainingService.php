<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/05/2020
 * Time: 08:41
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Explaining;


use WPCCrawler\Objects\Filtering\Explaining\Explainers\FilterSettingExplainer;

class FilterExplainingService {

    /** @var FilterExplainingService */
    private static $instance = null;

    /**
     * @var FilterSettingExplainer[] Each inner array contains the name of the filter setting and its filter list. The
     *      filters in the list will be explained
     */
    private $filterSettingExplainers = [];

    /**
     * @return FilterExplainingService
     * @since 1.11.0
     */
    public static function getInstance(): FilterExplainingService {
        if (self::$instance === null) {
            self::$instance = new FilterExplainingService();
        }

        return self::$instance;
    }

    /**
     * This is a singleton
     *
     * @since 1.11.0
     */
    protected function __construct() { }

    /**
     * @param FilterSettingExplainer $item
     * @return FilterExplainingService
     * @since 1.11.0
     */
    public function addFilterSettingExplainer(FilterSettingExplainer $item): self {
        $this->filterSettingExplainers[] = $item;
        return $this;
    }

    /**
     * @return array Explanation of all registered filter settings
     * @since 1.11.0
     */
    public function explainAll(): array {
        $result = [];
        foreach($this->filterSettingExplainers as $explainer) {
            $result[] = $explainer->explain();
        }

        return $result;
    }
}