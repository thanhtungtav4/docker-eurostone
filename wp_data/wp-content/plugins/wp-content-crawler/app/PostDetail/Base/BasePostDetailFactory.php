<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/11/2018
 * Time: 08:44
 */

namespace WPCCrawler\PostDetail\Base;


use Exception;
use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Crawling\Data\PostSaverData;
use WPCCrawler\Objects\Settings\SettingsImpl;

abstract class BasePostDetailFactory {

    /** @var BasePostDetailFactory[] */
    private static $factoryInstances = [];

    /** @var string[] Registered factory class names. */
    private static $factoryClasses = [];

    /*
     *
     */

    /** @var null|PostBot The bot that is used to crawl the data from the target site. Contains site settings. */
    private $postBot;

    /** @var null|BasePostDetailData Stores the data needed for this post detail. */
    private $detailData = null;

    /** @var null|BasePostDetailSaver Saves {@link $detailData} */
    private $detailSaver = null;

    /** @var null|BasePostDetailPreparer Prepares {@link $detailData} */
    private $detailPreparer = null;

    /** @var null|BasePostDetailTester This service is used for returning test results when testing via the Tester page */
    private $tester = null;

    /** @var null|BasePostDetailService This service is used for handling some operations that should be done for a post detail. */
    private $service = null;

    /** @var null|BasePostDetailDuplicateChecker Duplicate checker for this post detail. */
    private $duplicateChecker = null;

    /** @var null|BasePostDetailDeleter Deleter for this post detail. */
    private $deleter = null;

    /** @var bool|null True if the detail is available to be used and interacted with. */
    private $availability = null;

    /** @var bool|null True if the detail is available to be used for posts. */
    private $availabilityForPost = null;

    /** @var null|BasePostDetailSettings Used to handle settings-related operations and constants. */
    private $settings = null;

    /**
     * BasePostDetailFactory constructor. Only this class can create a BasePostDetailFactory.
     *
     * @param null|PostBot $postBot
     */
    private function __construct(?PostBot $postBot = null) {
        $this->postBot = $postBot;
    }

    /*
     * FACTORY METHODS
     */

    /**
     * @return string Name of this post detail. For example, "WooCommerce".
     * @since 1.9.0
     */
    public abstract function getName(): string;

    /**
     * @return string An identifier for this post detail. This must not contain any spaces. E.g. "woocommerce". This
     *                value is used, for example, in input names. If this post detail is for a specific post type, the
     *                post type can be used. E.g. "product".
     * @since 1.9.0
     */
    public abstract function getIdentifier(): string;

    /**
     * @return bool True if the detail is available to be shown and interacted with in general. Otherwise, false. For
     *              example, if this requires another plugin to be active, you can check that here.
     */
    protected function getAvailability(): bool {
        return true;
    }

    /**
     * @param SettingsImpl $postSettings
     * @return bool True if this is available for post, e.g., if this should be shown to the user in site settings.
     * @since 1.8.0
     * @noinspection PhpUnusedParameterInspection
     */
    protected function getAvailabilityForPost(SettingsImpl $postSettings): bool {
        return true;
    }

    /**
     * @param SettingsImpl|null $postSettings
     * @return null|BasePostDetailSettings
     * @noinspection PhpUnusedParameterInspection
     */
    protected function createSettings(?SettingsImpl $postSettings): ?BasePostDetailSettings {
        return null;
    }

    /**
     * @return BasePostDetailData
     */
    abstract protected function createData(): BasePostDetailData;

    /**
     * @param PostBot            $postBot
     * @param BasePostDetailData $data
     * @return BasePostDetailPreparer
     */
    abstract protected function createPreparer(PostBot $postBot, BasePostDetailData $data): BasePostDetailPreparer;

    /**
     * @param PostSaverData      $postSaverData
     * @param BasePostDetailData $data
     * @return BasePostDetailSaver|null
     */
    abstract protected function createSaver(PostSaverData $postSaverData, BasePostDetailData $data): ?BasePostDetailSaver;

