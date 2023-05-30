<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/04/2020
 * Time: 10:29
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Filter;


use Illuminate\Support\Arr;
use WPCCrawler\Exceptions\CommandNotExistException;
use WPCCrawler\Objects\Events\Enums\EventKey;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Commands\CommandService;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractConditionCommand;
use WPCCrawler\Objects\Filtering\Enums\FilterOptionKey;
use WPCCrawler\Objects\Filtering\FilterDependencyProvider\FilterDependencyProvider;
use WPCCrawler\Objects\Filtering\FilteringUtils;
use WPCCrawler\Objects\Filtering\Interfaces\Verbosable;
use WPCCrawler\Objects\Informing\Informer;

class Filter implements Verbosable {

    /**
     * @var string|null Human-readable title for this filter. This will be used when showing users something related to
     *      this filter
     */
    private $title;

    /**
     * @var FilterCondition|null Each filter can have only one condition. A condition have many commands and conditions in it.
     */
    private $condition;

    /** @var AbstractActionCommand[] Actions that will be performed when the condition holds */
    private $actions;

    /** @var string One of the constants defined in {@link EventKey}. Defines when the condition should be checked. */
    private $conditionEventKey;

    /** @var string One of the constants defined in {@link EventKey}. Defines when the actions will be run. */
    private $actionEventKey;

    /**
     * @var null|bool Whether this filter's condition was met or not. Stores the result of the last run
     *      {@link checkCondition()}. If the condition was met, this is true. If not, this is false. If the condition
     *      has not been checked yet, this is null.
     */
    private $conditionCheckResult = null;

    /**
     * @var array Key-value pairs where keys are field keys and values are sequential arrays storing the dot keys of the
     *            subjects matched by the conditions. This will be used to assign allowed subjects to the action
     *            commands that are constrained.
     *            E.g. ['post.preparedTags' => ['preparedTags.1', 'preparedTags.3'], ...]
     */
    private $allowedSubjectMap;

    /** @var bool True if this filter is enabled. Otherwise, false. */
    private $enabled = true;

    /**
     * @var bool True if extra logging should be done while executing the command. This is intended to be used when
     *      testing the settings such that extra information about the command is needed to debug it.
     */
    private $verbose = false;

    /**
     * @param FilterCondition|null $condition         See {@link condition}
     * @param array|null           $actions           See {@link actions}
     * @param string|null          $conditionEventKey See {@link conditionEventKey}
     * @param string|null          $actionEventKey    See {@link actionEventKey}
     * @param string|null          $title             See {@link title}
     * @since 1.11.0
     * @since 1.11.1 The constructor is made final
     */
    final public function __construct($condition, ?array $actions, ?string $actionEventKey = null,
                                ?string $conditionEventKey = null, ?string $title = null) {
        $this->condition            = $condition;
        $this->actions              = $actions ?: [];
        $this->actionEventKey       = $actionEventKey    ?: EventKey::POST_DATA_READY;
        $this->conditionEventKey    = $conditionEventKey ?: EventKey::POST_DATA_READY;
        $this->title                = $title;
    }

    /**
     * @return string|null See {@link title}
     * @since 1.11.0
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    /**
     * @return FilterCondition|null See {@link condition}
     * @since 1.11.0
     */
    public function getCondition(): ?FilterCondition {
        return $this->condition;
    }

    /**
     * @return AbstractActionCommand[] See {@link actions}
     * @since 1.11.0
     */
    public function getActions(): array {
        return $this->actions;
    }

    /**
     * @return bool True if this filter has at least one action.
     * @since 1.11.0
     */
    public function hasActions(): bool {
        return !empty($this->getActions());
    }

    /**
     * @return string See {@link conditionEventKey}
     * @since 1.11.0
     */
    public function getConditionEventKey(): string {
        return $this->conditionEventKey;
    }

    /**
     * @param string $conditionEventKey See {@link conditionEventKey}
     * @return $this
     * @since 1.11.0
     */
    public function setConditionEventKey(string $conditionEventKey): self {
        $this->conditionEventKey = $conditionEventKey;
        return $this;
    }

    /**
     * @return string See {@link actionEventKey}
     * @since 1.11.0
     */
    public function getActionEventKey(): string {
        return $this->actionEventKey;
    }

    /**
     * @param string $actionEventKey See {@link actionEventKey}
     * @return $this
     * @since 1.11.0
     */
    public function setActionEventKey(string $actionEventKey): self {
        $this->actionEventKey = $actionEventKey;
        return $this;
    }

    /**
     * @return bool|null See {@link conditionCheckResult}
     * @since 1.11.0
     */
    public function getConditionCheckResult(): ?bool {
        return $this->conditionCheckResult;
    }

