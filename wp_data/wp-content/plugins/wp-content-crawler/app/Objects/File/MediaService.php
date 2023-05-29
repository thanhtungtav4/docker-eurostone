<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 23/12/2018
 * Time: 10:12
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\File;


use Exception;
use WP_Error;
use WPCCrawler\Environment;
use WPCCrawler\Exceptions\FileNotFoundException;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Enums\InformationMessage;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\WPCCrawler;

class MediaService {

    /** @var MediaService|null */
    private static $instance = null;

    /** @var string[] Stores the paths of files that are created when conducting a test. */
    private $testFilePaths = [];

    /**
     * @var string Base name of the file that will store the test file paths. We store it in a PHP file because we do
     *             not want the full paths to be accessible publicly for security reasons.
     */
    private $fileBaseName = 'temp-media-file-paths.php';

    /** @var bool True if the user agent filter is registered to WP. */
    private $userAgentFilterRegistered = false;

    /** @var bool True if the value of {@link $userAgentString} should be used when downloading files. */
    private $userAgentEnabled = false;

    /** @var bool True if the request args filter is registered to WP. */
    private $requestArgsFilterRegistered = false;

    /** @var bool True if the request args should be set when downloading files. */
    private $requestArgsEnabled = false;

    /** @var bool True if the HTTP API debug action is registered to WP. */
    private $httpApiDebugActionRegistered = false;

    /** @var bool True if the registered HTTP API debug action should do its job. Otherwise, false. */
    private $httpApiDebugEnabled = false;

    /** @var null|MediaSavingOptions Options that will be used when saving the media files */
    private $mediaSavingOptions = null;

    /** @var string|null File URL given to {@link saveMedia()} method when it is called the last time */
    private $lastFileUrl = null;

    /** @var array|null Response for the request made to {@link lastFileUrl} */
    private $lastResponse = null;

    /**
     * Get the instance
     *
     * @return MediaService
     * @since 1.8.0
     */
    public static function getInstance() {
        if (static::$instance === null) static::$instance = new MediaService();
        return static::$instance;
    }

    /** This is a singleton */
    private function __construct() { }

    /**
     * Inserts the file as media for a given post
     *
     * @param int       $postId    The post ID to which the media will be attached
     * @param MediaFile $mediaFile Media file to be inserted. The media file must have a local path.
     * @return int ID of the inserted media (attachment)
     * @throws Exception If the media file does not have a local path ({@link MediaFile::getLocalPath()}) or the
     *                   attachment cannot be created
     * @since 1.8.0 Uses a MediaFile instance as a parameter instead of $filePath, $title, and $alt parameters.
     */
    public function insertMedia($postId, MediaFile $mediaFile) {
        // Built on the example at: https://codex.wordpress.org/Function_Reference/wp_insert_attachment

        $filePath = $mediaFile->getLocalPath();
        if (!$filePath) {
            throw new Exception('Media file must have a valid local path');
        }

        // Check the type of file. We'll use this as the 'post_mime_type'.
        $fileType = wp_check_filetype(basename($filePath), null);

        // Prepare an array of post data for the attachment.
        $attachment = [
            'guid'              => $mediaFile->getLocalUrl(),
            'post_mime_type'    => $fileType['type'],
            'post_title'        => $mediaFile->getMediaTitle(),
            'post_content'      => $mediaFile->getMediaDescription(),
            'post_excerpt'      => $mediaFile->getMediaCaption(),
            'post_status'       => 'inherit'
        ];

        // Insert the attachment.
        $attachmentId = wp_insert_attachment($attachment, $filePath, $postId);
        if (is_wp_error($attachmentId)) {
            throw new Exception(implode(', ', $attachmentId->get_error_messages()));
        }

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once(trailingslashit(ABSPATH) . Environment::adminDirName() . '/includes/image.php');

        // Generate the metadata for the attachment, and update the database record.
        $attachmentData = wp_generate_attachment_metadata($attachmentId, $filePath);

        if($mediaFile->getMediaAlt()) {
            update_post_meta($attachmentId, '_wp_attachment_image_alt', $mediaFile->getMediaAlt());
        }

        wp_update_attachment_metadata($attachmentId, $attachmentData);
        return $attachmentId;
    }

