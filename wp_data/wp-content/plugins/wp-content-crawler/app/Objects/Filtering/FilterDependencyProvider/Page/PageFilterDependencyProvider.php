<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/05/2020
 * Time: 12:40
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\FilterDependencyProvider\Page;


use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Filtering\FilterDependencyProvider\FilterDependencyProvider;
use WPCCrawler\Objects\Filtering\Interfaces\HasBot;

class PageFilterDependencyProvider extends FilterDependencyProvider implements HasBot {

    /** @var AbstractBot The post bot used to retrieve post data from the target post pages */
    private $bot;

    /**
     * @param AbstractBot $bot           See {@link bot}
     * @param array|null  $dataSourceMap See {@link FilterDependencyProvider::$dataSourceMap}
     * @since 1.11.0
     */
    public function __construct(AbstractBot $bot, ?array $dataSourceMap) {
        parent::__construct($dataSourceMap);
        $this->bot = $bot;
    }

    /**
     * @return AbstractBot See {@link bot}
     * @since 1.11.0
     */
    public function getBot(): AbstractBot {
        return $this->bot;
    }
}