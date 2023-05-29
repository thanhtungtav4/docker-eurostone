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


use WPCCrawler\Objects\Crawling\Bot\PostBot;

/**
 * @method PostBot getBot()
 * @since   1.11.0
 */
class PostPageFilterDependencyProvider extends PageFilterDependencyProvider {

    /**
     * @param PostBot    $postBot       See {@link postBot}
     * @param array|null $dataSourceMap See {@link FilterDependencyProvider::$dataSourceMap}
     * @since 1.11.0
     */
    public function __construct(PostBot $postBot, ?array $dataSourceMap) {
        parent::__construct($postBot, $dataSourceMap);
    }

}