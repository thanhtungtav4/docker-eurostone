<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/03/16
 * Time: 20:27
 */

namespace WPCCrawler;

use Illuminate\Filesystem\Filesystem;
use WPCCrawler\Controllers\DashboardController;
use WPCCrawler\Controllers\FeatureRequestController;
use WPCCrawler\Controllers\GeneralSettingsController;
use WPCCrawler\Controllers\TestController;
use WPCCrawler\Controllers\ToolsController;
use WPCCrawler\Objects\AssetManager\AssetManager;
use WPCCrawler\Objects\Crawling\Savers\PostSaver;
use WPCCrawler\Objects\Crawling\Savers\UrlSaver;
use WPCCrawler\Objects\GlobalShortCodes\GlobalShortCodeService;
use WPCCrawler\Objects\Settings\SettingRegistryService;
use WPCCrawler\Test\Test;
use WPCCrawler\Services\DatabaseService;
use WPCCrawler\Services\PostService;
use WPCCrawler\Services\SchedulingService;

class Factory {

    /** @var Factory|null */
    private static $instance;

    /** @return Factory */
    public static function getInstance(): Factory {
        return static::getClassInstance(Factory::class, static::$instance);
    }

    /*
     *
     */

    /** @var GeneralSettingsController|null */
    private static $generalSettingsController;

    /** @var TestController|null */
    private static $testController;

    /** @var FeatureRequestController|null */
    private static $featureRequestController;

    /** @var Test|null */
    private static $test;

    /** @var PostService|null */
    private static $postService;

    /** @var DatabaseService|null */
    private static $databaseService;

    /** @var SchedulingService|null */
    private static $schedulingService;

    /** @var UrlSaver|null */
    private static $urlSaver;

    /** @var PostSaver|null */
    private static $postSaver;

    /** @var ToolsController|null */
    private static $toolsController;

    /** @var DashboardController|null */
    private static $dashboardController;

    /** @var WPTSLMClient|null */
    private static $wptslmClient;

    /** @var Filesystem|null */
    private static $fs;

    public function __construct() {
        Factory::wptslmClient();

        Factory::dashboardController();
        Factory::testController();
        Factory::toolsController();
        Factory::generalSettingsController();
        // Factory::featureRequestController(); // TODO: This is not functional yet. So, comment it out or make it functional.

        Factory::postService();
        Factory::databaseService();
        Factory::schedulingService();

        // Initialize/register the global short codes
        GlobalShortCodeService::getInstance();
    }

    /** @return GeneralSettingsController */
    public static function generalSettingsController(): GeneralSettingsController {
        return static::getClassInstance(GeneralSettingsController::class, static::$generalSettingsController);
    }

    /** @return TestController */
    public static function testController(): TestController {
        return static::getClassInstance(TestController::class, static::$testController);
    }

    /** @return FeatureRequestController */
    public static function featureRequestController(): FeatureRequestController {
        return static::getClassInstance(FeatureRequestController::class, static::$featureRequestController);
    }

    /** @return Test */
    public static function test(): Test {
        return static::getClassInstance(Test::class, static::$test);
    }

    /** @return PostService */
    public static function postService(): PostService {
        return static::getClassInstance(PostService::class, static::$postService);
    }

    /** @return DatabaseService */
    public static function databaseService(): DatabaseService {
        return static::getClassInstance(DatabaseService::class, static::$databaseService);
    }

    /** @return SchedulingService */
    public static function schedulingService(): SchedulingService {
        return static::getClassInstance(SchedulingService::class, static::$schedulingService);
    }

    /** @return PostSaver */
    public static function postSaver(): PostSaver {
        return static::getClassInstance(PostSaver::class, static::$postSaver);
    }

    /** @return UrlSaver */
    public static function urlSaver(): UrlSaver {
        return static::getClassInstance(UrlSaver::class, static::$urlSaver);
    }

    /** @return ToolsController */
    public static function toolsController(): ToolsController {
        return static::getClassInstance(ToolsController::class, static::$toolsController);
    }

    /** @return DashboardController */
    public static function dashboardController(): DashboardController {
        return static::getClassInstance(DashboardController::class, static::$dashboardController);
    }

    /** @return AssetManager */
    public static function assetManager(): AssetManager {
        return AssetManager::getInstance();
    }

    /**
     * @param bool $fresh True if a fresh instance should be returned.
     * @return WPTSLMClient
     */
    public static function wptslmClient($fresh = false): WPTSLMClient {
        if(!static::$wptslmClient || $fresh) {
            $client = new WPTSLMClient(
                _wpcc('Content Crawler'),
                'wp-content-crawler',
                'plugin',
                'http://wpcontentcrawler.com/api/wptslm/v1',
                Utils::getPluginFilePath(),
                Environment::appDomain()
            );

            $client->setUrlHowToFindLicenseKey('https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-');

            $client->setIsProductPageCallback(function() {
                return Utils::isPluginPage();
            });

            static::$wptslmClient = $client;
        }

        return static::$wptslmClient;
    }

    /** @return Filesystem */
    public static function fileSystem(): Filesystem {
        return static::getClassInstance(Filesystem::class, static::$fs);
    }

    /**
     * @return SettingRegistryService
     * @since 1.9.0
     */
    public static function settingRegistryService(): SettingRegistryService {
        return SettingRegistryService::getInstance();
    }

    /**
     * Create or get instance of a class. A wrapper method to work with singletons. You need to import the class
     * with "use" before calling this method.
     *
     * @param string        $className  Name of the class. E.g. MyClass::class
     * @param mixed         $staticVar  A static variable that will store the instance of the class
     * @return mixed                    A singleton of the class
     */
    private static function getClassInstance($className, &$staticVar) {
        if(!$staticVar) {
            $staticVar = new $className();
//            var_dump("$className instance created.");
        }

        return $staticVar;
    }

}