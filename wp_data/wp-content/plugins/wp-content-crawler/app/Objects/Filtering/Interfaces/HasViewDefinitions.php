<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 12/04/2020
 * Time: 11:52
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Interfaces;


use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;

interface HasViewDefinitions {

    /**
     * @return ViewDefinitionList|null
     * @since 1.11.0
     */
    public function getViewDefinitions(): ?ViewDefinitionList;

}