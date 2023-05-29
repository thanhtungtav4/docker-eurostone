<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/10/2018
 * Time: 09:52
 */

namespace WPCCrawler\Test\Base;


use Exception;
use Illuminate\Contracts\View\View;
use WPCCrawler\Objects\Crawling\Data\CategoryData;
use WPCCrawler\Objects\Crawling\Data\PostData;
use WPCCrawler\Objects\File\MediaService;
use WPCCrawler\Objects\Filtering\Explaining\FilterExplainingService;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Test\Data\GeneralTestData;
use WPCCrawler\Test\Enums\TestType;
use WPCCrawler\Test\General\GeneralTestHistoryManager;
use WPCCrawler\Utils;
use WPCCrawler\WPCCrawler;

abstract class AbstractGeneralTest {

    /** @var GeneralTestData Data to be used to conduct the test */
    private $data;

    /** @var bool */
    private $isRun = false;

    /** @var float Time elapsed when conducting the test, in ms. */
    private $elapsedTime = 0;

    /** @var float Memory usage for the test, in MB. */
    private $memoryUsage = 0;

    /**
     * @var array An array that stores information about the test results. Keys are strings that describe their values.
     * E.g ["Post tags" => ["tag 1", "tag 2"], "Elapsed time" => "1000 ms"]
     */
    private $info = [];

    /** @var GeneralTestHistoryManager */
    private $historyHandler;

    /**
     * @param GeneralTestData $data
     */
    public function __construct(GeneralTestData $data) {
        $this->data = $data;
        $this->historyHandler = new GeneralTestHistoryManager();
    }

    /*
     * ABSTRACT METHODS
     */

    /**
     * Conduct the test and return an array of results.
     *
     * @param GeneralTestData $data
     * @throws Exception
     */
    protected abstract function createResults(GeneralTestData $data): void;

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
        WPCCrawler::setDoingGeneralTest(true);

        // Delete the files that were saved when conducting the previous test
        MediaService::getInstance()->deletePreviouslySavedTestFiles();

        $startTime = microtime(true);
        $memoryInitial = memory_get_usage();

        // Create the results
        try {
            $this->createResults($this->getData());

        } catch (Exception $e) {
            Informer::addInfo($e->getMessage() ?: _wpcc('An error occurred during the test.'))
                ->setException($e)->addAsLog();
        }

        // Update the history
        $this->historyHandler->addItemToHistoryWithGeneralTestData($this->getData());

        // Mark it as run
        $this->isRun = true;

        // Set performance variables
        $this->memoryUsage = (float) ((memory_get_usage() - $memoryInitial) / 1000000);
        $this->elapsedTime = (float) ((microtime(true) - $startTime) * 1000);

        // Prepare the info. First, get the current info. It is about the details.
        $info = $this->info;

        // Create a new info
        $this->info = [];

        // If there are details, add them to 'Details' section.
        if ($info) $this->info[_wpcc('Details')] = $info;

        // Add the performance info under 'Performance' section.
        $this->info[_wpcc('Performance')] = $this->getPerformanceInfo();

        // Save test file paths.
        MediaService::getInstance()->saveTestFilePaths();

        return $this;
    }

    /**
     * Get the HTML view that shows the results.
     *
     * @return string|null HTML
     * @throws Exception If the test has not been run
     */
    public function getResponse(): ?string {
        $this->checkIfRunOnce();

        // Get the result view
        $view = $this->createView();
        if ($view === null) {
            return null;
        }

        // Create test history view
        $testHistoryView = Utils::view('site-tester.test-history')
            ->with('testHistory', $this->historyHandler->getTestHistory());

        $result = json_encode([
            'view'                      => $view->render(),
            'viewTestHistory'           => $testHistoryView->render(),
            'filterSettingExplanations' => FilterExplainingService::getInstance()->explainAll(),
        ]);

        return $result === false
            ? null
            : $result;
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * @param string $description       Description of the information
     * @param mixed $value              Actual information
     * @param bool $doNotAddIfNotValid  If this is true, the value will be checked against its validity. If it is valid,
     *                                  i.e. not null, 0, etc., it will be added. Otherwise, it won't be added to the
     *                                  info array.
     */
    protected function addInfo($description, $value, $doNotAddIfNotValid = false): void {
        if ($doNotAddIfNotValid) {
            if ($value) $this->info[$description] = $value;

        } else {
            $this->info[$description] = $value;
        }
    }

    /**
     * @param CategoryData|PostData|null $data The data from which the next page URL information will be retrieved.
     */
    protected function addNextPageUrlInfo($data): void {
        if (!$data) return;

        // Get the next page URL.
        $nextPageUrl = $data->getNextPageUrl();

        // If there is no next page, stop.
        if(!$nextPageUrl) return;

        // Add next page info.
        $this->addInfo(
            _wpcc("Next Page URL"),
            Utils::view('site-tester/url-with-test')->with([
                'url'      => $nextPageUrl,
                'testType' => $data instanceof PostData ? TestType::POST : TestType::CATEGORY,
            ])->render()
        );
    }

    /*
     * GETTERS
     */

    /**
     * @return GeneralTestData See {@link data}
     */
    public function getData(): GeneralTestData {
        return $this->data;
    }

    /**
     * @return float See {@link elapsedTime}
     */
    public function getElapsedTime() {
        return $this->elapsedTime;
    }

    /**
     * @return float See {@link memoryUsage}
     */
    public function getMemoryUsage() {
        return $this->memoryUsage;
    }

    /**
     * @return array See {@link info}
     */
    public function getInfo(): array {
        return $this->info;
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
     * @return array An array containing elapsed time and used memory info. Keys are the names of the added info,
     *               values are their values.
     * @since 1.8.0
     */
    private function getPerformanceInfo(): array {
        return [
            _wpcc("Time")        => number_format($this->getElapsedTime(), 2) . ' ms',
            _wpcc("Memory Used") => number_format($this->getMemoryUsage(), 2) . ' MB',
        ];
    }

}