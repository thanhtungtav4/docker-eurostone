<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 31/10/2018
 * Time: 22:24
 */

namespace WPCCrawler\Test\Tests;


use Exception;
use Illuminate\Contracts\View\View;
use WPCCrawler\Objects\OptionsBox\Boxes\Def\DefaultOptionsBoxApplier;
use WPCCrawler\Objects\OptionsBox\Boxes\Def\DefaultOptionsBoxData;
use WPCCrawler\Objects\StringCalculator;
use WPCCrawler\Test\Base\AbstractTest;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Utils;

class CalculationTest extends AbstractTest {

    /** @var string */
    private $message;

    /**
     * Conduct the test and return an array of results.
     *
     * @param TestData $data Information required for the test
     * @return array|null
     */
    protected function createResults($data): ?array {
        // Here, form item values must be an array.
        if(!$data->getFormItemValues() || !is_array($data->getFormItemValues())) return null;

        $formula = array_values($data->getFormItemValues())[0];
        if (!$formula) return [];

        $results = [];

        if ($data->getTestData()) {
            // If the data comes from the options box, we cannot apply all options box settings. So, let's apply some
            // of them.

            /** @var DefaultOptionsBoxApplier|null $applier */
            $applier = null;

            if ($data->isFromOptionsBox()) {
                $applier = $data->applyOptionsBoxSettingsToTestData(function($applier) {
                    /** @var DefaultOptionsBoxApplier $applier */

                    // Since this is a calculation test, do not apply all calculation options coming from the options box.
                    // There might be a number of calculation options. We want to test only the calculation that the user
                    // wants to test.
                    $applier->setApplyCalculationOptions(false);

                    // Do not apply template options as well. Templates will be made ready after calculations are done.
                    $applier->setApplyTemplateOptions(false);
                });
            }

            // Create a string calculator using the calculation options
            /** @var DefaultOptionsBoxData $boxData */
            $boxData = $data->getOptionsBoxData();
            $calcOptions = $boxData->getCalculationOptions();
            if ($calcOptions === null) {
                return [];
            }

            $calc = new StringCalculator(
                $calcOptions->getDecimalSeparatorAfter(),
                $calcOptions->getPrecision(),
                $calcOptions->isUseThousandsSeparator()
            );

            $testData = $data->getTestData() ?: [];
            foreach($testData as $val) {
                try {
                    $isDefaultApplier = $applier instanceof DefaultOptionsBoxApplier;
                    if ($isDefaultApplier) {
                        $applier->replaceItemDotNotationShortCode($formula);
                    }

                    $results[] = $calc
                        ->calculateForX($formula, $isDefaultApplier && $applier->isTreatAsJson() ? 0 : $val);

                } catch(Exception $e) {
                    $results[] = "";
                }
            }
        }

        $this->message = sprintf(
            _wpcc('Test results for %1$s:'),
            "<span class='highlight formula'>$formula</span>"
        );

        return $results;
    }

    /**
     * Create the view of the response
     *
     * @return View|null
     * @throws Exception
     */
    protected function createView() {
        return Utils::view('partials/test-result')
            ->with("results", $this->getResults())
            ->with("message", $this->message);
    }
}