    /**
     * Saves the given URL in uploads folder and returns full URL of the uploaded file.
     *
     * @param string                  $fileUrl Full URL of the file to be downloaded
     * @param null|MediaSavingOptions $options Options
     * @return array|null An array with keys <b>'url'</b> (full URL for the file), <b>'file'</b> (absolute path of the
     *          file) and <b>'type'</b> (type of the file), or null
     * @since 1.10.2 The method signature is changed. $userAgentString and $timeoutSeconds are removed,
     *        ?MediaSavingOptions $options is added instead.
     */
    public function saveMedia($fileUrl, ?MediaSavingOptions $options = null) {
        // Built on the example at: https://codex.wordpress.org/Function_Reference/wp_handle_sideload
        // Gives us access to the download_url() and wp_handle_sideload() functions
        require_once(trailingslashit(ABSPATH) . Environment::adminDirName() . '/includes/file.php');

        // If the options are not provided, use the defaults.
        if ($options === null) $options = new MediaSavingOptions();
        $this->mediaSavingOptions = $options;

        // Prepare for the HTTP request
        $this->onBeforeSaveMedia($fileUrl);

        // Download file to temp dir. If the timeout is 0 seconds, use WP's default timeout value.
        $timeoutSec = $options->getTimeoutSeconds();
        $tempFile = download_url($fileUrl, $timeoutSec === 0 ? 300 : $timeoutSec);

        if (!is_wp_error($tempFile)) {
            try {
                $infoFinder = new FileInfoFinder($tempFile, $fileUrl, $this->getLastResponse());

            } catch (FileNotFoundException $e) {
                // Execution of this catch statement is highly unlikely.
                Informer::addInfo(sprintf(_wpcc('The local file does not exist. File URL: %1$s'), $fileUrl))
                    ->addAsLog();
                $this->onSaveMediaFinished();
                return null;
            }

            // Find the extension and file name. In case that they are not found, use placeholders.
            $ext = $infoFinder->findExtension();
            if ($ext === null) $ext = 'tmp';

            $fileName = $infoFinder->findFileName();
            if ($fileName === null) $fileName = sha1($fileUrl . uniqid('wpcc'));

            // TODO: Add an option to replace an extension with another. The user must be able to define what extension
            //  should be replaced with what extension. For example, he/she should be able to define a find-replace rule
            //  to replace "aspx" with "jpeg". This replacement can be defined in options box for files. In other words,
            //  we can put this option into the file options box.

            // Array based on $_FILE as seen in PHP file uploads
            $file = [
                'name'      => "{$fileName}.{$ext}", // ex: wp-header-logo.png
                'ext'       => $ext,
                'tmp_name'  => $tempFile,
                'error'     => 0,
                'size'      => @filesize($tempFile),
            ];

            $overrides = [
                // Tells WordPress to not look for the POST form fields that would normally be present, default is true,
                // we downloaded the file from a remote server, so there will be no form fields
                'test_form'     => false,

                // Setting this to false lets WordPress allow empty files, not recommended
                'test_size'     => true,

                // A properly uploaded file will pass this test. There should be no reason to override this one.
                'test_upload'   => true,

                'test_type'     => false,
            ];

            // Move the temporary file into the uploads directory
            $results = wp_handle_sideload($file, $overrides);

            if (empty($results['error'])) {
//                $localUrl   = $results['url'];  // URL to the file in the uploads dir
//                $filePath   = $results['file']; // Full path to the file
//                $type       = $results['type']; // MIME type of the file

                // If this has run when conducting a test, store the file path as the test file path.
                if(WPCCrawler::isDoingGeneralTest()) $this->addTestFilePath($results['file']);

                $this->onSaveMediaFinished();
                return $results;

            } else {
                // Inform the user.
                Informer::add(Information::fromInformationMessage(
                    InformationMessage::FILE_COULD_NOT_BE_SAVED_ERROR,
                    sprintf(_wpcc('%1$s, File URL: %2$s'), $results['error'], $fileUrl),
                    InformationType::INFO
                )->addAsLog());
            }

        } else {
            /** @var WP_Error $tempFile */
            // Inform the user.
            Informer::add(Information::fromInformationMessage(
                InformationMessage::FILE_COULD_NOT_BE_SAVED_ERROR,
                sprintf(_wpcc('%1$s, File URL: %2$s'), implode(' | ', $tempFile->get_error_messages()), $fileUrl),
                InformationType::INFO
            )->addAsLog());
        }

        $this->onSaveMediaFinished();
        return null;
    }

