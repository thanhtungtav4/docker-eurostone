<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/05/2020
 * Time: 08:47
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Explaining\Explainers;


use WPCCrawler\Objects\Filtering\Explaining\Base\AbstractExplainer;
use WPCCrawler\Objects\Filtering\Filter\FilterList;

class FilterSettingExplainer extends AbstractExplainer {

    /**
     * @var string Name of the setting that will be used to group the filters so that the user can understand that the
     *      filter explanations belong to that setting.
     */
    private $name;

    /** @var FilterList */
    private $filterList;

    /**
     * @param string     $name       See {@link name}
     * @param FilterList $filterList See {@link filterList}
     * @since 1.11.0
     */
    public function __construct(string $name, FilterList $filterList) {
        $this->name       = $name;
        $this->filterList = $filterList;
    }

    /**
     * @return string
     * @since 1.11.0
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return FilterList
     * @since 1.11.0
     */
    public function getFilterList(): FilterList {
        return $this->filterList;
    }

    public function explain(): array {
        return [
            'type'    => 'filterSetting',
            'name'    => $this->getName(),
            'enabled' => $this->getFilterList()->isEnabled(),
            'filters' => $this->explainFilterList($this->getFilterList()),
        ];
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * Explain a list of filters
     *
     * @param FilterList $list
     * @return array Explanation of the filter list
     * @since 1.11.0
     */
    protected function explainFilterList(FilterList $list): array {
        $result = [];
        foreach($list->getItems() as $filter) {
            $result[] = (new FilterExplainer($filter))->explain();
        }

        return $result;
    }

}