<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 12/12/2018
 * Time: 18:54
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Test\Base;


use Exception;
use Illuminate\Contracts\View\View;
use WPCCrawler\Objects\File\FileService;
use WPCCrawler\Objects\File\MediaFile;
use WPCCrawler\Objects\OptionsBox\Boxes\File\FileOptionsBoxApplier;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Utils;

abstract class AbstractFileOperationTest extends AbstractFileTest {

    /** @var string */
    private $message;

    /**
     * @param MediaFile $mediaFile
     * @param string $path
     * @return mixed Result of operation that will be sent as a result of the test.
     * @since 1.8.0
     */
    abstract protected function doOperation($mediaFile, $path);

    /**
     * @param string $path Target path
     * @return string Test result message
     * @since 1.8.0
     */
    abstract protected function createMessage($path): string;

    /**
     * @param TestData    $data Information required for the test
     * @param MediaFile[] $mediaFiles
     * @return array
     * @since 1.8.0
     */
    protected function createFileTestResults($data, $mediaFiles): array {
        $formItemValues = $data->getFormItemValues();
        if (!is_array($formItemValues)) return [];
        
        $path = Utils::array_get($formItemValues, "path");
        if (!$path) return [];

        // Get new directory path by making sure it does not go above the uploads directory.
        $path = FileService::getInstance()->getPathUnderUploadsDir($path);
        if (!$path) return [];

        $results = [];

        // If the data comes from the options box, we cannot apply all options box settings. So, let's apply some
        // of them.
        if ($data->isFromOptionsBox()) {
            $data->applyOptionsBoxSettingsToTestData(function($applier) {
                /** @var FileOptionsBoxApplier $applier */

                // Tell the applier we are running from within an options box so that it returns MediaFile instances.
                $applier->setFromOptionsBox(true);

                // Do not apply file operations options, since they are applied after template operations. The user
                // wants to see the template results.
                $applier->setApplyFileOperationsOptions(false);
            });
        }

        foreach($data->getTestData() ?: [] as $mediaFile) {
            if (!is_a($mediaFile, MediaFile::class)) continue;
            /** @var MediaFile $mediaFile */

            // Do the operation
            $results[] = $this->doOperation($mediaFile, $path);
        }

        $this->message = $this->createMessage($path) . ':';

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