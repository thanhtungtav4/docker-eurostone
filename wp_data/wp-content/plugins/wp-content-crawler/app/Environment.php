<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/03/16
 * Time: 15:32
 */

namespace WPCCrawler;


use Dotenv\Dotenv;
use WPCCrawler\Objects\AssetManager\BaseAssetManager;

class Environment {

    /** @var string App directory */
    private static $APP_DIR = null;

    /** @var bool True if the environment variables have been initialized. Otherwise, false. */
    private static $isInitialized = false;

    /*
     *
     */

    /** @var string Key for current environment. This can be either 'dev' or 'prod' */
    const ENV                               = "WPCC_ENV";

    /** @var string Key for domain which is used to define WCC-specific things in WordPress */
    const APP_DOMAIN                        = 'WPCC_APP_DOMAIN';

    /** @var string Key for the abbreviation for the plugin */
    const APP_SHORT_NAME                    = 'WPCC_APP_SHORT_NAME';

    /** @var string Key for used to validate nonces */
    const NONCE_NAME                        = 'WPCC_NONCE_NAME';

    /** @var string Key for name of custom post type which is used to store the sites to be crawled */
    const POST_TYPE                         = 'WPCC_POST_TYPE';

    /** @var string Key for name of the main plugin file */
    const PLUGIN_FILE_NAME                  = 'WPCC_PLUGIN_FILE_NAME';

    /** @var string Key for admin directory name */
    const ADMIN_DIR_NAME                    = 'WPCC_ADMIN_DIR_NAME';

    /** @var string Key for ID of the meta box used to show settings in custom post (site) create/edit page */
    const SITE_SETTINGS_META_BOX_ID         = 'WPCC_SITE_SETTINGS_META_BOX_ID';

    /** @var string Key for ID of the meta box used to show notes in custom post (site) create/edit page */
    const SITE_SETTINGS_NOTES_META_BOX_ID   = 'WPCC_SITE_SETTINGS_NOTES_META_BOX_ID';

    /** @var string Key for MySQL date format */
    const MYSQL_DATE_FORMAT                 = 'WPCC_MYSQL_DATE_FORMAT';

    /** @var string Key for directory name of app folder */
    const APP_DIR_NAME                      = 'WPCC_APP_DIR_NAME';

    /** @var string Key for storage directory relative to app dir */
    const RELATIVE_STORAGE_DIR              = 'WPCC_RELATIVE_STORAGE_DIR';

    /** @var string Key for cache directory relative to app dir */
    const RELATIVE_CACHE_DIR                = 'WPCC_RELATIVE_CACHE_DIR';

    /** @var string Key for file cache directory relative to app dir */
    const RELATIVE_RESPONSE_CACHE_DIR       = 'WPCC_RELATIVE_RESPONSE_CACHE_DIR';

    /** @var string Key for views directory relative to app dir */
    const RELATIVE_VIEWS_DIR                = 'WPCC_RELATIVE_VIEWS_DIR';

    /**
     * @var string Key that stores url_hash variable that is used in the views for restoring a page's state, e.g.
     *      reactivating a tab when the page is reloaded after a save operation
     */
    const KEY_URL_HASH                      = 'WPCC_KEY_URL_HASH';

    /**
     * @var string Key for the identifier used for 'post' type. This is used, e.g., when defining translatable fields
     *      in the post settings page.
     */
    const DEFAULT_POST_IDENTIFIER           = 'WPCC_DEFAULT_POST_IDENTIFIER';

    /**
     * @var string Key for the identifier used for default 'category' type. This is used, e.g., when defining
     *      command subjects in the post settings page.
     */
    const DEFAULT_CATEGORY_IDENTIFIER       = 'WPCC_DEFAULT_CATEGORY_IDENTIFIER';

    // CRON validation
    const F_SIZE                            = 'WPCC_F_SIZE';
    const F_HASH                            = 'WPCC_F_HASH';
    const F_CHECK                           = 'WPCC_F_CHECK';

    /**
     * @var string Key for the environment variable storing the required version of PHP for the plugin to run properly
     */
    const REQUIRED_PHP_VERSION              = 'WPCC_REQUIRED_PHP_VERSION';

    /** @var string Key for the environment variable storing whether the plugin should be run as demo or not */
    const DEMO                              = 'WPCC_DEMO';

    /** @var string Key for the environment variable storing the user capability type that can manage the plugin */
    const WPCC_ALLOWED_USER_CAPABILITY      = 'WPCC_ALLOWED_USER_CAPABILITY';

    /**
     * @var string Key for the environment variable storing the URL of the plugin's documentation. The URL does not end
     *      with a trailing slash.
     */
    const WPCC_DOCUMENTATION_URL            = 'WPCC_DOCUMENTATION_URL';