    /**
     * @param PostBot $postBot
     * @param BasePostDetailData $detailData
     * @return null|BasePostDetailTester
     * @noinspection PhpUnusedParameterInspection
     */
    protected function createTester(PostBot $postBot, BasePostDetailData $detailData): ?BasePostDetailTester {
        return null;
    }

    /**
     * Create a service for this post detail.
     *
     * @return null|BasePostDetailService
     */
    protected function createService(): ?BasePostDetailService {
        return null;
    }

    /**
     * Create a duplicate checker for this post detail.
     *
     * @param PostBot|null            $postBot
     * @param BasePostDetailData|null $detailData
     * @return null|BasePostDetailDuplicateChecker
     * @since 1.8.0
     * @noinspection PhpUnusedParameterInspection
     */
    protected function createDuplicateChecker(?PostBot $postBot, ?BasePostDetailData $detailData): ?BasePostDetailDuplicateChecker {
        return null;
    }

    /**
     * Create a deleter for this post detail.
     *
     * @return null|BasePostDetailDeleter
     * @since 1.8.0
     */
    protected function createDeleter(): ?BasePostDetailDeleter {
        return null;
    }

    /*
     * SETTERS
     */

    /**
     * Set a post bot to this factory.
     *
     * @param null|PostBot $postBot
     */
    public function setPostBot($postBot): void {
        $this->postBot = $postBot;
    }

    /*
     * GETTERS
     */

    /**
     * @return bool True if this detail is available to be shown and interacted with.
     */
    public function isAvailable(): bool {
        if ($this->availability === null) $this->availability = (bool) $this->getAvailability();

        return $this->availability;
    }

    /**
     * @param SettingsImpl $postSettings
     * @return bool True if this detail is available for posts.
     * @since 1.8.0
     */
    public function isAvailableForPost(SettingsImpl $postSettings): bool {
        if ($this->availabilityForPost === null) $this->availabilityForPost = (bool) $this->getAvailabilityForPost($postSettings);

        return $this->availabilityForPost;
    }

    /**
     * @param SettingsImpl|null $postSettings Post settings
     * @param bool              $fresh        True if a new fresh instance must be returned even if there is a cached
     *                                        one.
     * @return null|BasePostDetailSettings Settings of the post detail.
     */
    public function getSettings(?SettingsImpl $postSettings, $fresh = false): ?BasePostDetailSettings {
        if ($this->settings === null || $fresh) {
            $this->settings = $this->createSettings($postSettings);
        }

        return $this->settings;
    }

    /**
     * @return BasePostDetailData
     */
    public function getDetailData(): BasePostDetailData {
        if ($this->detailData === null) {
            $this->detailData = $this->createData();
        }

        return $this->detailData;
    }

    /**
     * @return null|BasePostDetailPreparer
     */
    public function getDetailPreparer(): ?BasePostDetailPreparer {
        if ($this->detailPreparer) {
            return $this->detailPreparer;
        }

        $postBot = $this->getPostBot();
        if ($postBot) {
            $this->detailPreparer = $this->createPreparer($postBot, $this->getDetailData());
        }

        return $this->detailPreparer;
    }

    /**
     * @param PostSaverData $postSaverData
     * @return null|BasePostDetailSaver
     */
    public function getDetailSaver(PostSaverData $postSaverData): ?BasePostDetailSaver {
        if ($this->detailSaver === null) {
            $this->detailSaver = $this->createSaver($postSaverData, $this->getDetailData());
        }

        return $this->detailSaver;
    }

    /**
     * @return null|BasePostDetailTester
     */
    public function getTester(): ?BasePostDetailTester {
        if ($this->tester) {
            return $this->tester;
        }

        $postBot = $this->getPostBot();
        if ($postBot) {
            $this->tester = $this->createTester($postBot, $this->getDetailData());
        }

        return $this->tester;
    }

