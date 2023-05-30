<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/12/2018
 * Time: 17:32
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Test\Tests;


use WPCCrawler\Objects\File\MediaFile;
use WPCCrawler\Test\Base\AbstractFileOperationTest;

class FileMoveTest extends AbstractFileOperationTest {

    /**
     * @param MediaFile $mediaFile
     * @param string    $path
     * @return mixed Result of operation that will be sent as a result of the test.
     * @since 1.8.0
     */
    protected function doOperation($mediaFile, $path) {
        // Move the file to the directory path
        $success = $mediaFile->moveToDirectory($path);

        return !$success ? '' : $mediaFile->getLocalUrl();
    }

    /**
     * @param string $path Target path
     * @return string Test result message
     * @since 1.8.0
     */
    protected function createMessage($path): string {
        return sprintf(
            _wpcc('Test result for moving files to %1$s directory'),
            "<span class='highlight directory'>" . htmlspecialchars($path) . "</span>"
        );
    }
}