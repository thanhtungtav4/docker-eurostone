<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/10/2018
 * Time: 15:19
 */

namespace WPCCrawler\Test\Base;


use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;
use WPCCrawler\Objects\Crawling\Interfaces\MakesCrawlRequest;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Filtering\FilterDependencyProvider\Page\PageFilterDependencyProvider;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxApplier;
use WPCCrawler\Objects\OptionsBox\OptionsBoxService;
use WPCCrawler\Objects\Settings\Factory\HtmlManip\CategoryHtmlManipKeyFactory;
use WPCCrawler\Objects\Settings\Factory\HtmlManip\PostHtmlManipKeyFactory;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Utils;
use WPCCrawler\WPCCrawler;

abstract class AbstractTest {

    /** @var string The key under which the view is added to the response */
    protected $responseViewKey = 'view';

    /** @var string The key under which the results are added to the response */
    protected $responseResultsKey = 'data';

    /**
     * @var string The name of the variable which stores the modified results, which are the results obtained after
     * applying the options configured in the options box, in the view.
     */
    protected $viewModifiedResultsKey = 'modifiedResults';

    /** @var float Start time of the test. */
    private $startTime = 0;

    /** @var float Memory usage just before conducting the test. */
    private $memoryInitial = 0;

    /** @var TestData */
    private $data;

    /** @var array|null */
    private $results = null;

    /** @var bool */
    private $isRun = false;

    const MANIPULATION_STEP_NONE                            = -1;
    const MANIPULATION_STEP_INITIAL_REPLACEMENTS            = 0;
    const MANIPULATION_STEP_FIND_REPLACE_ELEMENT_ATTRIBUTES = 1;
    const MANIPULATION_STEP_EXCHANGE_ELEMENT_ATTRIBUTES     = 2;
    const MANIPULATION_STEP_REMOVE_ELEMENT_ATTRIBUTES       = 3;
    const MANIPULATION_STEP_FIND_REPLACE_ELEMENT_HTML       = 4;
    const MANIPULATION_STEP_REMOVE_ELEMENTS_FROM_CRAWLER    = 5;

    /**
     * @param TestData $data The data to be used to conduct the test
     */
    public function __construct(TestData $data) {
        $this->data = $data;
    }

    /*
     * ABSTRACT METHODS
     */

    /**
     * Conduct the test and return an array of results.
     *
     * @param TestData $data Information required for the test
     * @return array|null
     */
    protected abstract function createResults($data): ?array;

    /**
     * Create the view of the response
     *
     * @return View|null
     */
    protected abstract function createView();

    /*
     * PUBLIC METHODS
     */

    /**
     * Run the test
     *
     * @return $this
     */
    public function run(): self {
        WPCCrawler::setDoingUnitTest(true);

        // Mark the start time and initial memory usage so that we can calculate elapsed time and memory usage later.
        $this->startTime = microtime(true);
        $this->memoryInitial = memory_get_usage();

        try {
            $this->results = $this->createResults($this->data);

        } catch (Exception $e) {
            $this->results = [];

            Informer::addInfo($e->getMessage() ?: _wpcc('An error occurred during the test.'))
                ->setException($e)->addAsLog();
        }

        $this->isRun = true;
        return $this;
    }

    /**
     * Get the test results
     *
     * @return array Test results as an array
     * @throws Exception If the test has not been run
     */
    public function getResults(): array {
        $this->checkIfRunOnce();
        return $this->results ?: [];
    }

