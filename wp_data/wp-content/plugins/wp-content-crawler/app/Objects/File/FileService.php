<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 12/12/2018
 * Time: 18:49
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\File;


use Exception;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Enums\InformationMessage;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\OptionsBox\OptionsBoxService;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Traits\ShortCodeReplacerAndFindAndReplace;
use WPCCrawler\Utils;

class FileService {

    use ShortCodeReplacerAndFindAndReplace;

    /** @var int Maximum length of file name */
    const MAX_FILE_NAME_LENGTH = 240;

    /** @var string Opening brackets for the short codes in the name of a file. */
    const SC_OPENING_BRACKETS = 'sc123sc';

    /** @var string Closing brackets for the short codes in the name of a file. */
    const SC_CLOSING_BRACKETS = 'cs321cs';

    /*
     *
     */

    /** @var FileService */
    private static $instance = null;

    /** @var Filesystem */
    private $fs = null;

    /** @var string */
    private $tempDir = null;

    /** @var string Temporary file storage directory path relative to WP's uploads directory */
    protected $relativeTempDirPath = '/wpcc-temp';

    /**
     * Get the instance
     *
     * @return FileService
     * @since 1.8.0
     */
    public static function getInstance() {
        if (static::$instance === null) static::$instance = new FileService();
        return static::$instance;
    }

    /** This is a singleton. */
    protected function __construct() {}

