<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/12/2018
 * Time: 12:41
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\AssetManager;

use WPCCrawler\Environment;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Informing\Informer;

/**
 * @since 1.8.0
 */
abstract class BaseAssetManager {

    /** @var bool True if script localization was done at least once. If not, false. */
    private $isLocalized = false;

    /** This is a singleton */
    protected function __construct() {}
    final protected function __clone() {}

    /**
     * Get the absolute path of a file in the "app" directory of the plugin.
     *
     * <ul>
     *   <li>The relative path can contain forward slashes or directory separators. They are replaced with the directory
     *     separator.</li>
     *   <li>Repeated forward slashes or directory separators will be reduced to one. The path will be prepared.</li>
     *   <li>The path can start with a trailing forward slash or a directory separator as well. It will be handled.</li>
     *   <li>The path does not need to point to an existing file. This method does not check whether or not the file
     *     exists.
     *   </li>
     * </ul>
     *
     * @param string $relativePath The relative path
     * @return string The absolute path of the given relative path
     * @since 1.10.2
     */
    public function appPath(string $relativePath): string {
        // Create the absolute path candidate. This might contain wrong directory separator or multiple
        // slashes/directory separators. So, this must be tidied up.
        $candidate = WP_CONTENT_CRAWLER_PATH . DIRECTORY_SEPARATOR . Environment::appDirName() . DIRECTORY_SEPARATOR
            . ltrim($relativePath, '/' . DIRECTORY_SEPARATOR);

        // Tidy up the candidate
        // Replace all forward slashes with the directory separator
        $candidate = str_replace('/', DIRECTORY_SEPARATOR, $candidate);

        // Remove repeated directory separators, e.g. change "a////b/c//d///e" to "a/b/c/d/e".
        return preg_replace(
            '/' . preg_quote(DIRECTORY_SEPARATOR, '/') . '+/',
            DIRECTORY_SEPARATOR,
            $candidate
        ) ?: '';
    }

    /**
     * Get the absolute path of a file in the "public" directory of the plugin
     *
     * @param string $relativePath See {@link appPath()}
     * @return string See {@link appPath()}
     * @since 1.10.2
     */
    public function publicPath(string $relativePath): string {
        return $this->appPath('public/' . $relativePath);
    }

    /**
     * Get the absolute path of a file in the "public/dist/css" directory of the plugin
     *
     * @param string $relativePath See {@link publicPath()}
     * @return string See {@link publicPath()}
     * @since 1.10.2
     */
    public function stylePath(string $relativePath): string {
        return $this->publicPath('dist/css/' . $relativePath);
    }

    /**
     * Get the absolute path of a file in the "public/dist/js" directory of the plugin
     *
     * @param string $relativePath See {@link publicPath()}
     * @return string See {@link publicPath()}
     * @since 1.10.2
     */
    public function scriptPath(string $relativePath): string {
        return $this->publicPath('dist/js/' . $relativePath);
    }

    /**
     * Get URL of a file of a plugin
     *
     * @param string|null $absPath Absolute path of a file of a plugin
     * @return string|null URL of the file
     * @since 1.10.2
     */
    public function getPluginFileUrl(?string $absPath): ?string {
        if (!$absPath) return null;

        $fs = Factory::fileSystem();
        if (!$fs->exists($absPath)) return null;

        return plugins_url(
            is_dir($absPath) ? '' : $fs->basename($absPath),
            $absPath
        );
    }

    /**
     * Register and enqueue a style.
     *
     * @param string      $handle  Handle of the style
     * @param string|null $absPath Absolute path of the style file
     * @param array       $deps    An array of dependent styles
     * @param bool|string $ver     Version of the file
     * @param string      $media   See {@link wp_register_style()}
     * @since 1.10.2 Instead of path relative to WordPress root directory, absolute path is used.
     * @see wp_register_style()
     * @see wp_enqueue_style()
     * @see BaseAssetManager::getSourceUrl()
     */
    protected function addStyle(string $handle, ?string $absPath = null, array $deps = [], $ver = false,
                                string $media = 'all'): void {
        // Register it only if it was not registered
        if(!wp_style_is($handle, 'registered')) {
            $url = $this->getSourceUrl($absPath);
            if(!$url) {
                Informer::addError("URL of the style handle '{$handle}' is not found. Path: {$absPath}")->addAsLog();
                return;
            }

            if(!$ver) $ver = $this->getLastModifiedTimeString($absPath);
            wp_register_style($handle, $url, $deps, $ver, $media);
        }

        // Add it only if it was not enqueued
        if(!wp_style_is($handle, 'enqueued')) {
            wp_enqueue_style($handle);
        }
    }

