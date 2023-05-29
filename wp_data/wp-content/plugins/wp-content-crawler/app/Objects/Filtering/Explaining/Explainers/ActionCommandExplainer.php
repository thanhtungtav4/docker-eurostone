<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 09/05/2020
 * Time: 19:52
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Explaining\Explainers;


use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractActionCommand;
use WPCCrawler\Objects\Filtering\Explaining\Base\AbstractCommandExplainer;

/**
 * @method AbstractActionCommand getCommand()
 * @since 1.11.0
 */
class ActionCommandExplainer extends AbstractCommandExplainer {

    /**
     * @param AbstractActionCommand $cmd
     * @since 1.11.0
     */
    public function __construct(AbstractActionCommand $cmd) {
        parent::__construct($cmd);
    }

}