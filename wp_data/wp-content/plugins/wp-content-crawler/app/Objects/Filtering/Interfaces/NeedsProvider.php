<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 12/04/2020
 * Time: 14:44
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Interfaces;


use WPCCrawler\Objects\Filtering\FilterDependencyProvider\FilterDependencyProvider;

interface NeedsProvider {

    /**
     * @param FilterDependencyProvider|null $provider
     * @since 1.11.0
     */
    public function setProvider(?FilterDependencyProvider $provider): void;

    /**
     * @return FilterDependencyProvider|null
     * @since 1.11.0
     */
    public function getProvider(): ?FilterDependencyProvider;

}