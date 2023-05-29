<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 14/03/2020
 * Time: 14:25
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands;


use Exception;
use WPCCrawler\Exceptions\CommandNotExistException;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractActionCommand;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Crawling\Stop;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Element\ElementClone;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Element\ElementExchangeAttributes;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Element\ElementRemoveAttributes;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Element\ElementRemoveLinks;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Element\RemoveElement;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Notification\SendEmailNotification;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Numeric\Calculate;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Crawling\StopAndDeletePost;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\PostPage\AddCategories;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\PostPage\AddTags;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\PostPage\SetAuthor;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\PostPage\SetFeaturedImage;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\PostPage\SetPostStatus;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextClear;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextFindReplace;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextLimitChars;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextLimitWords;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextMakeCamelCase;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextMakeKebabCase;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextMakeLowerCase;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextMakeSlug;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextMakeSnakeCase;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextMakeStudlyCase;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextMakeTitleCase;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextMakeUcFirst;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextMakeUpperCase;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextRemoveEmptyHtmlElements;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextRemoveLinks;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\TextTemplate;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Crawling\CrawlingCommand;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Crawling\FirstPageCommand;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Crawling\RecrawlingCommand;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Date\DateIsNewerThanFixed;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Date\DateIsNewerThanRelative;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Date\DateIsOlderThanFixed;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Date\DateIsOlderThanRelative;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Element\ElementDoesNotExist;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Element\ElementExists;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Error\AnyError;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Error\HtmlError;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Error\RequestError;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\NumberComparison\EqualToCommand;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\NumberComparison\GreaterThanCommand;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\NumberComparison\GreaterThanOrEqualCommand;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\NumberComparison\LessThanCommand;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\NumberComparison\LessThanOrEqualCommand;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\NumberComparison\NotEqualToCommand;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text\TextContains;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text\TextDoesNotContain;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text\TextDoesNotEndWith;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text\TextDoesNotMatchRegex;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text\TextDoesNotStartWith;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text\TextEndsWith;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text\TextIsLowerCase;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text\TextIsNotLowerCase;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text\TextIsNotUpperCase;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text\TextIsUpperCase;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text\TextMatchesRegex;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text\TextStartsWith;
use WPCCrawler\Objects\Filtering\Enums\CommandType;

class CommandService {

    /** @var CommandService|null */
    private static $instance = null;

    /**
     * @var array<string, AbstractBaseCommand> Key-value pairs where keys are command keys and the values are objects
     *      extending to {@link AbstractBaseCommand}
     */
    private $registry = null;

    /**
     * @return CommandService
     * @since 1.11.0
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new CommandService();
        }

        return self::$instance;
    }

    /**
     * This is a singleton
     * @since 1.11.0
     */
    protected function __construct() { }

    /*
     * PRIVATE REGISTRY CREATION METHODS
     */

    /**
     * @return string[] Names of classes extending to {@link AbstractBaseCommand}
     * @since 1.11.0
     */
    private function getCommandClasses(): array {
        // Register all commands here
        return [
            /*
             * CONDITION COMMANDS
             */

            GreaterThanCommand::class,
            GreaterThanOrEqualCommand::class,
            LessThanCommand::class,
            LessThanOrEqualCommand::class,
            EqualToCommand::class,
            NotEqualToCommand::class,

            TextStartsWith::class,
            TextDoesNotStartWith::class,
            TextEndsWith::class,
            TextDoesNotEndWith::class,
            TextContains::class,
            TextDoesNotContain::class,
            TextIsUpperCase::class,
            TextIsNotUpperCase::class,
            TextIsLowerCase::class,
            TextIsNotLowerCase::class,
            TextMatchesRegex::class,
            TextDoesNotMatchRegex::class,

            ElementExists::class,
            ElementDoesNotExist::class,

            DateIsOlderThanRelative::class,
            DateIsOlderThanFixed::class,
            DateIsNewerThanRelative::class,
            DateIsNewerThanFixed::class,

            RecrawlingCommand::class,
            CrawlingCommand::class,
            FirstPageCommand::class,

            RequestError::class,
            HtmlError::class,
            AnyError::class,

            /*
             * ACTION COMMANDS
             */

            TextClear::class,
            TextFindReplace::class,
            TextMakeUpperCase::class,
            TextMakeLowerCase::class,
            TextMakeTitleCase::class,
            TextMakeSnakeCase::class,
            TextMakeKebabCase::class,
            TextMakeCamelCase::class,
            TextMakeStudlyCase::class,
            TextMakeUcFirst::class,
            TextMakeSlug::class,
            TextLimitWords::class,
            TextLimitChars::class,
            TextRemoveEmptyHtmlElements::class,
            TextRemoveLinks::class,
            TextTemplate::class,

            RemoveElement::class,
            ElementExchangeAttributes::class,
            ElementRemoveLinks::class,
            ElementRemoveAttributes::class,
            ElementClone::class,

            Calculate::class,

            SendEmailNotification::class,

            Stop::class,
            StopAndDeletePost::class,

            // Post page action commands
            SetAuthor::class,
            SetFeaturedImage::class,
            SetPostStatus::class,
            AddTags::class,
            AddCategories::class,

        ];
    }

    /*
     * PUBLIC METHODS
     */

    /**
     * Get the instance of a command
     *
     * @param string $key One of the constants defined in {@link CommandKey}.
     * @return AbstractBaseCommand Instance of the command
     * @throws CommandNotExistException If the command does not exist in the registry
     * @since 1.11.0
     */
    public function getCommand(string $key): AbstractBaseCommand {
        $registry = $this->getRegistry();

        if (!isset($registry[$key])) {
            throw new CommandNotExistException("Command with key '{$key}' does not exist.");
        }

        return $registry[$key];
    }

    /**
     * @return array[] An array of array representations of all registered commands.
     * @since 1.11.0
     */
    public function getCommandsAsArray(): array {
        $commands  = [];
        $instances = array_values($this->getRegistry());

        foreach($instances as $instance) {
            /** @var AbstractBaseCommand $instance */
            $commandArr = $instance->toArray();

            // Set the type of the command as well
            $commandArr['type'] = is_a($instance, AbstractActionCommand::class)
                ? CommandType::ACTION
                : CommandType::CONDITION;

            $commands[] = $commandArr;
        }

        return $commands;
    }

    /**
     * @return array<string, AbstractBaseCommand> See {@link $registry}
     * @since 1.11.0
     */
    public function getRegistry(): array {
        if ($this->registry === null) {
            $classNames = $this->getCommandClasses();

            $this->registry = [];
            foreach($classNames as $className) {
                try {
                    /** @var AbstractBaseCommand $className */
                    $instance = $className::newInstance();

                } catch (Exception $e) {
                    continue;
                }

                // Make sure the instance is of correct type
                if (!is_a($instance, AbstractBaseCommand::class)) continue;

                /** @var AbstractBaseCommand $instance */
                $this->registry[$instance->getKey()] = $instance;
            }
        }

        return $this->registry;
    }

}