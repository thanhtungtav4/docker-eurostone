<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 04/12/2018
 * Time: 18:29
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\Cache;


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use WPCCrawler\Environment;
use WPCCrawler\Factory;

class ResponseCache {

    /** @var null|ResponseCache */
    private static $instance = null;

    private $fs;

    /** @var string */
    private $cacheDir;

    /**
     * Get the instance
     *
     * @return ResponseCache
     * @since 1.8.0
     */
    public static function getInstance() {
        if (!static::$instance) static::$instance = new ResponseCache();
        return static::$instance;
    }

    /**
     * This is a singleton.
     */
    private function __construct() {
        // Init the file system
        $this->fs = Factory::fileSystem();

        // Create the cache dir path
        $this->cacheDir = Factory::assetManager()->appPath(Environment::relativeResponseCacheDir());
    }

    /**
     * Get response from cache.
     *
     * @param string $method Request method, e.g. "GET", "POST"
     * @param string $url    URL
     * @return null|string
     * @since 1.8.0
     */
    public function get($method, $url): ?string {
        $path = $this->getFilePath($method, $url);

        // If the file is not readable or it does not exist, return null.
        if (!$this->fs->exists($path) || !$this->fs->isReadable($path)) {
            return null;
        }

        // Load the file.
        try {
            $contents = $this->fs->get($path);
        } catch (FileNotFoundException $e) {
            return null;
        }

        // Unserialize the content
        $arr = unserialize($contents);

        // Make sure an array is returned and it has 'content' key.
        if (!is_array($arr) || !isset($arr['content'])) return null;

        // If there is an expiration, check if it has passed.
        if (isset($arr['expire']) && $arr['expire']) {
            $lastModified = $this->fs->lastModified($path);
            $current = time();
            $expire = (int) $arr['expire'] * 60; // Make the expire time in seconds

            // Check if the cache has expired.
            if ($current - ($lastModified + $expire) > 0) return null;
        }

        // Return the content
        return $arr['content'];
    }

    /**
     * Cache the response.
     *
     * @param string $method   Request method, e.g. "GET", "POST"
     * @param string $url      URL
     * @param string $response Response text to be cached
     * @param int    $expire   Expiration duration in minutes
     * @since 1.8.0
     */
    public function save($method, $url, $response, $expire = 10080): void {
        if (!$expire) $expire = 0;

        // Create an array
        $cacheArr = [
            'expire'  => $expire,
            'content' => $response
        ];

        // Serialize the array and store as cache
        $path = $this->getFilePath($method, $url);
        $this->fs->put($path, serialize($cacheArr), false);
    }

    /**
     * Delete response cache.
     *
     * @param string $method Request method, e.g. "GET", "POST"
     * @param string $url    URL
     * @return bool True if deleted.
     * @since 1.8.0
     */
    public function delete($method, $url): bool {
        $path = $this->getFilePath($method, $url);

        // If there is no cache, return true.
        if (!$this->fs->exists($path)) return true;

        return $this->fs->delete($path);
    }

    /**
     * Delete all response cache.
     *
     * @return bool True if deleted.
     * @since 1.8.0
     */
    public function deleteAll(): bool {
        return $this->fs->deleteDirectory($this->cacheDir, true);
    }

    /**
     * Check if cache exists for a given method and URL.
     *
     * @param string $method Request method, e.g. "GET", "POST"
     * @param string $url    URL
     * @return bool
     * @since 1.8.0
     */
    public function has($method, $url): bool {
        $path = $this->getFilePath($method, $url);
        return $this->fs->exists($path);
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Get cache file path for the given method and URL
     *
     * @param string $method
     * @param string $url
     * @return string
     * @since 1.8.0
     */
    private function getFilePath($method, $url): string {
        // Create the directory name
        $dir = $this->cacheDir . DIRECTORY_SEPARATOR . strtolower($method);

        // If the directory does not exist, create non-existent directories.
        if (!$this->fs->isDirectory($dir)) {
            $this->fs->makeDirectory($dir, 0755, true);
        }

        // Return the file path
        return $dir . DIRECTORY_SEPARATOR . $this->getFileName($url);
    }

    /**
     * Get file name for a URL.
     *
     * @param string $url
     * @return string
     * @since 1.8.0
     */
    private function getFileName($url): string {
        return sha1($url) . '.txt';
    }

}