    /**
     * Get absolute path under WordPress' uploads directory for a relative path.
     *
     * @param string $relativePath A path relative to uploads directory of WordPress.
     * @return null|string If an error occurs, null. Otherwise, absolute path for the given relative path.
     * @since 1.8.0
     */
    public function getPathUnderUploadsDir($relativePath) {
        $uploadsDir = $this->getUploadsDir();
        if (!$uploadsDir) return null;

        // Create the target directory path. The directory should be relative to the uploads directory.
        $relativePath = trim(str_replace('/', DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);

        // The new directory path must start with the uploads directory path, since we want to restrict the move
        // operation. The user can move the file into another folder in uploads directory.
        try {
            $newDirectoryPath = $this->getAbsolutePath($relativePath, $uploadsDir);
        } catch (Exception $e) {
            Informer::addError(
                sprintf(
                    _wpcc('%1$s directory is not in %2$s directory. The file can only be moved to a directory that 
                    is under the uploads directory of WordPress.'),
                    $uploadsDir . DIRECTORY_SEPARATOR . $relativePath,
                    $uploadsDir
                )
            )->addAsLog();
            return null;
        }

        return $newDirectoryPath;
    }

    /**
     * Get absolute path of the file by restricting defining a path that is above the given root path.
     *
     * @param string $filePath Relative file path
     * @param string $root     Root directory path
     * @return string Absolute path of the file
     * @throws Exception If the given file path tries to go above the root.
     * @since 1.8.0
     * @see https://stackoverflow.com/a/39796579/2883487
     */
    public function getAbsolutePath($filePath, $root) {
        $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        $path = [];
        foreach (explode('/', $filePath) as $part) {
            // Ignore empty parts and current directory parts
            if (empty($part) || $part === '.') continue;

            if ($part !== '..') {
                // We found a new part. Add it to the parts.
                array_push($path, $part);

            } else if (count($path) > 0) {
                // Going back up is only allowed if there is at least one path.
                array_pop($path);

            } else {
                // Going back up is not allowed if there is no path.
                throw new Exception('Climbing above the root is not permitted.');
            }
        }

        // Prepend the root directory to the found parts.
        array_unshift($path, $root);

        // Combine the parts with directory separator and return.
        return join(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Get a valid file name. This makes sure there is no directory separators in the file name.
     *
     * @param string|null $fileName File name to be validated.
     * @return false|string If the name cannot be made valid, false. Otherwise, a valid name.
     * @since 1.8.0
     */
    public function validateFileName(?string $fileName) {
        // Make sure the new name does not contain any directory separators
        $fileName = $this->forceDirectorySeparator($fileName);
        if (!$fileName) return false;

        // Get the parts
        $parts = explode(DIRECTORY_SEPARATOR, $fileName);

        // Get the last part.
        $fileName = array_pop($parts);

        // If the new name does not exist, return false.
        if (!$fileName) return false;

        // Replace remaining short codes' brackets with URL-safe brackets. This is done also because we do not want the
        // short codes in the name of the files get replaced by a short code replacement operation that might be done in
        // PostTemplatePreparer. Because, name of the files cannot be changed simply. They need to be moved to their
        // new location in the file system. Making this replacement will achieve this. Later on, we can replace the
        // short codes using the opening and closing brackets defined as constants here.
        $fileName = $this->findAndReplaceSingle(
            '/\[([^\]]+)\]/',
            static::SC_OPENING_BRACKETS . '$1' . static::SC_CLOSING_BRACKETS,
            $fileName,
            true
        );

        // Make the name suitable for a URL. This also limits the length to 200 chars.
        $fileName = sanitize_title($fileName);

        // Make sure the file name length is in the limits
        if (mb_strlen($fileName) > static::MAX_FILE_NAME_LENGTH) {
            // If not, trim it such that it is in the limits.
            $fileName = mb_substr($fileName, 0, static::MAX_FILE_NAME_LENGTH);
        }

        return $fileName;
    }

    /**
     * Changes forward and backward slashes with {@link DIRECTORY_SEPARATOR}
     *
     * @param string|null $path A path
     * @return string
     * @since 1.8.0
     */
    public function forceDirectorySeparator(?string $path): string {
        if (!$path) return '';

        return str_replace('\\', DIRECTORY_SEPARATOR, str_replace('/', DIRECTORY_SEPARATOR, $path));
    }

    /**
     * Get WordPress' uploads directory path.
     *
     * @return null|string Path for the uploads directory of WordPress. If the path could not be retrieved, returns null.
     * @since 1.8.0
     */
    public function getUploadsDir() {
        $dirArr = $this->getUploadDirArray();

        // The result contains 'error' when there is an error. If the path could not be retrieved, inform the user
        // about the error.
        if (isset($dirArr['error']) && $dirArr['error']) {
            Informer::addError(_wpcc('Upload directory could not be retrieved: ') . $dirArr['error'])->addAsLog();
            return null;
        }

        return realpath(rtrim($dirArr['basedir'], DIRECTORY_SEPARATOR)) ?: null;
    }

    /**
     * Get URL of the file that is located in WordPress' uploads directory.
     *
     * @param string|null $path Path of the file that is under WP's uploads directory
     * @return null|string If the directory information cannot be retrieved from WordPress, null. Otherwise, URL for the
     *                     file.
     * @since 1.8.0
     */
    public function getUrlForPathUnderUploadsDir($path): ?string {
        if ($path === null) return null;

        // Get WordPress' upload directory path and URL
        $dirArr = $this->getUploadDirArray();
        if (isset($dirArr['error']) && $dirArr['error']) return null;

        // Make sure the base URL does not end with a forward slash
        $baseUploadsUrl = rtrim($dirArr['baseurl'], '/');

        // Make sure the real path of the base directory is retrieved without a leading directory separator
        $baseUploadsDir = realpath(rtrim($dirArr['basedir'], DIRECTORY_SEPARATOR));
        if (!$baseUploadsDir) return null;

        // Remove the base uploads directory path from the local file path
        $relativePath = trim(str_replace($baseUploadsDir, '', $path), DIRECTORY_SEPARATOR);

        // Replace directory separators in the relative file path with a forward slash, since URLs use forward slashes.
        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

        // Create the local URL by appending relative URL to the base uploads URL
        return $baseUploadsUrl . '/' . $relativePath;
    }

    /**
     * Get unique file path under a directory.
     *
     * @param string      $baseName    File name with extension
     * @param string      $directory   Directory path. The file name will be unique to this directory.
     * @param null|string $currentPath Current path of the file, if exists.
     * @return string Absolute file path that is unique to the given directory
     * @since 1.8.0
     */
    public function getUniqueFilePath(string $baseName, string $directory, ?string $currentPath = null): string {
        $directory = rtrim($this->forceDirectorySeparator($directory), DIRECTORY_SEPARATOR);

        // If the new path is the same as the old one, do nothing and return the path.
        if ($currentPath === $directory . DIRECTORY_SEPARATOR . $baseName) return $currentPath;

        // Get required file information
        $ext        = $this->getFileSystem()->extension($baseName);
        $fileName   = $this->getFileSystem()->name($baseName);

        $count = 0;
        do {
            // Create the new path. If this is not the first try, then append a number to the file name.
            $newBaseName = $count > 0 ? "{$fileName}-{$count}.{$ext}" : "{$fileName}.{$ext}";
            $newPath = $directory . DIRECTORY_SEPARATOR . $newBaseName;

            $count++;
            // Check if the new path is a path to an existing file. If so, rename the file by appending a number to it.
        } while($this->getFileSystem()->exists($newPath));

        return $newPath;
    }

    /**
     * Saves the remote file of the media file to the local environment by using its source URL. After successful save,
     * sets the media file's local path and local URL.
     *
     * @param MediaFile $mediaFile
     * @param PostBot   $postBot
     * @return bool True on success. Otherwise, false.
     * @since 1.8.0
     */
    public function saveMediaFile(MediaFile $mediaFile, PostBot $postBot) {
        // Save the file
        $file = MediaService::getInstance()
            ->saveMedia($mediaFile->getSourceUrl(), MediaSavingOptions::fromSiteSettings($postBot->getSettingsImpl()));

        // If there is no file, continue with the next one.
        if (!$file) {
            // Inform the user.
            Informer::add(Information::fromInformationMessage(
                InformationMessage::FILE_COULD_NOT_BE_SAVED_ERROR,
                sprintf(_wpcc('Original URL: %1$s, Prepared URL: %2$s'), $mediaFile->getOriginalSourceUrl(), $mediaFile->getSourceUrl()),
                InformationType::INFO
            )->addAsLog());

            return false;
        }

        /**
         * Modify the saved media file. You can move the file, rename it, modify it by, say, adding a text
         * on it, etc.
         *
         * @param array   $file             An array containing data about the file, such as its path and URL.
         * @param string  $preparedMediaUrl The URL from which the file was retrieved
         * @param string  $postUrl          URL of the post page that stores the target media file
         * @param int     $siteId           Site ID
         * @param PostBot $postBot          PostBot itself
         *
         * @return array
         * @since 1.6.3
         */
        $file = apply_filters('wpcc/post/media/file', $file, $mediaFile->getSourceUrl(), $postBot->getPostUrl(), $postBot->getSiteId(), $postBot);

        // Set the local file path and URL of the media file
        $mediaFile
            ->setLocalPath($file['file'])
            ->setLocalUrl($file['url']);

        return true;
    }

    /**
     * Applies short codes to media file's name.
     *
     * @param MediaFile $mediaFile
     * @param array     $map See {@link ShortCodeReplacer::replaceShortCodes}
     * @return array Find and replace configurations that can be used to replace old file URLs with the changed ones
     * @since 1.8.0
     */
    public function applyShortCodesToMediaFileName(MediaFile $mediaFile, &$map) {
        $frForMedia = [];

        // Store the current local URL
        $prevLocalUrl = $mediaFile->getLocalUrl();

        // Replace short codes in the name of the file
        $currentName = $mediaFile->getName();
        $newName = $currentName !== null 
            ? $this->replaceShortCodesSingle($map, $currentName, null, static::SC_OPENING_BRACKETS, static::SC_CLOSING_BRACKETS)
            : null;
        $this->clearRemainingPredefinedShortCodes($newName, static::SC_OPENING_BRACKETS, static::SC_CLOSING_BRACKETS);
        $mediaFile->rename($newName);

        // Add find-replace configs for the media's original URL and the previous local URL, that is the local URL
        // of the file before it has just been renamed. By this way, we will be able to change all possible URLs
        // with the right local URL.
        if ($prevLocalUrl !== $mediaFile->getLocalUrl()) {
            $frForMedia[] = $this->createFindReplaceConfigForUrl($prevLocalUrl, $mediaFile->getLocalUrl());
        }

        if ($mediaFile->getOriginalSourceUrl() !== $mediaFile->getLocalUrl()) {
            $frForMedia[] = $this->createFindReplaceConfigForUrl($mediaFile->getOriginalSourceUrl(), $mediaFile->getLocalUrl());
        }

        return $frForMedia;
    }

    /**
     * Prepares file data using file URL selectors
     *
     * @param PostBot $bot                  The bot that will be used to extract data
     * @param Crawler $crawler              The crawler from which the data will be extracted
     * @param array   $fileUrlSelectors     An array of selectors. Each selector is an array that should contain
     *                                      'selector', and 'attr' keys whose values are strings. 'selector' is a CSS
     *                                      selector, and 'attr' is the target attribute from which the content will
     *                                      be retrieved. Default 'attr' is 'src'.
     * @param bool    $singleResult         True if only one result is enough.
     * @return MediaFile[] Found data as a MediaFile array
     * @since 1.8.0
     */
    public function saveFilesWithSelectors(PostBot $bot, Crawler $crawler, $fileUrlSelectors, $singleResult = false) {
        $mediaFiles = [];

        // Prepare the file data
        foreach($fileUrlSelectors as $selectorData) {
            $selector = Utils::array_get($selectorData, SettingInnerKey::SELECTOR);
            if (!$selector) continue;

            $attr = Utils::array_get($selectorData, SettingInnerKey::ATTRIBUTE);
            if (!$attr) $attr = 'src';

            // Get file data
            $fileData = $bot->extractData($crawler, $selector, [$attr, "alt", "title"], false, $singleResult, true);
            if (!$fileData) continue;

            // If the file data is not an array, make it an array.
            if (!is_array($fileData)) $fileData = [$fileData];

            // Try to get an options box applier for this selector data
            $applier = OptionsBoxService::getInstance()->createApplierFromSelectorData($selectorData);

            // Make replacements
            foreach ($fileData as $mFileData) {
                // Get the source URL
                // If the file data is an array
                if (is_array($mFileData)) {
                    // It must have an index of the given $attr.
                    if (!isset($mFileData[$attr])) {
                        // $attr index does not exist. Hence, we do not have a file URL. Continue with the next one.
                        continue;
                    }

                    $src = $mFileData[$attr];
                } else {
                    $src = $mFileData;
                }

                // If there is no URL, continue with the next one.
                if (!$src) continue;

                // Store the original source URL
                $original = $src;

                // Prepare the media URL
                try {
                    $src = $bot->resolveUrl($src);

                } catch (Exception $e) {
                    Informer::addError(_wpcc('URL could not be resolved.') . ' - ' . $src)->addAsLog();
                }

                // Create a media file for this file
                $mediaFile = (new MediaFile($src, null))
                    ->setOriginalSourceUrl($original);

                // Get "alt" and "title" values
                if (is_array($mFileData)) {
                    $mediaFile
                        ->setMediaAlt(Utils::array_get($mFileData, 'alt'))
                        ->setMediaTitle(Utils::array_get($mFileData, 'title'));
                }

                // Save the media file
                $success = $this->saveMediaFile($mediaFile, $bot);
                if (!$success) continue;

                // Apply file options box options
                if ($applier) $applier->apply($mediaFile);

                // Add it among others
                $mediaFiles[] = $mediaFile;

                // Stop if there should only be a single result.
                if ($singleResult) break;
            }

        }

        return $mediaFiles;
    }

    /**
     * @return string Absolute path of the plugin's temporary file storage directory
     * @since 1.8.0
     */
    public function getTempDirPath(): string {
        if ($this->tempDir === null) {
            // Get WordPress' upload directory path and URL
            $dirArr = $this->getUploadDirArray();

            // Make sure the real path of the base directory is retrieved without a leading directory separator
            $baseUploadsDir = realpath(rtrim($dirArr['basedir'], DIRECTORY_SEPARATOR));

            $this->tempDir = $baseUploadsDir . DIRECTORY_SEPARATOR . ltrim($this->relativeTempDirPath, DIRECTORY_SEPARATOR);

            if (!$this->getFileSystem()->isDirectory($this->tempDir)) {
                $this->getFileSystem()->makeDirectory($this->tempDir, 0755, true);

                // If the directory still does not exist, notify the user.
                if (!$this->getFileSystem()->isDirectory($this->tempDir)) {
                    Informer::addError(sprintf(
                        _wpcc('Directory "%1$s" could not be created. Please make sure this directory exists and it is writable.'),
                        $this->tempDir
                    ))->addAsLog();
                }
            }

        }

        return $this->tempDir;
    }

    /**
     * @return Filesystem
     * @since 1.8.0
     */
    public function getFileSystem() {
        if ($this->fs === null) $this->fs = Factory::fileSystem();
        return $this->fs;
    }

    /**
     * Get uploads directory details of WordPress.
     *
     * @return array See {@link wp_upload_dir()}
     * @since 1.9.0
     * @uses wp_upload_dir()
     */
    private function getUploadDirArray() {
        // wp_get_upload_dir exists starting from 4.5. Hence, we use wp_upload_dir to support older versions of
        // WordPress. The function call below is the same call as wp_get_upload_dir does.
        return wp_upload_dir(null, false);
    }
}
