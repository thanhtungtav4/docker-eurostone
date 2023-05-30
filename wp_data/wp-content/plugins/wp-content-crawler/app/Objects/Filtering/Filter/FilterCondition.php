<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/04/2020
 * Time: 10:37
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Filter;


use Illuminate\Support\Arr;
use WPCCrawler\Objects\Filtering\Commands\CommandService;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractConditionCommand;
use WPCCrawler\Objects\Filtering\Enums\ConditionOperator;
use WPCCrawler\Objects\Filtering\Enums\FilterOptionKey;
use WPCCrawler\Objects\Filtering\Explaining\Loggers\Logger;
use WPCCrawler\Objects\Filtering\FilterDependencyProvider\FilterDependencyProvider;
use WPCCrawler\Objects\Filtering\FilteringUtils;
use WPCCrawler\Objects\Filtering\Interfaces\Verbosable;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Utils;

class FilterCondition implements Verbosable {

    /**
     * @var string One of the constants defined in {@link ConditionOperator}. Defaults to {@link ConditionOperator::AND}
     */
    private $operator;

    /**
     * @var FilterCondition[]|AbstractConditionCommand[] A sequential array of {@link FilterCondition}s and
     *      {@link AbstractConditionCommand}s. The array contains the both types.
     */
    private $items;

    /**
     * @var null|bool Stores the result of {@link isMet()}. If the condition was not checked before, this is null. If
     *      the condition was checked and met, this is true. Otherwise, this is false.
     */
    private $conditionCheckResult = null;

    /**
     * @var bool True if extra logging should be done while executing the command. This is intended to be used when
     *      testing the settings such that extra information about the command is needed to debug it.
     */
    private $verbose = false;

    /** @var null|Logger */
    private $logger = null;

    /**
     * @param string|null $operator See {@link operator}
     * @param array|null  $items    See {@link items}
     * @since 1.11.0
     */
    public function __construct(?string $operator, ?array $items = null) {
        $this->operator = $operator === null ? ConditionOperator::AND : $operator;
        $this->items    = $items ?: [];
    }

    /**
     * @return string See {@link operator}
     * @since 1.11.0
     */
    public function getOperator(): string {
        return $this->operator;
    }

    /**
     * @return bool True if the operator is {@link ConditionOperator::AND}
     * @since 1.11.0
     */
    public function isAnd(): bool {
        return $this->getOperator() === ConditionOperator::AND;
    }

    /**
     * @return bool True if the operator is {@link ConditionOperator::OR}
     * @since 1.11.0
     */
    public function isOr(): bool {
        return $this->getOperator() === ConditionOperator::OR;
    }

    /**
     * @return FilterCondition[]|AbstractConditionCommand[] See {@link items}
     * @since 1.11.0
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * @return Logger|null
     * @since 1.11.0
     */
    public function getLogger(): ?Logger {
        return $this->logger;
    }

    /**
     * Check if the condition is met
     *
     * @param FilterDependencyProvider $provider See {@link onIsMet()}
     * @return bool See {@link onIsMet()}
     * @since 1.11.0
     */
    public function isMet(FilterDependencyProvider $provider): bool {
        if ($this->isVerbose()) $this->logger = (new Logger())->tick();

        $this->conditionCheckResult = $this->onIsMet($provider);

        $logger = $this->getLogger();
        if ($logger) $logger->tock();

        return $this->conditionCheckResult;
    }

    /**
     * @return array Key-value pairs where keys are field keys and values are sequential arrays storing the dot keys of
     *               the subjects (or values of the subjects, e.g. objects) matched by the conditions. This can be used
     *               to assign allowed subjects to the action commands that are constrained.
     *
     *               E.g. ['post.preparedTags' => ['preparedTags.1', 'preparedTags.3'], 'special.postBot' => [Object, Object], ...]
     * @since 1.11.0
     */
    public function getSuitableSubjectItemMap(): array {
        $and = $this->isAnd();

        $fieldKeyItemMap = [];
        foreach($this->getItems() as $item) {
            if (is_a($item, AbstractConditionCommand::class)) {
                /** @var AbstractConditionCommand $item */
                $fieldKey = $item->getFieldKey();
                if (!$fieldKey) continue;

                // Get the subject items
                $currentSubjectItems = $item->getSuitableSubjectItems();
                if ($currentSubjectItems === null) continue;

                $this->addSubjectItems($and, $fieldKeyItemMap, $currentSubjectItems, $fieldKey);

            } else if (is_a($item, FilterCondition::class)) {
                /** @var FilterCondition $item */
                $this->addConditionSubjectItems($and, $fieldKeyItemMap, $item);
            }
        }

        // Make sure the subject items of each field are unique arrays and sequential
        foreach($fieldKeyItemMap as $k => &$v) {
            // When the values are created, their keys are preserved by array_merge or array_intersect functions. Make
            // sure the array is sequential.
            if($v) $v = array_values($v);

            // Make sure the value is an array
            if(!is_array($v)) continue;

            // If the first item is an object, we assume the array is an object array. We do not use array_unique for
            // object arrays because it cannot compare the identities of the objects.
            $v = $v && is_object($v[0]) ? Utils::arrayUniqueObject($v) : array_unique($v);

            // array_unique also preserves the keys. Make array sequential.
            $v = array_values($v);
        }

        return $fieldKeyItemMap;
    }

