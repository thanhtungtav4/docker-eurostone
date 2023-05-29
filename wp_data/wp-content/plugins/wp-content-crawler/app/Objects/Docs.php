<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 27/11/2019
 * Time: 22:22
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Filesystem\Filesystem;
use WPCCrawler\Environment;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Utils;

class Docs {

    /** @var Docs */
    private static $instance = null;

    /** @var string */
    private $absoluteLocalIndexFilePath;

    /** @var string|null */
    private $localLabelIndexFileUrl;

    /** @var string */
    private $remoteLabelIndexFileUrl;

    /** @var Filesystem */
    private $fileSystem;

    /** @var string|false Version of the docs. See {@link getVersionForDocs()}. */
    private $version = null;

    /** @var string|false */
    private $docsBaseUrl = null;

    /**
     * @return Docs
     * @since 1.9.0
     */
    public static function getInstance(): Docs {
        if (static::$instance === null) static::$instance = new Docs();
        return static::$instance;
    }

    /**
     * This is a singleton
     *
     * @since 1.9.0
     */
    protected function __construct() {
        $this->absoluteLocalIndexFilePath = Factory::assetManager()
            ->appPath(Environment::relativeStorageDir() . DIRECTORY_SEPARATOR . 'label-index.json');

        $this->localLabelIndexFileUrl = Factory::assetManager()->getPluginFileUrl($this->absoluteLocalIndexFilePath);

        $this->fileSystem = new Filesystem();
    }

    /**
     * Get URL of the documentation for the current version of the plugin
     *
     * @return string|false When successful, the base URL for the documentation for the current version of the plugin,
     *                      without a slash in the end. If documentation URL or the version of the plugin cannot be
     *                      retrieved, returns false.
     * @since 1.9.0
     */
    public function getDocumentationBaseUrl() {
        if ($this->docsBaseUrl === null) {
            $mainUrl = Environment::getDocumentationUrl();
            $version = $this->getVersionForDocs();

            $this->docsBaseUrl = !$version || !$mainUrl ? false : $mainUrl . '/' . $version;
        }

        return $this->docsBaseUrl;
    }

    /**
     * Get URL of the local label index file
     *
     * @return string|null If the local label index file exists, its URL is returned. If it does not exist, null is
     *                     returned.
     * @since 1.9.0
     */
    public function getLocalLabelIndexFileUrl() {
        // Check if local file exists. If not, return null.
        if (!$this->fileSystem->exists($this->absoluteLocalIndexFilePath)) {
            return null;
        }

        return $this->localLabelIndexFileUrl;
    }

    /**
     * Get full URL of the label index file from the documentation for the current version of the plugin.
     *
     * @return string|null Full URL of the label index file of the documentation. The label index file contains the
     *                     labels and their relative URLs. Also see
     *                     {@link Environment::WPCC_DOCUMENTATION_REMOTE_LABEL_INDEX_FILE}. Returns null if plugin version or
     *                     the index file path (defined as environment variable) cannot be retrieved.
     * @since 1.9.0
     */
    public function getRemoteLabelIndexUrl() {
        if ($this->remoteLabelIndexFileUrl) return $this->remoteLabelIndexFileUrl;

        $baseUrl = $this->getDocumentationBaseUrl();
        if (!$baseUrl) return null;

        // Create the label index file's URL
        $this->remoteLabelIndexFileUrl = sprintf('%1$s/%2$s', $baseUrl, Environment::getDocsLabelIndexRemoteFilePath());

        // Append the host as a URL parameter
        $host = parse_url(get_home_url(), PHP_URL_HOST);
        if ($host) {
            $this->remoteLabelIndexFileUrl .= '?' . http_build_query([
                    'host' => $host
                ]);
        }

        return $this->remoteLabelIndexFileUrl;
    }

    /**
     * Download remote label index file and save it locally
     *
     * @param int $timeout Timeout for remote URL request, in seconds. If 0, it means no timeout.
     * @return bool True if the local file is successfully created. Otherwise, false.
     * @since 1.9.0
     */
    public function createLocalLabelIndexFile(int $timeout = 0) {
        // If there is no absolute local file path, return false.
        if (!$this->absoluteLocalIndexFilePath) return false;

        // Get the remote URL. If it does not exist, return false.
        $remoteUrl = $this->getRemoteLabelIndexUrl();
        if (!$remoteUrl) return false;

        // Create the required directories if they do not exist
        $localDirPath = dirname($this->absoluteLocalIndexFilePath);
        if (!$this->fileSystem->exists($localDirPath)) {
            $this->fileSystem->makeDirectory($localDirPath, 0755, true);
        }

        // Save the file existing at the remote URL
        $client = new Client([
            'timeout' => $timeout
        ]);

        try {
            $client->get($remoteUrl, ['sink' => $this->absoluteLocalIndexFilePath]);

        } catch (TransferException $e) {
            // If there was an error, notify the user and return false.
            Informer::addInfo(sprintf(
                _wpcc('Label index could not be retrieved from URL %1$s.') . ' ' . $e->getMessage(),
                $remoteUrl
            ))->addAsLog();

            return false;
        }

        // The remote index file must have been saved to the local path now.
        if (!$this->fileSystem->exists($this->absoluteLocalIndexFilePath)) {
            // If the file does not exist, notify the user.
            Informer::addInfo(_wpcc('Label index could not be created locally.'))
                ->addAsLog();

            return false;
        }

        return true;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Get plugin version for docs
     *
     * @return string|false Get the version of the plugin without patch part. The resultant version contains only 1 dot
     *                      and two numbers around it, e.g. "1.9". If the version could not be retrieved, returns false.
     * @since 1.9.0
     */
    private function getVersionForDocs() {
        if ($this->version === null) {
            // Get the minor version of the plugin since we need it in the URL of the label index file
            $pluginMetaData = get_plugin_data(Environment::pluginFilePath());

            $version = Utils::array_get($pluginMetaData, 'Version');
            if ($version) {
                preg_match('/^[0-9]+\.[0-9]+/', $version, $matches);
                $this->version = !$matches || !$matches[0] ? false : trim($matches[0], '/');

            } else {
                $this->version = false;
            }

        }

        return $this->version;
    }

}