    /**
     * @return array See {@link allowedSubjectMap}
     * @since 1.11.0
     */
    public function getAllowedSubjectMap(): array {
        return $this->allowedSubjectMap ?: [];
    }

    /**
     * @param FilterDependencyProvider $provider Provider that will be used to inject the dependencies to the commands
     * @return bool True if the condition evaluates to true. Otherwise, false.
     * @since 1.11.0
     */
    public function checkCondition(FilterDependencyProvider $provider): bool {
        // If this filter is disabled, mark it as not met and stop.
        if ($this->isDisabled()) {
            $this->conditionCheckResult = false;
            return $this->conditionCheckResult;
        }

        // If there is no condition, we consider this condition as met.
        $condition = $this->getCondition();
        if (!$condition) return $this->conditionCheckResult = true;

        // If there are no actions, this filter is not useful. Otherwise, check if the condition is met.
        $this->conditionCheckResult = $this->getActions()
            ? $condition->isMet($provider)
            : false;

        // If the condition is met, execute the condition commands whose subject keys are needed by action commands.
        $this->maybeExecuteNotExecutedConditionActions($provider);

        // Get the subject item map
        $this->allowedSubjectMap = $this->conditionCheckResult
            ? $condition->getSuitableSubjectItemMap()
            : [];

        return $this->conditionCheckResult ?: false;
    }

    /**
     * Execute the actions, if this filter is enabled.
     *
     * @param FilterDependencyProvider $provider Provider that will be used to inject the dependencies to the commands
     * @since 1.11.0
     */
    public function executeActions(FilterDependencyProvider $provider): void {
        // If there is no action or this filter is disabled, stop.
        if (!$this->hasActions() || $this->isDisabled()) return;

        $allowedSubjectMap = $this->getAllowedSubjectMap();
        $actions = $this->getActions();
        foreach($actions as $action) {
            $provider->injectDependencies($action);

            // If this action's subjects should be constrained, assign the allowed subjects to the action.
            if ($action->shouldLimitSubjects()) {
                $fieldKey = $action->getFieldKey();
                if ($fieldKey) $action->setAllowedSubjects($allowedSubjectMap[$fieldKey] ?? null);
            }

            // Execute the action and then invalidate the dependencies
            $action->execute();
            $provider->invalidateDependencies($action);

            // Retrieve the allowed subjects from the action command and update the allowed subject map used here. By
            // this way, the next command can have the allowed subjects added by the current command.
            if ($action->shouldLimitSubjects()) {
                $fieldKey = $action->getFieldKey();
                if ($fieldKey) $allowedSubjectMap[$fieldKey] = $action->getAllowedSubjects();
            }
        }
    }

    /**
     * @return bool See {@link enabled}
     * @since 1.11.0
     */
    public function isEnabled(): bool {
        return $this->enabled;
    }

    /**
     * @return bool Reverse of {@link enabled}
     * @since 1.11.0
     */
    public function isDisabled(): bool {
        return !$this->isEnabled();
    }