    /**
     * @var string Key for the environment variable storing the relative path to label index file in documentation
     *      site. The value does not start with a forward slash.
     */
    const WPCC_DOCUMENTATION_REMOTE_LABEL_INDEX_FILE = 'WPCC_DOCUMENTATION_REMOTE_LABEL_INDEX_FILE';

    /**
     * Initializes the environment variables.
     * @since 1.9.0
     */
    public static function init(): void {
        if (static::$isInitialized) return;
        static::$isInitialized = true;

        // Load the variables defined in .env file as environment variables.
        $dotEnv = Dotenv::createMutable(__DIR__);
        $dotEnv->load();

        $dotEnv->required(static::ENV)->allowedValues(['dev', 'prod']);

        $dotEnv->required([
            static::APP_DOMAIN,
            static::NONCE_NAME,
            static::POST_TYPE,
            static::PLUGIN_FILE_NAME,
            static::ADMIN_DIR_NAME,
            static::SITE_SETTINGS_META_BOX_ID,
            static::SITE_SETTINGS_NOTES_META_BOX_ID,
            static::MYSQL_DATE_FORMAT,
            static::APP_DIR_NAME,
            static::RELATIVE_STORAGE_DIR,
            static::RELATIVE_CACHE_DIR,
            static::RELATIVE_RESPONSE_CACHE_DIR,
            static::RELATIVE_VIEWS_DIR,
            static::KEY_URL_HASH,
            static::DEFAULT_POST_IDENTIFIER,
            static::DEFAULT_CATEGORY_IDENTIFIER,
            static::F_HASH,
            static::REQUIRED_PHP_VERSION,
            static::DEMO,
            static::WPCC_ALLOWED_USER_CAPABILITY,
            static::WPCC_DOCUMENTATION_URL,
            static::WPCC_DOCUMENTATION_REMOTE_LABEL_INDEX_FILE,
        ]);

        $dotEnv->required(static::F_SIZE)->isInteger();
    }

    /**
     * Get value of an environment variable
     *
     * @param string $varName Name of an environment variable
     * @return mixed
     * @since 1.9.0
     */
    public static function get($varName) {
        // If not initialized, initialize the environment variables.
        static::init();

        // Return the environment variable's value. Some servers do not let server's environment variables to be set.
        // So, if we cannot get the value via getenv(), try $_SERVER and $_ENV variables.
        return getenv($varName) ?: ($_SERVER[$varName] ?? ($_ENV[$varName] ?? ''));
    }

    /**
     * Get the app directory of the plugin relative to WordPress root
     * @return string The relative path without a trailing slash
     * @deprecated This does not take into account the fact that the paths of WordPress directories might be different.
     *             Use {@link BaseAssetManager::appPath()} or its other path methods to get an absolute path of a
     *             plugin file, and {@link BaseAssetManager::getPluginFileUrl()} to get the URL of the absolute path.
     */
    public static function appDir() {
        if(!static::$APP_DIR) {
            static::$APP_DIR = DIRECTORY_SEPARATOR . str_replace(
                str_replace('/', DIRECTORY_SEPARATOR, trailingslashit(ABSPATH)),
                '',
                WP_CONTENT_CRAWLER_PATH
            ) . static::appDirName();
        }

        return static::$APP_DIR;
    }

    /**
     * @return string Absolute path of the plugin's entry point file
     * @since 1.9.0
     */
    public static function pluginFilePath() {
        return WP_CONTENT_CRAWLER_PATH . static::pluginFileName() . '.php';
    }

    /**
     * @return string Absolute path of WP Content Crawler's root directory with a trailing slash at the end.
     * @see WP_CONTENT_CRAWLER_PATH
     * @since 1.11.0
     */
    public static function wpccPath() {
        return WP_CONTENT_CRAWLER_PATH;
    }

    /*
     * Environment variable methods
     */

    /**
     * Check if this is the development environment.
     *
     * @return bool True if this is the development environment.
     * @since 1.8.0
     */
    public static function isDev() {
        return static::get(static::ENV) === 'dev';
    }

    /**
     * @return string See {@link Environment::APP_DOMAIN}
     * @since 1.9.0
     */
    public static function appDomain() {
        return static::get(static::APP_DOMAIN);
    }

    /**
     * @return string See {@link Environment::APP_SHORT_NAME}
     * @since 1.9.0
     */
    public static function appShortName() {
        return static::get(static::APP_SHORT_NAME);
    }

    /**
     * @return string See {@link Environment::NONCE_NAME}
     * @since 1.9.0
     */
    public static function nonceName() {
        return static::get(static::NONCE_NAME);
    }

    /**
     * @return string See {@link Environment::POST_TYPE}
     * @since 1.9.0
     */
    public static function postType() {
        return static::get(static::POST_TYPE);
    }

