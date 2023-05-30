<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/05/2020
 * Time: 08:37
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering;


use Exception;
use Illuminate\Support\Str;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractConditionCommand;
use WPCCrawler\Objects\Filtering\Explaining\Loggers\Logger;
use WPCCrawler\Objects\Filtering\FilterDependencyProvider\FilterDependencyProvider;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\WPCCrawler;

class FilteringUtils {

    /** @var FilteringService|null */
    private static $filteringService = null;

    /**
     * Injects data sources to the command and checks if the command applies
     *
     * @param AbstractConditionCommand $cmd      The command that will be checked if it applies or not
     * @param FilterDependencyProvider $provider Provider that will be used to inject dependencies of the command
     * @return bool True if the condition command applies
     * @since 1.11.0
     */
    public static function doesConditionCommandApply(AbstractConditionCommand $cmd, FilterDependencyProvider $provider): bool {
        $provider->injectDependencies($cmd);
        $currentResult = $cmd->doesApply();
        $provider->invalidateDependencies($cmd);

        return $currentResult;
    }

    /**
     * This is a convenience method that is used to get the actual value of $verbose
     *
     * @param bool|null $verbose True if the created items should be verbose, meaning that they should log extra
     *                           details. If this is null, this will be considered as true when this is run when
     *                           testing things, e.g. by using Tester.
     * @return bool If $verbose is a boolean, it is returned as-is. If it is null, returns what
     *              {@link WPCCrawler::isDoingGeneralTest()} returns.
     * @since 1.11.0
     */
    public static function getVerbose(?bool $verbose): bool {
        return $verbose === null ? WPCCrawler::isDoingGeneralTest() : $verbose;
    }

    /**
     * Get a dummy filtering service, which means a filtering service with no settings defined.
     *
     * @return FilteringService
     * @since 1.11.0
     */
    public static function getFilteringService(): FilteringService {
        if (self::$filteringService === null) {
            self::$filteringService = new FilteringService(new SettingsImpl([]));
        }

        return self::$filteringService;
    }

    /**
     * Test if a regular expression matches a text. This method logs any errors both to the given logger and as an
     * information message via {@link Informer}.
     *
     * @param string      $text    A text
     * @param string      $regex   A regular expression that the text should match
     * @param string      $cmdName Name of the command. This will be used when logging the error, if any.
     * @param Logger|null $logger  The logger to which the errors will be added as a message, if any.
     * @return false|int Returns what {@link preg_match} returns
     * @since 1.11.0
     */
    public static function doesTextMatchRegex(string $text, string $regex, string $cmdName, ?Logger $logger = null) {
        // If the regular expression does not start with a forward slash, encapsulate the regex with forward slashes.
        // We use the forward slash character as a delimiter.
        if (!Str::startsWith($regex, '/')) {
            $regex = "/{$regex}/";
        }

        try {
            // Set an error handler to catch any errors that might occur when matching the regular expression.
            set_error_handler(function($errorNo, $errorString) {
                throw new Exception($errorString, $errorNo);
            });

            $result = preg_match($regex, $text);

        } catch (Exception $e) {
            $result = false;

            $msgRegexError = sprintf(_wpcc('Regular expression error at "%s" command'), $cmdName);
            Informer::add((new Information($msgRegexError, $e->getMessage(), InformationType::ERROR))
                ->addAsLog());

            if ($logger) $logger->addMessage($msgRegexError . ': ' . $e->getMessage());
        }

        // Restore the error handler
        restore_error_handler();

        return $result;
    }

    /**
     * Check if data type(s) exist in a collection
     *
     * @param int|int[] $type     One of the constants defined in {@link ValueType}
     * @param int[]     $haystack A collection of constants defined in {@link ValueType}, where the given type's
     *                            existence will be checked
     * @return bool True if all of the given value types exist in the haystack
     * @since 1.11.0
     */
    public static function hasDataType($type, array $haystack): bool {
        if (in_array(ValueType::T_ANY, $haystack)) return true;

        foreach((array) $type as $t) {
            if (!in_array($t, $haystack)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int|int[] $type     One of the constants defined in {@link ValueType}
     * @param int[]     $haystack A collection of constants defined in {@link ValueType}, where the given type's
     *                            existence will be checked
     * @return bool True if one of the given data types exists in the haystack
     * @since 1.11.0
     */
    public static function containsDataType($type, array $haystack): bool {
        if (in_array(ValueType::T_ANY, $haystack)) return true;

        foreach((array) $type as $t) {
            if (in_array($t, $haystack)) {
                return true;
            }
        }

        return false;
    }
}