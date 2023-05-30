<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 12/12/2018
 * Time: 17:29
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Test;


use Illuminate\Filesystem\Filesystem;
use WPCCrawler\Factory;
use WPCCrawler\Objects\File\FileService;
use WPCCrawler\Objects\File\MediaFile;

class TestService {

    /** @var TestService */
    private static $instance = null;

    /** @var Filesystem */
    private $fs = null;

    /**
     * Get the instance
     *
     * @return TestService
     * @since 1.8.0
     */
    public static function getInstance() {
        if (static::$instance === null) static::$instance = new TestService();
        return static::$instance;
    }

    /** This is a singleton. */
    protected function __construct() {}

    /**
     * @return Filesystem
     * @since 1.8.0
     */
    public function getFileSystem() {
        if ($this->fs === null) $this->fs = Factory::fileSystem();
        return $this->fs;
    }

    /**
     * Create a temporary local file for a source URL.
     *
     * @param string $sourceUrl Source URL of the file
     * @return null|MediaFile A MediaFile instance for the file in the source URL on success. Otherwise, null on failure.
     * @since 1.8.0
     */
    public function createTempMediaFileForUrl($sourceUrl) {
        // Get the path of the source URL. The path is the part that does does not include the domain and the query params.
        $path = parse_url($sourceUrl, PHP_URL_PATH);
        if (!$path) return null;

        // Get the base name (file name with extension) of the file from the path
        $fileBaseName = $this->getFileSystem()->basename($path);
        if (!$fileBaseName) return null;

        // Create the temporary file path with the base name
        $tempFilePath = FileService::getInstance()->getUniqueFilePath($fileBaseName, FileService::getInstance()->getTempDirPath());

        // Create a temp file. Write a dummy value into the file so that we can know if the file can be created.
        $writtenBytes = $this->getFileSystem()->put($tempFilePath, '1');

        // Return null if the file could not be created.
        if ($writtenBytes < 1) return null;

        // Create the media file
        $mediaFile = new MediaFile($sourceUrl, $tempFilePath);

        return $mediaFile;
    }

}