    /**
     * @return string See {@link Environment::PLUGIN_FILE_NAME}
     * @since 1.9.0
     */
    public static function pluginFileName() {
        return static::get(static::PLUGIN_FILE_NAME);
    }

    /**
     * Get admin directory name.
     * @return string See {@link Environment::ADMIN_DIR_NAME}
     */
    public static function adminDirName() {
        return static::get(static::ADMIN_DIR_NAME);
    }

    /**
     * @return string See {@link Environment::SITE_SETTINGS_META_BOX_ID}
     * @since 1.9.0
     */
    public static function siteSettingsMetaBoxId() {
        return static::get(static::SITE_SETTINGS_META_BOX_ID);
    }

    /**
     * @return string See {@link Environment::SITE_SETTINGS_NOTES_META_BOX_ID}
     * @since 1.9.0
     */
    public static function siteSettingsNotesMetaBoxId() {
        return static::get(static::SITE_SETTINGS_NOTES_META_BOX_ID);
    }

    /**
     * @return string See {@link Environment::MYSQL_DATE_FORMAT}
     * @since 1.9.0
     */
    public static function mysqlDateFormat() {
        return static::get(static::MYSQL_DATE_FORMAT);
    }

    /**
     * @return string See {@link Environment::APP_DIR_NAME}
     * @since 1.9.0
     */
    public static function appDirName() {
        return static::get(static::APP_DIR_NAME);
    }

    /**
     * @return string See {@link Environment::RELATIVE_STORAGE_DIR}
     * @since 1.9.0
     */
    public static function relativeStorageDir() {
        return static::get(static::RELATIVE_STORAGE_DIR);
    }

    /**
     * @return string See {@link Environment::RELATIVE_CACHE_DIR}
     * @since 1.9.0
     */
    public static function relativeCacheDir() {
        return static::get(static::RELATIVE_CACHE_DIR);
    }

    /**
     * @return string See {@link Environment::RELATIVE_RESPONSE_CACHE_DIR}
     * @since 1.9.0
     */
    public static function relativeResponseCacheDir() {
        return static::get(static::RELATIVE_RESPONSE_CACHE_DIR);
    }

    /**
     * @return string See {@link Environment::RELATIVE_VIEWS_DIR}
     * @since 1.9.0
     */
    public static function relativeViewsDir() {
        return static::get(static::RELATIVE_VIEWS_DIR);
    }

    /**
     * @return string See {@link Environment::KEY_URL_HASH}
     * @since 1.9.0
     */
    public static function keyUrlHash() {
        return static::get(static::KEY_URL_HASH);
    }

    /**
     * @return string See {@link Environment::DEFAULT_POST_IDENTIFIER}
     * @since 1.9.0
     */
    public static function defaultPostIdentifier() {
        return static::get(static::DEFAULT_POST_IDENTIFIER);
    }

    /**
     * @return string See {@link Environment::DEFAULT_CATEGORY_IDENTIFIER}
     * @since 1.9.0
     */
    public static function defaultCategoryIdentifier() {
        return static::get(static::DEFAULT_CATEGORY_IDENTIFIER);
    }

    /**
     * @return int
     * @since 1.9.0
     */
    public static function fSize() {
        return (int) static::get(static::F_SIZE);
    }

    /**
     * @return string
     * @since 1.9.0
     */
    public static function fHash() {
        return static::get(static::F_HASH);
    }

    /**
     * @return string
     * @since 1.9.0
     */
    public static function requiredPhpVersion() {
        return static::get(static::REQUIRED_PHP_VERSION);
    }

    /**
     * @return bool
     * @since 1.9.0
     */
    public static function isDemo() {
        return (bool) static::get(static::DEMO);
    }

    /**
     * @return string See {@link Environment::WPCC_ALLOWED_USER_CAPABILITY}
     * @since 1.9.0
     */
    public static function allowedUserCapability() {
        return static::get(static::WPCC_ALLOWED_USER_CAPABILITY);
    }

    /**
     * @return string See {@link Environment::WPCC_DOCUMENTATION_URL}
     * @since 1.9.0
     */
    public static function getDocumentationUrl() {
        return (string) rtrim(static::get(static::WPCC_DOCUMENTATION_URL), '/');
    }

    /**
     * @return string See {@link Environment::WPCC_DOCUMENTATION_REMOTE_LABEL_INDEX_FILE}
     * @since 1.9.0
     */
    public static function getDocsLabelIndexRemoteFilePath() {
        return ltrim(static::get(static::WPCC_DOCUMENTATION_REMOTE_LABEL_INDEX_FILE), '/');
    }

    /**
     * @return string|null If found, the version of WordPress. Otherwise, null.
     * @since 1.12.0
     */
    public static function getWordPressVersion(): ?string {
        global $wp_version;
        return isset($wp_version) && is_string($wp_version)
            ? $wp_version
            : null;
    }
}