    /**
     * @param bool $enabled See {@link enabled}
     * @since 1.11.0
     */
    public function setEnabled(bool $enabled): void {
        $this->enabled = $enabled;
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * If the condition of this filter was met, executes the not-executed condition commands that have the same subject
     * keys as one of the action commands so that the action commands that need the subjects from the condition
     * commands can get them. This method is necessary because {@link FilterCondition::isMet()} stops if one of the
     * condition commands inside an "or" block evaluates to true. In that case, the other commands in the "or" block is
     * not executed. If there is an action command that need the matched subject keys from that not-executed condition
     * command, that action command is not able to do so. This method handles that situation.
     *
     * @param FilterDependencyProvider $provider Provider that will be used to inject dependencies of the commands
     * @uses  getConditionCheckResult()          to check if this filter's condition was met. It must return true for
     *                                           this method to be able to do its job.
     * @since 1.11.0
     */
    protected function maybeExecuteNotExecutedConditionActions(FilterDependencyProvider $provider): void {
        // If the condition was not met, no need to execute them. Because, the actions will not run.
        $condition = $this->getCondition();
        if (!$condition || !$this->getConditionCheckResult()) return;

        // The condition was met. Get the subjects of the actions that need matching subjects from the condition
        // commands.
        $subjectKeys = [];
        foreach($this->getActions() as $action) {
            if (!$action->shouldLimitSubjects()) continue;

            $fieldKey = $action->getFieldKey();
            if (!$fieldKey) continue;

            $subjectKeys[] = $fieldKey;
        }

        // Find the condition commands that have one of the subject keys and that were not executed. Then, execute them.
        $condition->forEachCommand(function(AbstractConditionCommand $cmd) use (&$subjectKeys, $provider) {
            // If the subject keys are not null, then this command was executed before. We do not want to run the
            // executed commands again. Also, the command's field key should be a field key that is used by one of the
            // actions that require subject keys from the condition commands.
            if ($cmd->getSuitableSubjectItems() !== null || !in_array($cmd->getFieldKey(), $subjectKeys)) return;

            // Execute the condition command. We are not interested in the result. We only want to execute it so that
            // its subject keys can be retrieved later.
            FilteringUtils::doesConditionCommandApply($cmd, $provider);

            $logger = $cmd->getLogger();
            if ($logger) $logger
                ->addMessage(_wpcc("This command is run after the filter condition's execution is finished. This 
                    command is run because its subject values are needed by an action command."));
        });
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
     * @return Filter
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
     * @param array|null $options Options that will be used to create a filter
     * @param bool|null  $verbose See {@link FilteringUtils::getVerbose()}
     * @return Filter|null
     * @since 1.11.0
     */
    public static function fromOptions(?array $options, ?bool $verbose = null): ?Filter {
        if ($options === null) return null;

        $title      = Arr::get($options, FilterOptionKey::TITLE);
        $filterIf   = Arr::get($options, FilterOptionKey::FILTER_IF);
        $filterThen = Arr::get($options, FilterOptionKey::FILTER_THEN);
        $config     = Arr::get($options, FilterOptionKey::CONFIG);

        if (!is_string($title))     $title      = null;
        if (!is_array($filterIf))   $filterIf   = [];
        if (!is_array($filterThen)) $filterThen = [];
        if (!is_array($config))     $config     = [];

        $enabled = Arr::get($config, FilterOptionKey::CONFIG_ENABLED, '1') === '1';

        $thenActions = Arr::get($filterThen, FilterOptionKey::ITEMS, []);
        if (!is_array($thenActions)) $thenActions = [];

        $eventDotKey = FilterOptionKey::OPTIONS . '.' . FilterOptionKey::EVENT;
        $conditionEventKey = Arr::get($filterIf,   $eventDotKey);
        $actionEventKey    = Arr::get($filterThen, $eventDotKey);
        if (!is_string($conditionEventKey)) $conditionEventKey = null;
        if (!is_string($actionEventKey))    $actionEventKey    = null;

        $verbose = FilteringUtils::getVerbose($verbose);

        $commandService = CommandService::getInstance();
        $actions = [];
        foreach($thenActions as $commandOption) {
            $command = static::createCommandFromOptions($commandService, $commandOption,
                AbstractActionCommand::class, $verbose);
            if ($command === null) continue;

            $actions[] = $command;
        }

        // If there are no actions, return null, since a filter without actions is not usable.
        if (!$actions) return null;

        $filter = new static(FilterCondition::fromOptions($filterIf, $verbose), $actions, $actionEventKey, $conditionEventKey, $title);
        $filter->setVerbose($verbose);
        $filter->setEnabled($enabled);

        return $filter;
    }

    /**
     * Create a command from options
     *
     * @param CommandService $commandService Service that will be used to get command instances
     * @param array|null     $options        Options that will be used to create a command by using
     *                                       {@link AbstractBaseCommand::fromOptions()} method
     * @param string|null    $type           Class name of the expected command type. If this is null, all commands are
     *                                       allowed. For example, you can set this to {@link AbstractConditionCommand}
     *                                       to get only condition commands.
     * @param bool|null      $verbose        See {@link FilteringUtils::getVerbose()}
     * @return AbstractBaseCommand|null If the options are valid, a new command. Otherwise, null.
     * @since 1.11.0
     */
    public static function createCommandFromOptions(CommandService $commandService, ?array $options,
                                                    ?string $type = null, ?bool $verbose = null): ?AbstractBaseCommand {
        if ($options === null) return null;

        $commandKey = Arr::pull($options, FilterOptionKey::COMMAND);
        if (!is_string($commandKey)) return null;

        try {
            $cmdInstance = $commandService->getCommand($commandKey);

            // If there is a type, make sure the command is of the given type. If not, return null.
            if ($type !== null && !is_a($cmdInstance, $type)) return null;

        } catch (CommandNotExistException $e) {
            Informer::addInfo(sprintf(_wpcc('Command with key "%1$s" does not exist.'), $commandKey))
                ->setException($e)
                ->addAsLog();
            return null;
        }

        return $cmdInstance::fromOptions($options, FilteringUtils::getVerbose($verbose));
    }
}