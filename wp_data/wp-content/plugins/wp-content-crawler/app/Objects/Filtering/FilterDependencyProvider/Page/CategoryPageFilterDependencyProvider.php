<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/04/2020
 * Time: 21:29
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\FilterDependencyProvider\Page;


use WPCCrawler\Objects\Crawling\Bot\CategoryBot;

/**
 * @method CategoryBot getBot()
 * @since   1.11.0
 */
class CategoryPageFilterDependencyProvider extends PageFilterDependencyProvider {

    /**
     * @param CategoryBot $categoryBot   See {@link categoryBot}
     * @param array|null  $dataSourceMap See {@link FilterDependencyProvider::$dataSourceMap}
     * @since 1.11.0
     */
    public function __construct(CategoryBot $categoryBot, ?array $dataSourceMap) {
        parent::__construct($categoryBot, $dataSourceMap);
    }

}