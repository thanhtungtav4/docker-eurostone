<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/10/2018
 * Time: 15:52
 */

namespace WPCCrawler\Test\Tests;


use Exception;
use Illuminate\Contracts\View\View;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Traits\FindAndReplaceTrait;
use WPCCrawler\Test\Base\AbstractTest;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Utils;

class FindReplaceTest extends AbstractTest {

    use FindAndReplaceTrait;

    /** @var string|null */
    private $message;

    /**
     * Apply find-replace to a subject.
     *
     * @param string $find    What to find
     * @param string $replace With what to replace
     * @param string $subject In what to find-replace
     * @param bool   $regex   True if $find is a regular expression.
     * @return string Modified result
     * @since 1.8.0
     */
    protected function applyFindReplace($find, $replace, $subject, $regex) {
        return $this->findAndReplaceSingle($find, $replace, $subject, $regex);
    }

    /**
     * Conduct the test and return an array of results.
     *
     * @param TestData $data Information required for the test
     * @return array|null
     */
    protected function createResults($data): ?array {
        // Here, form item values must be an array.
        $formItemValues = $data->getFormItemValues();
        if(!$formItemValues || !is_array($formItemValues)) return null;

        $subject    = $data->get("subject");
        $find       = Utils::array_get($formItemValues, SettingInnerKey::FIND);
        $replace    = Utils::array_get($formItemValues, SettingInnerKey::REPLACE);
        $regex      = isset($formItemValues[SettingInnerKey::REGEX]);

        $results = [];

        // Make the replacement for the subject.
        if ($subject !== null) {
            $results[] = $this->applyFindReplace($find, $replace, $subject, $regex);
        }

        // If there are other test data, make the replacements for them as well.
        $testData = $data->getTestData() ?: [];
        foreach($testData as $val) {
            $results[] = $this->applyFindReplace($find, $replace, $val, $regex);
        }

        $message = sprintf(
            _wpcc('Test result for find %1$s and replace with %2$s'),
            "<span class='highlight find'>" . htmlspecialchars($find) . "</span>",
            "<span class='highlight replace'>" . htmlspecialchars($replace) . "</span>"
        );

        if($regex) $message .= " " . _wpcc("(as regex)");
        $message .= ':';

        $this->message = $message;

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
            ->with("message", $this->message ?: '');
    }
}