    /**
     * Register, enqueue and/or localize a script. Localization values will only be added once.
     *
     * @param string      $handle  Handle of the script
     * @param string|null $absPath Absolute path of the script file
     * @param array       $deps    An array of dependent styles
     * @param bool|string $ver     Version of the file
     * @param bool        $in_footer
     * @since 1.10.2 Instead of path relative to WordPress root directory, absolute path is used.
     * @see wp_register_script()
     * @see wp_enqueue_script()
     * @see BaseAssetManager::getSourceUrl()
     */
    protected function addScript(string $handle, ?string $absPath = null, array $deps = [], $ver = false,
                                 bool $in_footer = false): void {
        // Register it only if it was not registered
        if(!wp_script_is($handle, 'registered')) {
            $url = $this->getSourceUrl($absPath);
            if(!$url) {
                Informer::addError("URL of the script handle '{$handle}' is not found. Path: {$absPath}")->addAsLog();
                return;
            }

            if(!$ver) $ver = $this->getLastModifiedTimeString($absPath);
            wp_register_script($handle, $url, $deps, $ver, $in_footer);
        }

        // Add it only if it was not enqueued
        if(!wp_script_is($handle, 'enqueued')) {
            wp_enqueue_script($handle);

            // Add script localization if it was not added before. It is enough to do this once. No need to print the
            // same values to the page's source code for each script. Once is enough.
            if(!$this->isLocalized) {
                $localizationName   = $this->getLocalizationName();
                $localizationValues = $this->getLocalizationValues();
                if ($localizationName !== null && $localizationValues) {
                    wp_localize_script($handle, $localizationName, $localizationValues);
                }

                $this->isLocalized = true;
            }
        }
    }

    /**
     * Get last modified time of a file as string
     *
     * @param string|null $absPath See {@link getLastModifiedTime()}
     * @return string|null If {@link getLastModifiedTime()} returns an integer, its string representation. Otherwise,
     *                     null.
     * @uses  getLastModifiedTime()
     * @since 1.11.1
     */
    protected function getLastModifiedTimeString(?string $absPath): ?string {
        $time = $this->getLastModifiedTime($absPath);
        return is_int($time)
            ? (string) $time
            : null;
    }

    /**
     * Get last modified time of an asset.
     *
     * @param string|null $absPath Absolute path of the file
     * @return false|int False if the file is not found, last modified time otherwise.
     */
    protected function getLastModifiedTime(?string $absPath) {
        if ($absPath === null || !file_exists($absPath)) {
            return false;
        }

        return filemtime($absPath);
    }

    /**
     * Returns the localizations for the scripts. For localization values to be added, a valid localization value must
     * be returned from {@link getLocalizationValues()}.
     *
     * @return string|null A string that will be the variable name of the JavaScript localization values. E.g. if this
     *                     is 'wpcc', localization values defined in {@link getLocalizationValues()} will be available
     *                     under 'wpcc' variable in the JS window. So, to define localization values, override
     *                     {@link getLocalizationValues()}.
     * @since 1.8.0
     * @see   wp_localize_script()
     * @see   BaseAssetManager::getLocalizationValues()
     */
    protected function getLocalizationName(): ?string {
        return null;
    }

    /**
     * Get script localization values. For localization values to be added, a valid localization name must be returned
     * from {@link getLocalizationName()}.
     *
     * @return array A key-value pair, where keys are the array keys of localization variable in JS, and the values are
     *               their values. E.g. ['error_occurred' => 'An error occurred'].
     * @see wp_localize_script()
     * @see BaseAssetManager::getLocalizationName()
     */
    protected function getLocalizationValues(): array {
        return [];
    }

    /*
     *
     */

    /**
     * Get the source URL. This method decides what asset should be used. For example, if the development assets are
     * wanted, it changes the given path to its development version, if it exists.
     *
     * If the given path has a version whose name ends with "-dev", that version will be returned. For example,
     * if the path is "/wp-content/plugins/wp-content-crawler/app/public/dist/dev-tools.js", this method will look
     * for "/wp-content/plugins/wp-content-crawler/app/public/dist/dev-tools-dev.js" when the debug mode is enabled. The
     * debug mode is enabled if $_GET has "debug". If "-dev" version of the file is available, this method returns
     * that version's URL.
     *
     * @param string|null $absPath Absolute path of the asset
     * @return string|null If given path is not valid, returns the given value. Otherwise, URL of the asset.
     * @since 1.8.0
     */
    private function getSourceUrl(?string $absPath): ?string {
        // If there is no source, return the given value.
        if (!$absPath) return $absPath;

        // Check if the dev version is required
        $isDev = isset($_GET["debug"]) || Environment::isDev();
        if (!$isDev) return $this->getPluginFileUrl($absPath);

        // Create the development version's path. If it is the same as the given path or it does not exist, return the
        // given path.
        $devAbsPath = preg_replace('/(\.[^.]*)$/', '-dev$1', $absPath, 1);
        if ($devAbsPath === null || $devAbsPath === $absPath || !file_exists($devAbsPath)) {
            return $this->getPluginFileUrl($absPath);
        }

        // Return the development version
        return $this->getPluginFileUrl($devAbsPath);
    }
}