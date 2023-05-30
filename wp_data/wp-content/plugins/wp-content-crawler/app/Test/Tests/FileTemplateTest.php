<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 28/10/2018
 * Time: 21:14
 */

namespace WPCCrawler\Test\Tests;


use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use WPCCrawler\Objects\Enums\ShortCodeName;
use WPCCrawler\Objects\File\FileService;
use WPCCrawler\Objects\File\MediaFile;
use WPCCrawler\Objects\OptionsBox\Boxes\File\FileOptionsBoxApplier;
use WPCCrawler\Objects\Traits\ShortCodeReplacer;
use WPCCrawler\Test\Base\AbstractFileTest;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Utils;

class FileTemplateTest extends AbstractFileTest {

    use ShortCodeReplacer;

    /** @var string */
    private $message;

    /**
     * @param TestData    $data Information required for the test
     * @param MediaFile[] $mediaFiles
     * @return array
     * @since 1.8.0
     */
    protected function createFileTestResults($data, $mediaFiles): array {
        $formItemValues = $data->getFormItemValues();
        if (!is_array($formItemValues)) return [];
        
        // Get the template
        $template = Utils::array_get($formItemValues, 'template');
        if (!$template) return [];

        $results = [];

        // If the data comes from the options box, we cannot apply all options box settings. So, let's apply some
        // of them.
        if ($data->isFromOptionsBox()) {
            $data->applyOptionsBoxSettingsToTestData(function($applier) {
                /** @var FileOptionsBoxApplier $applier */

                // Tell the applier we are running from within an options box so that it returns MediaFile instances.
                $applier->setFromOptionsBox(true);

                // Since this is a template test, do not apply all template options coming from the options box.
                // There might be a number of template options. We want to test only the template that the user
                // wants to test.
                $applier->setApplyTemplateOptions(false);

                // Do not apply file operations options, since they are applied after template operations. The user
                // wants to see the template results.
                $applier->setApplyFileOperationsOptions(false);
            });
        }

        // Get if this template is for file name
        $isForName = Str::contains($data->get('formItemDotKey', ''), 'templates_file_name');

        foreach($data->getTestData() ?: [] as $val) {
            if (!is_a($val, MediaFile::class)) continue;
            /** @var MediaFile $val */

            $map = $val->getShortCodeMap();
            $result = $this->replaceShortCodesSingle($map, $template);

            // If the template is for file name, validate the name.
            if ($isForName) $result = FileService::getInstance()->validateFileName($result);

            $results[] = $result !== false ? $result : '';
        }

        $this->message = sprintf(
            _wpcc('%2$s Test results for %1$s:'),
            "<span class='highlight template'>" . (mb_strlen($template) > 200 ? mb_substr($template, 0, 199) . '...' : $template) . "</span>",
            sprintf(
                _wpcc('Only %1$s and file short codes are replaced for testing purposes. Values of some short codes
                    might not be available when testing here. Use Tester page for a complete test.'),
                "<span class='highlight short-code'>[" . ShortCodeName::WCC_ITEM . "]</span>"
            )
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