    /**
     * Saves the paths of the test files to a file. This will override the data existing in the current file storing the
     * test file paths.
     *
     * @since 1.8.0
     */
    public function saveTestFilePaths(): void {
        // Write the test file paths into the file
        $paths = implode("\n", $this->getTestFilePaths());
        $file = sprintf('<?' . 'php return "%1$s";', addslashes($paths));
        FileService::getInstance()->getFileSystem()->put($this->getFilePath(), $file);
    }

    /**
     * Deletes previously-saved test files by retrieving their paths from the file saved by {@link saveTestFilePaths()}.
     *
     * @since 1.8.0
     */
    public function deletePreviouslySavedTestFiles(): void {
        $testFilePaths = $this->getPreviousTestFilePaths();
        FileService::getInstance()->getFileSystem()->delete($testFilePaths);
    }

    /*
     * GETTERS AND SETTERS
     */

    /**
     * @return string[]
     */
    public function getTestFilePaths() {
        return $this->testFilePaths;
    }

    /**
     * Add a test file path that should be deleted later.
     *
     * @param string|null $path Path of the test file that will be deleted later.
     * @since 1.8.0
     */
    public function addTestFilePath(?string $path): void {
        /*
         * Do not add the path if
         *  . This is not a test.
         *  . The path is not a valid path.
         *  . The path already exists as a test path.
         *  . The path is not a file.
         */
        if (!WPCCrawler::isDoingGeneralTest() ||
            !$path ||
            in_array($path, $this->testFilePaths) ||
            !Factory::fileSystem()->isFile($path)
        ) {
            return;
        }

        $this->testFilePaths[] = $path;
    }

    /**
     * Remove a test file path.
     *
     * @param string $path
     * @since 1.8.0
     */
    public function removeTestFilePath($path): void {
        /*
         * Do not add the path if
         *  . This is not a test.
         *  . The path is not a valid path.
         */
        if (!WPCCrawler::isDoingGeneralTest() || !$path) return;

        // Find the key of the test file path
        $key = array_search($path, $this->testFilePaths);

        // If it exists, remove the path.
        if ($key !== false) {
            unset($this->testFilePaths[$key]);
        }
    }

