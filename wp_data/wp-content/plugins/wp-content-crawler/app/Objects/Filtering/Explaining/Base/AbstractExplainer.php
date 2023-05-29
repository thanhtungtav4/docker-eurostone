<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 09/05/2020
 * Time: 20:19
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Explaining\Base;


abstract class AbstractExplainer {

    /**
     * @return array Key-value pairs that will be used to display the explanation of this item in the front-end
     * @since 1.11.0
     */
    public abstract function explain(): array;

}