    /**
     * Get JSON that shows the results. The results include an HTML view as well.
     *
     * @return string
     * @throws Exception If the test has not been run
     */
    public function getResponse(): string {
        $this->checkIfRunOnce();

        // Create the response
        $response = [
            $this->responseResultsKey => $this->getResults() ?: []
        ];

        // If view exists, add it to the response.
        $view = $this->createView();
        if ($view) {

            // If the test data does not come from the options box, apply available options box settings to the results.
            if (!$this->getData()->isFromOptionsBox()) {
                $this->addOptionsBoxResultsToView($view);
            }

            // Add memory usage and elapsed time
            $view->with([
                'memoryUsage' => $this->calculateMemoryUsage(),
                'elapsedTime' => $this->calculateElapsedTime()
            ]);

            // Render the view and add it to the response
            $response[$this->responseViewKey] = $view->render();

        } else {
            // If there is no view, add information messages.
            $response['infoView']       = Utils::view('partials/info-list')->render();
            $response['infoStyle']      = Factory::assetManager()->getInformationStyle();
            $response['memoryUsage']    = $this->calculateMemoryUsage();
            $response['elapsedTime']    = $this->calculateElapsedTime();
        }

        // Return the response by parsing it to JSON
        $json = json_encode($response);

        // If there is an error when parsing the response to JSON, log it.
        if ($json === false) {
            Informer::add((new Information(
                sprintf(_wpcc('JSON encoding error (%1$s)'), json_last_error()),
                json_last_error_msg(),
                InformationType::ERROR
            ))->addAsLog());

            // If the error was caused by malformed UTF-8 chars, try to convert the encoding and try again
            if (json_last_error() === JSON_ERROR_UTF8) {
                // If there is a view, render it so that the information can be shown in the view.
                if ($view) $response[$this->responseViewKey] = $view->render();

                // Now, try to fix the encoding and try to parse it to JSON again.
                $response = Utils::deepFixMixedUTF8Encoding($response);
                $json = json_encode($response);

                // If there still is a problem
                if ($json === false) {
                    // Remove the results and set the view as info view so that the user can see what went wrong.
                    $response[$this->responseResultsKey] = [];
                    $response[$this->responseViewKey] = Utils::view('partials/info-list')->render();
                    $json = json_encode($response);
                }
            }
        }

        return $json === false
            ? ''
            : $json;
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * @param AbstractBot|null $bot             A bot. The data required for filters will be retrieved from this bot.
     *                                          Hence, this must be the bot that makes the requests for the test, if
     *                                          any.
     * @param Crawler|null     $crawler
     * @param null|int         $lastStep        One of the constants of this class whose name starts with MANIPULATION_STEP,
     *                                          e.g.
     *                                          {@link MANIPULATION_STEP_REMOVE_ELEMENT_ATTRIBUTES}. If this is null, all
     *                                          manipulation steps will be applied.
     * @param null|string      $fallbackBaseUrl See {@link AbstractBot::resolveRelativeUrls()}
     */
    protected function applyHtmlManipulationOptions(?AbstractBot $bot, ?Crawler &$crawler, $lastStep = null, $fallbackBaseUrl = null): void {
        if (!$crawler) return;

        // Create a bot by adding the manipulation options
        $dummyBot = new DummyBot($this->data->getManipulationOptions(), null, $this->data->getUseUtf8(), $this->data->getConvertEncodingToUtf8());

        if ($bot instanceof MakesCrawlRequest) {
            $dummyBot
                ->setResponseHttpStatusCode($bot->getResponseHttpStatusCode())
                ->setCrawlingUrl($bot->getCrawlingUrl());
        }

        // Apply manipulation steps and stop at the last manipulation step
        $this->applyHtmlManipulationSteps($dummyBot, $crawler, $fallbackBaseUrl, $lastStep);
    }

    /**
     * @return bool|null True if the manipulation options are retrieved from post tab. If not, false. Null, if there is
     *                   no manipulation option.
     * @since 1.9.0
     */
    protected function isManipulationOptionsForPost(): ?bool {
        // Make sure there are manipulation options
        $manipulationOptions = $this->data->getManipulationOptions();
        if (!$manipulationOptions) return null;

        // Make sure there are array keys
        $optionKeys = array_keys($manipulationOptions);
        if (!$optionKeys) return null; // @phpstan-ignore-line

        // Find out if this is for post or category
        return Str::startsWith($optionKeys[0], '_post');
    }

    /**
     * Configure the options box applier. This is called before the applier applies the options. So, you can configure
     * the applier here. By default, all options will be applied.
     *
     * @param BaseOptionsBoxApplier $optionsBoxApplier
     */
    protected function configureOptionsBoxApplier($optionsBoxApplier): void {

    }

    /*
     * GETTERS
     */

    /**
     * @return TestData
     */
    public function getData(): TestData {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isRun(): bool {
        return $this->isRun;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * @throws Exception If the test has not been run
     */
    private function checkIfRunOnce(): void {
        if (!$this->isRun) {
            throw new Exception("The test has not been run. You have to run the test first.");
        }
    }

    /**
     * Applies options box options and adds the results to the given view under {@link $viewModifiedResultsKey} key.
     *
     * @param View $view
     * @throws Exception
     * @since 1.8.0
     */
    private function addOptionsBoxResultsToView($view): void {
        // Get options box data
        $baseOptionsBoxData = $this->data->getOptionsBoxData();
        $optionsBoxData = $baseOptionsBoxData ? $baseOptionsBoxData->getData() : null;
        if (!$optionsBoxData) return;

        // Create an options box applier
        $optionsBoxApplier = OptionsBoxService::getInstance()->createApplierFromRawData($optionsBoxData);
        if (!$optionsBoxApplier) return;

        $optionsBoxApplier
            ->setForTest(true)
            ->setFromOptionsBox(false);

        // Let the child configure the options box applier.
        $this->configureOptionsBoxApplier($optionsBoxApplier);

        $modifiedResults = array_map(function ($v) use (&$optionsBoxApplier) {
            $res = $optionsBoxApplier->apply($v);
            if (is_object($res) || is_array($res)) return '';

            return $res;
        }, $this->getResults());

        // Remove null values from the modified results.
        $modifiedResults = array_filter($modifiedResults, function($v) {
            return $v !== null;
        });

        // Add the modified results to the view
        $view->with($this->viewModifiedResultsKey, $modifiedResults ?: []);
    }

    /**
     * Applies manipulation steps and stops at the given last step
     *
     * @param AbstractBot $bot                 A bot using which the manipulations will be made
     * @param Crawler     $crawler             The crawler to be manipulated
     * @param null|string $fallbackBaseUrl     See {@link AbstractBot::resolveRelativeUrls()}
     * @param null|int    $lastStep            One of the constants of this class whose name starts with
     *                                         MANIPULATION_STEP, e.g.
     *                                         {@link MANIPULATION_STEP_REMOVE_ELEMENT_ATTRIBUTES}. If this is null,
     *                                         all manipulation steps will be applied.
     */
    private function applyHtmlManipulationSteps($bot, &$crawler, $fallbackBaseUrl = null, $lastStep = null): void {
        $isForPost = $this->isManipulationOptionsForPost() ?: false;
        $keyFactory = $isForPost ? PostHtmlManipKeyFactory::getInstance() : CategoryHtmlManipKeyFactory::getInstance();

        if ($this->getData()->get('fromRequestFilter') === '1') return;
        $provider = new PageFilterDependencyProvider($bot, null);
        $bot->applyFilterSetting($keyFactory->getRequestFiltersKey(), $provider);

        if ($lastStep === static::MANIPULATION_STEP_NONE) return;

        $manipulationOptions = $this->data->getManipulationOptions();

        // Make initial replacements
        $crawler = $bot->makeInitialReplacements($crawler, Utils::array_get($manipulationOptions, $keyFactory->getFindReplaceFirstLoadKey()), $isForPost);
        $bot->setCrawler($crawler);
        if ($lastStep === static::MANIPULATION_STEP_INITIAL_REPLACEMENTS) return;

        // Apply HTML manipulations
        $bot->applyFindAndReplaceInElementAttributes($crawler, $keyFactory->getFindReplaceElementAttributesKey());
        if ($lastStep === static::MANIPULATION_STEP_FIND_REPLACE_ELEMENT_ATTRIBUTES) return;

        $bot->applyExchangeElementAttributeValues($crawler, $keyFactory->getExchangeElementAttributesKey());
        if ($lastStep === static::MANIPULATION_STEP_EXCHANGE_ELEMENT_ATTRIBUTES) return;

        $bot->applyRemoveElementAttributes($crawler, $keyFactory->getRemoveElementAttributesKey());
        if ($lastStep === static::MANIPULATION_STEP_REMOVE_ELEMENT_ATTRIBUTES) return;

        $bot->applyFindAndReplaceInElementHTML($crawler, $keyFactory->getFindReplaceElementHtmlKey());
        if ($lastStep === static::MANIPULATION_STEP_FIND_REPLACE_ELEMENT_HTML) return;

        // Clear the crawler from unnecessary elements
        $bot->removeElementsFromCrawler($crawler, Utils::array_get($manipulationOptions, $keyFactory->getUnnecessaryElementSelectorsKey()));
        if ($lastStep === static::MANIPULATION_STEP_REMOVE_ELEMENTS_FROM_CRAWLER) return;

        // Resolve relative URLs
        $bot->resolveRelativeUrls($crawler, $fallbackBaseUrl);

        if ($this->getData()->get('fromPageFilter') === '1') return;
        $bot->applyFilterSetting($keyFactory->getPageFiltersKey(), $provider);
    }

    /**
     * Calculates memory usage using {@link $memoryInitial}
     * @return string
     */
    private function calculateMemoryUsage(): string {
        return number_format((memory_get_usage() - $this->memoryInitial) / 1000000, 2);
    }

    /**
     * Calculates elapsed time using {@link $startTime}
     * @return string
     */
    private function calculateElapsedTime(): string {
        return number_format((microtime(true) - $this->startTime) * 1000, 2);
    }

}