    /**
     * Iterate over all commands in this condition. The callback will be called for the inner condition's commands as
     * well.
     *
     * @param callable|null $callback A function that takes only one parameter which is a
     *                                {@link AbstractConditionCommand::class} and that returns void.
     *
     *                                E.g. function(AbstractConditionCommand $cmd) { ... }
     * @since 1.11.0
     */
    public function forEachCommand(?callable $callback): void {
        if (!$callback) return;

        foreach($this->getItems() as $item) {
            if (is_a($item, AbstractConditionCommand::class)) {
                $callback($item);

            } else if (is_a($item, FilterCondition::class)) {
                $item->forEachCommand($callback);
            }
        }
    }

    /**
     * @return bool|null See {@link conditionCheckResult}
     * @since 1.11.0
     */
    public function getConditionCheckResult(): ?bool {
        return $this->conditionCheckResult;
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * Check if the condition is met
     *
     * @param FilterDependencyProvider $provider Provider that will be used to inject dependencies of the commands. See
     *                                           {@link doesConditionCommandApply()}.
     * @return bool True if the condition is met. If the items do not exist in the condition, returns true. If the
     *              condition has an unsupported operator, returns false.
     * @since 1.11.0
     */
    protected function onIsMet(FilterDependencyProvider $provider): bool {
        // If there are no commands, it means the condition is met always.
        if (!$this->getItems()) return true;

        $or  = $this->isOr();
        $and = $this->isAnd();

        // If this is neither "or" nor "and", return false. We do not support any other operators.
        if (!$or && !$and) {
            // Notify the user about the problem.
            Informer::addInfo(sprintf(_wpcc('Condition operator "%s" is not supported.'), $this->getOperator()));
            return false;
        }

        $currentResult = false;
        foreach($this->getItems() as $item) {
            if (is_a($item, AbstractConditionCommand::class)) {
                // Inject a suitable data source to the command, if there is a suitable data source.
                $currentResult = FilteringUtils::doesConditionCommandApply($item, $provider);

            } else if (is_a($item, FilterCondition::class)) {
                $currentResult = $item->isMet($provider);
            }

            // If the operator is "or" and at least one command applies, then this condition is met. No need to check
            // others.
            if ($or && $currentResult) return true;

            // If the operator is "and" and the current result is false, then this condition is not met. No need to
            // check others.
            if ($and && !$currentResult) return false;
        }

        // If the operator is "and" and we reached here, then the condition is met.
        // If the operator is "or"  and we reached here, then the condition is not met.
        // We do not support any other operators. So, this basically sums up to returning true only if the operator is
        // "and".
        return $and;
    }

    /**
     * Add item map of a condition to another item map according to the type of condition operator
     *
     * @param bool            $and             See {@link addSubjectItems()}
     * @param array           $fieldKeyItemMap See {@link addSubjectItems()}
     * @param FilterCondition $condition       The condition whose subject keys map will be merged to $fieldKeyItemMap
     * @since 1.11.0
     */
    protected function addConditionSubjectItems(bool $and, array &$fieldKeyItemMap, FilterCondition $condition): void {
        foreach($condition->getSuitableSubjectItemMap() as $fieldKey => $subjectItems) {
            $this->addSubjectItems($and, $fieldKeyItemMap, $subjectItems, $fieldKey);
        }
    }

    /**
     * Add subject items of a command to the given subject key map by considering the type of condition operator
     *
     * @param bool              $and             True if the operator to be used to add the subject items is "and".
     *                                           Otherwise, false.
     * @param array             $fieldKeyItemMap Key-value pairs where keys are field keys and the values are string
     *                                           arrays containing dot keys or object arrays
     * @param string[]|object[] $subjectItems    The items that will be added to $fieldKeyItemMap
     * @param string            $fieldKey        Field key under which the subject items will be added to
     *                                           $fieldKeyItemMap
     * @since 1.11.0
     */
    protected function addSubjectItems(bool $and, array &$fieldKeyItemMap, array $subjectItems, string $fieldKey): void {
        // If the map does not contain the field key, initialize it with the given items.
        if (!isset($fieldKeyItemMap[$fieldKey])) {
            $fieldKeyItemMap[$fieldKey] = $subjectItems;
            return;
        }

        // If the operator is "and":
        //  . Get the common subject items for all commands.
        // Otherwise:
        //  . Merge the subject items from all commands
        if ($and) {
            // array_intersect cannot handle the objects that do not have string representations. If this is an object
            // array, then use our own implementation. Otherwise, use PHP's. When one of the arrays is empty, we use
            // our own implementation just to be on the safe side. array_intersect throws an exception when no string
            // representation exists. Our implementation will stop immediately when one of the arrays is empty. So, no
            // performance concerns.
            $isObject = empty($fieldKeyItemMap[$fieldKey]) || empty($subjectItems) || is_object($subjectItems[0]);
            $fieldKeyItemMap[$fieldKey] = $isObject
                ? Utils::arrayIntersect($fieldKeyItemMap[$fieldKey], $subjectItems)
                : array_intersect($fieldKeyItemMap[$fieldKey], $subjectItems);

        } else {
            $fieldKeyItemMap[$fieldKey] = array_merge($fieldKeyItemMap[$fieldKey], $subjectItems);
        }
    }

    /*
     * INTERFACE METHODS
     */

    /**
     * @return bool See {@link verbose}
     * @since 1.11.0
     */
    public function isVerbose(): bool {
        return $this->verbose;
    }

    /**
     * @param bool $verbose See {@link verbose}
     * @return FilterCondition
     * @since 1.11.0
     */
    public function setVerbose(bool $verbose): self {
        $this->verbose = $verbose;
        return $this;
    }

    /*
     * STATIC METHODS
     */

    /**
     * @param array|null $options Options that will be used to create a condition
     * @param bool|null  $verbose See {@link FilteringUtils::getVerbose()}
     * @return FilterCondition|null
     * @since 1.11.0
     */
    public static function fromOptions(?array $options, ?bool $verbose = null): ?FilterCondition {
        if (!$options) return null;

        $operatorKey     = sprintf('%1$s.%2$s', FilterOptionKey::OPTIONS, FilterOptionKey::OPERATOR);
        $operator        = Arr::get($options, $operatorKey);
        $itemDefinitions = Arr::get($options, FilterOptionKey::ITEMS);

        if (!is_array($itemDefinitions)) return null;

        $commandService = CommandService::getInstance();
        $verbose = FilteringUtils::getVerbose($verbose);

        // The factory methods for item types
        $factoryMap = [
            FilterOptionKey::TYPE_COMMAND   => function($itemDef) use ($commandService, $verbose) { return Filter::createCommandFromOptions($commandService, $itemDef, AbstractConditionCommand::class, $verbose); },
            FilterOptionKey::TYPE_CONDITION => function($itemDef) use ($verbose) { return FilterCondition::fromOptions($itemDef, $verbose); }
        ];

        $items = [];
        foreach($itemDefinitions as $itemDef) {
            $type = Arr::pull($itemDef, FilterOptionKey::TYPE);
            $item = isset($factoryMap[$type]) ? $factoryMap[$type]($itemDef) : null;

            // If the item exists, add it to the list.
            if ($item === null) continue;
            $items[] = $item;
        }

        // If there are items, return a new condition. Otherwise, return null since a condition without any items is not
        // usable.
        if (!$items) return null;

        $operator = is_string($operator)
            ? $operator
            : null;
        $filterCondition = static::newInstance($operator, $items);
        $filterCondition->setVerbose($verbose);

        return $filterCondition;
    }

    /**
     * Create a new instance of this class
     *
     * @param string|null $operator See {@link operator}
     * @param array|null  $items    See {@link items}
     * @since 1.11.1
     */
    protected static function newInstance(?string $operator, ?array $items = null): FilterCondition {
        return new FilterCondition($operator, $items);
    }
}