    /**
     * @return null|BasePostDetailService
     */
    public function getService(): ?BasePostDetailService {
        if ($this->service === null) {
            $this->service = $this->createService();
        }

        return $this->service;
    }

    /**
     * @return null|BasePostDetailDuplicateChecker
     * @since 1.8.0
     */
    public function getDuplicateChecker(): ?BasePostDetailDuplicateChecker {
        if ($this->duplicateChecker === null) {
            $this->duplicateChecker = $this->createDuplicateChecker($this->getPostBot(), $this->getDetailData());
        }

        if ($this->duplicateChecker) {
            // If there is a valid post bot, assign it to the duplicate checker.
            if (!$this->duplicateChecker->getPostBot() && $this->getPostBot()) {
                $this->duplicateChecker->setPostBot($this->getPostBot());
            }

            // If there is a valid post bot, assign it to the duplicate checker.
            if (!$this->duplicateChecker->getDetailData()) {
                $this->duplicateChecker->setDetailData($this->getDetailData());
            }
        }

        return $this->duplicateChecker;
    }

    /**
     * @return null|BasePostDetailDeleter
     * @since 1.8.0
     */
    public function getDeleter(): ?BasePostDetailDeleter {
        if ($this->deleter === null) {
            $this->deleter = $this->createDeleter();
        }

        return $this->deleter;
    }

    /**
     * @return null|PostBot
     */
    public function getPostBot(): ?PostBot {
        return $this->postBot;
    }

    /*
     * STATIC METHODS
     */

    /**
     * Register a post detail factory so that it can be used in the plugin when necessary.
     *
     * @param string|string[] $factoryClass Name(s) of a class that extends {@link BasePostDetailFactory}
     */
    public static function registerFactoryByName($factoryClass): void {
        if (is_array($factoryClass)) {
            static::$factoryClasses = array_unique(array_merge(static::$factoryClasses, $factoryClass));
        } else {
            static::$factoryClasses[] = $factoryClass;
        }
    }

    /**
     * Creates registered factory instances if they were not created and returns them.
     *
     * @param null|PostBot $postBot
     * @return BasePostDetailFactory[]
     */
    public static function getRegisteredFactoryInstances($postBot = null): array {
        /** @var BasePostDetailFactory[] $instances */
        $instances = [];

        // Create or collect the instances of the registered factories
        foreach(static::$factoryClasses as $className) {
            try {
                $instances[] = static::getFactoryInstance($className, $postBot);

            } catch (Exception $e) {
                // Nothing to do here.
            }
        }

        return $instances;
    }

    /**
     * Creates a BasePostDetailFactory with the given class name and $postBot instance. If an instance was created
     * before, returns that instance.
     *
     * @param string  $factoryClass Name of a class that extends {@link BasePostDetailFactory}
     * @param PostBot|null $postBot
     * @return BasePostDetailFactory
     * @throws Exception
     */
    public static function getFactoryInstance($factoryClass, ?PostBot $postBot = null): BasePostDetailFactory {
        // If an instance does not exist
        if (!isset(static::$factoryInstances[$factoryClass])) {
            // Create an instance
            $instance = new $factoryClass($postBot);

            // Make sure the instance is a child of the factory class
            if (!is_a($instance, BasePostDetailFactory::class)) {
                throw new Exception("The factory {$factoryClass} must extend " . BasePostDetailFactory::class);
            }

            // Store the instance
            static::$factoryInstances[$factoryClass] = $instance;
        }

        $instance = static::$factoryInstances[$factoryClass];

        // If the instance does not have a valid post bot and there is a post bot passed as a parameter, then set this
        // post bot to the factory instance.
        if ($postBot && !$instance->getPostBot()) {
            $instance->setPostBot($postBot);
        }

        return $instance;
    }

    /**
     * Invalidates all factory instances.
     *
     * @since 1.8.0
     */
    public static function invalidateInstances(): void {
        // Remove all factories
        foreach(static::$factoryInstances as $factory) unset($factory);

        static::$factoryInstances = [];
    }
}