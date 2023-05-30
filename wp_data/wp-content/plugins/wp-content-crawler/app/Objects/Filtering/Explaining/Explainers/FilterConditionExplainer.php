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


use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractConditionCommand;
use WPCCrawler\Objects\Filtering\Explaining\Base\AbstractExplainer;
use WPCCrawler\Objects\Filtering\Filter\FilterCondition;

class FilterConditionExplainer extends AbstractExplainer {

    /** @var FilterCondition The filter condition that will be explained */
    private $filterCondition;

    /**
     * @param FilterCondition $filterCondition See {@link filterCondition}
     * @since 1.11.0
     */
    public function __construct(FilterCondition $filterCondition) {
        $this->filterCondition = $filterCondition;
    }

    public function explain(): array {
        $itemExplanations = [];

        foreach($this->filterCondition->getItems() as $item) {
            if (is_a($item, AbstractConditionCommand::class)) {
                $itemExplanations[] = (new ConditionCommandExplainer($item))->explain();

            } else if (is_a($item, FilterCondition::class)) {
                $itemExplanations[] = (new FilterConditionExplainer($item))->explain();
            }
        }

        $checkResult = $this->filterCondition->getConditionCheckResult();
        $logger = $this->filterCondition->getLogger();
        return [
            'type'      => 'condition',
            'condition' => [
                'operator' => $this->filterCondition->getOperator(),
                'details'  => array_merge(
                    $logger ? $logger->toArray() : [],
                    [
                        'executed'      => $checkResult !== null,
                        'checkResult'   => $checkResult,
                        'ownItemCount'  => sizeof($this->filterCondition->getItems()),
                        'totalCmdCount' => $this->getConditionCommandCount(),
                    ]
                ),
            ],
            'items' => $itemExplanations
        ];
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * @return int The number of commands in this filter condition. This counts the commands in the inner conditions as
     *             well.
     * @since 1.11.0
     */
    protected function getConditionCommandCount(): int {
        $count = 0;
        $this->filterCondition->forEachCommand(function() use (&$count) {
            $count++;
        });

        return $count;
    }

}