    /**
     * Invalidates the state of this instance. The next {@link MediaService::getInstance()} will return a new instance
     * with the default state.
     *
     * @since 1.10.2
     * @internal This is used in unit tests and not intended to be used outside the tests.
     */
    public function invalidateState(): void {
        static::$instance = null;
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * Makes preparations to properly save a media file
     *
     * @param string $fileUrl URL of the file that will be saved
     * @since 1.10.2
     */
    private function onBeforeSaveMedia(string $fileUrl): void {
        $this->lastFileUrl  = $fileUrl;
        $this->lastResponse = null;

        // Register the filters that change the request parameters
        $this->maybeRegisterUserAgentFilter();
        $this->enableUserAgent(true);

        $this->maybeRegisterRequestArgsFilter();
        $this->enableRequestArgs(true);

        $this->maybeRegisterHttpApiDebugAction();
        $this->enableHttpApiDebug(true);
    }

    /**
     * Carries out the operations that should be done when {@link saveMedia()} method is finished execution.
     *
     * @since 1.10.2
     */
    private function onSaveMediaFinished(): void {
        // Disable the user agent and request args
        $this->enableUserAgent(false);
        $this->enableRequestArgs(false);

        // Invalidate the options. The options are good for only one request.
        $this->mediaSavingOptions = null;

        // Invalidate the file URL and its response
        $this->lastFileUrl  = null;
        $this->lastResponse = null;
    }

    /**
     * @return MediaSavingOptions|null
     * @since 1.10.2
     */
    private function getMediaSavingOptions(): ?MediaSavingOptions {
        return $this->mediaSavingOptions;
    }

    /**
     * Registers a filter to WordPress that assigns the user agent string that will be used when downloading files. If
     * the filter was previously registered, this method will do nothing.
     *
     * @since 1.10.2
     */
    private function maybeRegisterUserAgentFilter(): void {
        // If the filter is already registered, stop. Registering it once is enough.
        if ($this->userAgentFilterRegistered) return;

        // This is just a number. Its value does not matter.
        $filterPriority = 87;

        // Changes WP's default user agent
        add_filter('http_headers_useragent', function($defaultValue) {
            // If changing the user agent string is not enabled, return the default value. We can control this behavior
            // by changing the static variable's value.
            if (!$this->userAgentEnabled) return $defaultValue;

            $options = $this->getMediaSavingOptions();
            if (!$options) return $defaultValue;

            // Change the user agent string only if there is a non-null user agent string defined.
            $userAgentString = $options->getUserAgent();
            return $userAgentString !== null
                ? $userAgentString
                : $defaultValue;

        }, $filterPriority);

        // Flag it as registered so that we do not register it more than once.
        $this->userAgentFilterRegistered = true;
    }

    /**
     * @param bool $enabled True if WordPress should use the user agent string from {@link mediaSavingOptions} when
     *                      making requests.
     * @since 1.10.2
     */
    private function enableUserAgent(bool $enabled): void {
        $this->userAgentEnabled = $enabled;
    }

    /**
     * Registers a filter to WordPress to change the request args when needed.
     *
     * @since 1.10.2
     */
    private function maybeRegisterRequestArgsFilter(): void {
        // If registered, stop. No need to register it multiple times.
        if ($this->requestArgsFilterRegistered) return;

        add_filter('http_request_args', function($args) {
            // If custom request arguments should not be used, stop.
            if (!$this->requestArgsEnabled) return $args;

            $options = $this->getMediaSavingOptions();
            if (!$options) return $args;

            // Assign the cookies if they exist.
            $cookies = $options->getCookies();
            if ($cookies) $args['cookies'] = $cookies;

            // Assign the headers if they exist
            $headers = $options->getRequestHeaders();
            if ($headers) $args['headers'] = $headers;

            // Decide if the SSL certificate should be verified
            $args['sslverify'] = $options->isVerifySsl();

            return $args;
        }, 10, 1);

        // Flag it as registered so that we do not register it more than once.
        $this->requestArgsFilterRegistered = true;
    }

    /**
     * @param bool $enabled True if WordPress should use the request arguments from {@link mediaSavingOptions} when
     *                      making requests.
     * @since 1.10.2
     */
    private function enableRequestArgs(bool $enabled): void {
        $this->requestArgsEnabled = $enabled;
    }

    /**
     * Register a callback for 'http_api_debug' action so that we can retrieve the HTTP response retrieved for the
     * request made to save a file
     *
     * @since 1.10.2
     */
    private function maybeRegisterHttpApiDebugAction(): void {
        // If registered, stop. No need to register it multiple times.
        if ($this->httpApiDebugActionRegistered) return;

        add_action('http_api_debug', function($response, $context, $cls, $args, $url) {
            // Stop if it is not enabled.
            if (!$this->httpApiDebugEnabled) return;

            // If this action is not called for the URL we want, stop.
            $expectedUrl = $this->getLastFileUrl();
            if ($expectedUrl === null || $url !== $expectedUrl) return;

            // Store the response
            $this->lastResponse = $response;
        }, 10, 5);

        // Flag it as registered so that we do not register it more than once.
        $this->httpApiDebugActionRegistered = true;
    }

    /**
     * @param bool $enabled See {@link httpApiDebugEnabled}
     * @since 1.10.2
     */
    private function enableHttpApiDebug(bool $enabled): void {
        $this->httpApiDebugEnabled = $enabled;
    }

    /**
     * Get the test file paths that were saved by {@link saveTestFilePaths()}.
     *
     * @return array
     * @since 1.8.0
     */
    private function getPreviousTestFilePaths() {
        // If the file does not exist, return an empty array.
        if(!FileService::getInstance()->getFileSystem()->isFile($this->getFilePath())) return [];

        // Get the new-line separated file paths
        $newLineSeparatedSlashedString = include($this->getFilePath());
        if (!$newLineSeparatedSlashedString) return [];

        // Unslash the string
        $unslashed = stripslashes($newLineSeparatedSlashedString);
        if (!$unslashed) return [];

        // Explode from the new lines to get the file paths as an array
        return explode("\n", $unslashed);
    }

    /**
     * Get the path of the file that stores the test file paths
     *
     * @return string
     * @since 1.8.0
     */
    private function getFilePath() {
        return FileService::getInstance()->getTempDirPath() . DIRECTORY_SEPARATOR . $this->fileBaseName;
    }

    /**
     * @return string|null See {@link lastFileUrl}
     * @since 1.10.2
     */
    private function getLastFileUrl(): ?string {
        return $this->lastFileUrl;
    }

    /**
     * @return array|null See {@link lastResponse}
     * @since 1.10.2
     */
    private function getLastResponse(): ?array {
        return $this->lastResponse;
    }

}