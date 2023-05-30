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


use Illuminate\Support\Arr;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractConditionCommand;
use WPCCrawler\Objects\Filtering\Explaining\Base\AbstractCommandExplainer;

/**
 * @since 1.11.0
 */
class ConditionCommandExplainer extends AbstractCommandExplainer {

    /**
     * @param AbstractConditionCommand $cmd
     * @since 1.11.0
     */
    public function __construct(AbstractConditionCommand $cmd) {
        parent::__construct($cmd);
    }

    public function explain(): array {
        $cmd = $this->getCommand();
        $checkResult = $cmd instanceof AbstractConditionCommand
            ? $cmd->getConditionCheckResult()
            : false;

        $result = parent::explain();
        $result['details'] = array_merge(Arr::get($result, 'details', []), [
            'checkResult' => $checkResult,
        ]);

        return $result;
    }

    /**
     * @return AbstractBaseCommand|AbstractConditionCommand
     * @since 1.11.0
     */
    public function getCommand(): AbstractBaseCommand {
        return parent::getCommand();
    }


}