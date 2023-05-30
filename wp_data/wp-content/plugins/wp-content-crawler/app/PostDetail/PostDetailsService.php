<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 30/11/2018
 * Time: 08:33
 */

namespace WPCCrawler\PostDetail;

use Exception;
use WPCCrawler\Exceptions\DuplicatePostException;
use WPCCrawler\Exceptions\StopSavingException;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Crawling\Data\PostData;
use WPCCrawler\Objects\Crawling\Data\PostSaverData;
use WPCCrawler\Objects\Crawling\Preparers\TransformablePreparer;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformationService;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformer;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;
use WPCCrawler\PostDetail\Base\BasePostDetailData;
use WPCCrawler\PostDetail\Base\BasePostDetailDeleter;
use WPCCrawler\PostDetail\Base\BasePostDetailFactory;
use WPCCrawler\PostDetail\Base\BasePostDetailPreparer;
use WPCCrawler\PostDetail\Base\BasePostDetailSaver;
use WPCCrawler\PostDetail\Base\BasePostDetailService;
use WPCCrawler\PostDetail\Base\BasePostDetailSettings;
use WPCCrawler\PostDetail\Base\BasePostDetailTester;
use WPCCrawler\Utils;

/**
 * This class is a mediator between the client and the post detail package.
 *
 * @package WPCCrawler\PostDetail
 * @since   1.8.0
 */
class PostDetailsService {

    /** @var PostDetailsService */
    private static $instance = null;

    /**
     * Get the instance.
     *
     * @return PostDetailsService
     * @since 1.8.0
     */
    public static function getInstance() {
        if (static::$instance === null) static::$instance = new PostDetailsService();
        return static::$instance;
    }

    /** This is a singleton */
    protected function __construct() { }

    /**
     * Registers custom post detail factories
     * @since 1.8.0
     */
    public function registerCustomFactories(): void {
        // Register custom factories.
        /** @var string[] $factoryNames */
        $factoryNames = [];

        /**
         * Register a custom post detail factory.
         *
         * @param string[] $factoryNames Already registered factory names
         * @return string[] An array of names of classes that extend {@link BasePostDetailFactory}. These classes will
         *                  be registered to the plugin.
         * @since 1.8.0
         */
        $factoryNames = apply_filters('wpcc/post/detail/register-factory', $factoryNames);

        // Register custom factories.
        $this->registerFactoryByName($factoryNames);
    }

    /**
     * Register a post detail factory so that it can be used in the plugin when necessary.
     *
     * @param string|string[] $factoryClass Name(s) of a class that extends {@link BasePostDetailFactory}
     * @since 1.8.0
     */
    public function registerFactoryByName($factoryClass): void {
        BasePostDetailFactory::registerFactoryByName($factoryClass);
    }

    /**
     * @param string       $factoryClass See {@link BasePostDetailFactory::getFactoryInstance()}
     * @param PostBot|null $postBot      See {@link BasePostDetailFactory::getFactoryInstance()}
     * @return BasePostDetailFactory|mixed
     * @throws Exception
     * @uses BasePostDetailFactory::getFactoryInstance()
     * @since 1.9.0
     */
    public function getFactoryInstance($factoryClass, $postBot = null) {
        return BasePostDetailFactory::getFactoryInstance($factoryClass, $postBot);
    }

    /**
     * Adds settings meta keys to the given array
     *
     * @param array $allKeys Already-existing settings meta keys.
     * @return array Settings meta keys with the meta keys retrieved from the post details added
     * @since 1.8.0
     */
    public function addAllSettingsMetaKeys($allKeys = []) {
        return $this->addMetaKeys(function($settings) {
            /** @var BasePostDetailSettings $settings */
            return $settings->getAllMetaKeys();
        }, $allKeys);
    }

    /**
     * Adds default values for settings meta keys to the given array
     *
     * @param array $allKeys Already-existing defaults. See {@link PostService::$metaKeyDefaults}
     * @return array The given defaults with defaults retrieved from post details are added
     * @since 1.8.0
     */
    public function addAllSettingsMetaKeyDefaults($allKeys = []) {
        return $this->addMetaKeys(function($settings) {
            /** @var BasePostDetailSettings $settings */
            return $settings->getMetaKeyDefaults();
        }, $allKeys);
    }

    /**
     * Adds single settings meta keys to the given array.
     *
     * @param array $singleKeys Already-existing single settings meta keys.
     * @return array Settings meta keys with the meta keys retrieved from the post details added
     * @since 1.8.0
     */
    public function addAllSingleSettingsMetaKeys($singleKeys = []) {
        return $this->addMetaKeys(function($settings) {
            /** @var BasePostDetailSettings $settings */
            return $settings->getSingleMetaKeys();
        }, $singleKeys);
    }

    /**
     * @param SettingsImpl|array|null $postSettings
     * @return array A key-value pair. The keys are meta keys of the settings. The values are arrays storing the
     *               configuration for the options box for that setting.
     * @since 1.8.0
     */
    public function getOptionsBoxConfigs($postSettings) {
        // Get a valid instance from the given value
        $postSettings = $this->getPostSettingsImplInstance($postSettings);

        $allConfigs = [];
        $this->walkRegisteredFactories(function($factory) use (&$postSettings, &$allConfigs) {
            /** @var BasePostDetailFactory $factory */

            // Check availability for post
            if (!$factory->isAvailableForPost($postSettings)) return;

            // Get the service
            $service = $factory->getService();
            if (!$service || !is_a($service, BasePostDetailService::class)) return;

            // Get the configs
            $configs = $service->getOptionsBoxConfigs();
            if (!$configs) return;

            $allConfigs = array_merge($allConfigs, $configs);
        });

        return $allConfigs ?: [];
    }

    /**
     * Prepares the registered post details.
     *
     * @param null|PostBot $postBot
     * @since 1.8.0
     */
    public function preparePostDetails($postBot): void {
        // Get a valid instance from the given value
        $postSettings = $this->getPostSettingsImplInstanceFromPostBot($postBot);

        // Prepare the registered post details
        $this->walkRegisteredFactories(function($factory) use (&$postSettings) {
            /** @var BasePostDetailFactory $factory */

            // Check availability for post
            if (!$factory->isAvailableForPost($postSettings)) return;

            // Get the preparer
            $preparer = $factory->getDetailPreparer();
            if (!$preparer || !is_a($preparer, BasePostDetailPreparer::class)) return;

            // Prepare
            $preparer->prepare();

        }, $postBot);
    }

    /**
     * Prepare templates of the registered post details.
     *
     * @param PostBot $postBot
     * @param array   $map See {@link ShortCodeReplacer::replaceShortCodes}
     * @param array   $frForMedia Find-replace config that can be used replace media file URLs with local URLs.
     * @since 1.8.0
     */
    public function prepareTemplates($postBot, $map, $frForMedia): void {
        // Get a valid instance from the given value
        $postSettings = $this->getPostSettingsImplInstanceFromPostBot($postBot);

        // Prepare templates defined in Options Boxes of other post details implementations
        $this->walkRegisteredFactories(function($factory) use (&$postSettings, &$map, &$frForMedia) {
            /** @var BasePostDetailFactory $factory */

            // Check availability for post
            if (!$factory->isAvailableForPost($postSettings)) return;

            // Get the service
            $service = $factory->getService();
            if (!$service || !is_a($service, BasePostDetailService::class)) return;

            // Apply short codes
            $service->applyShortCodes($factory->getDetailData(), $map, $frForMedia);
        }, $postBot);
    }

    /**
     * Prepare all interactable fields of the data, which should be an instance of {@link Transformable}, of all post
     * details.
     *
     * @param PostBot  $postBot
     * @param callable $cbPrepare See {@link TransformablePreparer::__construct()}
     * @since 1.9.0
     */
    public function prepareDetailData($postBot, callable $cbPrepare): void {
        // Get a valid instance from the given value
        $postSettings = $this->getPostSettingsImplInstanceFromPostBot($postBot);

        // Prepare templates defined in Options Boxes of other post details implementations
        $this->walkRegisteredFactories(function($factory) use (&$postSettings, &$cbPrepare) {
            /** @var BasePostDetailFactory $factory */

            // Check availability for post
            if (!$factory->isAvailableForPost($postSettings)) return;

            // Get the data
            $data = $factory->getDetailData();
            if (!is_a($data, Transformable::class)) return;

            /** @var Transformable $data */

            // Prepare the data
            $preparer = new TransformablePreparer($data, array_keys($data->getInteractableFields()->toAssociativeArray()), $cbPrepare);
            $preparer->prepare();

        }, $postBot);
    }

    /**
     * Save registered post details.
     *
     * @param PostBot       $postBot
     * @param PostSaverData $saverData
     * @throws DuplicatePostException|StopSavingException
     * @since 1.8.0
     */
    public function save($postBot, $saverData): void {
        // Get a valid instance from the given value
        $postSettings = $this->getPostSettingsImplInstanceFromPostBot($postBot);

        // Get duplicate check options
        $duplicateCheckOptions = null;
        $duplicateCheckSettingValues = $postBot->getSetting(SettingKey::DUPLICATE_CHECK_TYPES);

        // The values are stored under 0 key. So, make sure 0 key exists.
        if($duplicateCheckSettingValues && isset($duplicateCheckSettingValues[0])) {
            $duplicateCheckOptions = $duplicateCheckSettingValues[0];
        }

        /** @var null|DuplicatePostException $duplicateException */
        $duplicateException = null;

        $this->walkRegisteredFactories(function($factory) use (&$duplicateException, &$postSettings, &$saverData, &$duplicateCheckOptions) {
            /** @var BasePostDetailFactory $factory */

            // If there is a duplicate post found, stop.
            if ($duplicateException) return;

            // Check availability for post
            if (!$factory->isAvailableForPost($postSettings)) return;

            // Get the saver
            $saver = $factory->getDetailSaver($saverData);
            if (!$saver || !is_a($saver, BasePostDetailSaver::class)) return;

            try {
                // Save
                $saver->save($factory->getDuplicateChecker(), $duplicateCheckOptions);
            } catch (DuplicatePostException $e) {
                $duplicateException = $e;
            }

        }, $postBot);

        // If there is a duplicate post exception, throw it here.
        if ($duplicateException) throw $duplicateException;
    }

    /**
     * Get settings views of the registered post details, combined.
     *
     * @param SettingsImpl|array|null $postSettings
     * @param array                   $viewVars Variables that will be injected to the settings views of the details
     * @return string Rendered settings views as a single string.
     * @since 1.8.0
     */
    public function getSettingsViews($postSettings, $viewVars = []) {
        // Get a valid instance from the given value
        $postSettings = $this->getPostSettingsImplInstance($postSettings);

        // Add post detail settings if there are any
        $postDetailSettingsViews = '';
        $this->walkRegisteredFactories(function($factory) use (&$postDetailSettingsViews, &$viewVars, &$postSettings) {
            /** @var BasePostDetailFactory $factory */

            // Check availability for post
            if (!$factory->isAvailableForPost($postSettings)) return;

            // Get the detail settings. Get a fresh one because the settings instance was created before in
            // addMetaKeys() method with a null post settings. Here, we provide a non-null post settings. Hence,
            // a new instance of detail settings must be created so that it can use the post settings. If a new instance
            // is not created, there would not be any value in providing a non-null post settings here. So, a fresh one.
            $settings = $factory->getSettings($postSettings, true);
            if (!$settings || !is_a($settings, BasePostDetailSettings::class)) return;

            // Get the settings view
            $detailView = $settings->getSettingsView();
            if (!$detailView) return;

            // Append to other views
            $postDetailSettingsViews .= $detailView->with($viewVars)->render();
        });

        return $postDetailSettingsViews;
    }

    /**
     * Adds site settings assets of each available detail factory
     *
     * @param SettingsImpl|array|null $postSettings
     * @since 1.8.0
     */
    public function addSiteSettingsAssets($postSettings): void {
        // Get a valid instance from the given value
        $postSettings = $this->getPostSettingsImplInstance($postSettings);

        $this->walkRegisteredFactories(function($factory) use (&$postSettings) {
            /** @var BasePostDetailFactory $factory */

            // Check availability
            if (!$factory->isAvailableForPost($postSettings)) return;

            $service = $factory->getService();
            if (!$service || !is_a($service, BasePostDetailService::class)) return;

            $service->addSiteSettingsAssets();
        });
    }

    /**
     * Get test views of the registered post details, combined.
     *
     * @param PostBot                     $postBot
     * @param PostData                    $postData
     * @param array                       $viewVars
     * @return string Rendered test views as a single string.
     * @since 1.8.0
     */
    public function getTestViews(PostBot $postBot, $postData, $viewVars = []) {
        // Get a valid instance from the given value
        $postSettings = $this->getPostSettingsImplInstanceFromPostBot($postBot);

        // Add views defined for the custom post details
        $postDetailViews = '';
        $this->walkRegisteredFactories(function($factory) use (&$postData, &$postSettings, &$viewVars, &$postDetailViews) {
            /** @var BasePostDetailFactory $factory */

            // Check availability
            if (!$factory->isAvailableForPost($postSettings)) return;

            $detailTester = $factory->getTester();
            if (!$detailTester || !is_a($detailTester, BasePostDetailTester::class)) return;

            $detailView = $detailTester->getTesterView();
            if (!$detailView) return;

            // Inject required variables to the view, render, and combine with other views
            $postDetailViews .= $detailView->with($viewVars)->with([
                'detailData' => $factory->getDetailData(),
                'postData'   => $postData,
            ])->render();

        }, $postBot);

        return $postDetailViews;
    }

    /**
     * Adds site tester assets of each available detail factory
     *
     * @since 1.8.0
     */
    public function addSiteTesterAssets(): void {
        $this->walkRegisteredFactories(function($factory) {
            /** @var BasePostDetailFactory $factory */
            $service = $factory->getService();
            if (!$service || !is_a($service, BasePostDetailService::class)) return;

            $service->addSiteTesterAssets();
        });
    }

    /**
     * Get duplicate check options from the factories.
     *
     * @param SettingsImpl $postSettings
     * @return array|null An array having "values" and "defaults" keys, each having an array. If there is no option,
     *                    returns null.
     * @since 1.8.0
     */
    public function getDuplicateOptions(SettingsImpl $postSettings) {
        $allOptions = [
            "values" => [],
            "defaults" => [],
        ];

        $this->walkRegisteredFactories(function($factory) use (&$postSettings, &$allOptions) {
            /** @var BasePostDetailFactory $factory */

            // Check availability
            if (!$factory->isAvailableForPost($postSettings)) return;

            // Get the duplicate checker
            $duplicateChecker = $factory->getDuplicateChecker();
            if (!$duplicateChecker) return;

            // Get the options
            $options = $duplicateChecker->getOptions();
            if (!$options) return;

            // Check for validity:
            //  1. Values must exist
            //  2. If defaults exist, they must have the same number of items as the values
            //  3. If defaults exist, the values and the defaults have to have the same keys.
            if (!isset($options["values"]) ||
                (isset($options["defaults"]) && sizeof($options["values"]) !== sizeof($options["defaults"])) ||
                (isset($options["defaults"]) && array_keys($options["values"]) !== array_keys($options["defaults"]))
            ) {
                return;
            }

            // Get the values
            $values = $options['values'];

            // Get the defaults and prepare if they do not exist
            $defaults = Utils::array_get($options, 'defaults');
            if (!$defaults) {
                $defaults = [];
                foreach($values as $k => $v) {
                    $defaults[$k] = 0;
                }
            }

            // Add the values and defaults to all options
            $allOptions["values"] = array_merge($allOptions["values"], $values);
            $allOptions["defaults"] = array_merge($allOptions["defaults"], $defaults);
        });

        // If the values array is not empty, return the results. Otherwise, return null.
        return !empty($allOptions["values"]) ? $allOptions : null;
    }

    /**
     * Calls the deleters of the registered factories.
     *
     * @since 1.8.0
     * @param SettingsImpl $postSettings
     * @param PostSaverData|null $saverData
     */
    public function delete(SettingsImpl $postSettings, $saverData): void {
        $this->walkRegisteredFactories(function($factory) use (&$postSettings, $saverData) {
            /** @var BasePostDetailFactory $factory */

            // Check availability
            if (!$factory->isAvailableForPost($postSettings)) return;

            // Get the deleter
            $deleter = $factory->getDeleter();
            if (!$deleter || !is_a($deleter, BasePostDetailDeleter::class)) return;

            // Check if the factory has any data
            $detailData = $factory->getDetailData();
            if (!is_a($detailData, BasePostDetailData::class)) return;

            // Delete
            $deleter->delete($postSettings, $detailData, $saverData);

        });
    }

    /**
     * Get category taxonomies defined by the post details
     *
     * @param null|SettingsImpl $postSettings
     * @return array See {@link BasePostDetailService::getCategoryTaxonomies()}
     * @since 1.8.0
     */
    public function getCategoryTaxonomies($postSettings) {
        $allTaxonomies = [];
        $this->walkRegisteredFactories(function($factory) use (&$postSettings, &$allTaxonomies) {
            /** @var BasePostDetailFactory $factory */

            // Check availability
            if ($postSettings && !$factory->isAvailableForPost($postSettings)) return;

            // Get the service
            $service = $factory->getService();
            if (!$service || !is_a($service, BasePostDetailService::class)) return;

            // Get the category taxonomies
            $taxonomies = $service->getCategoryTaxonomies();
            if (!$taxonomies) return;

            // Collect them
            $allTaxonomies = array_merge($allTaxonomies, $taxonomies);
        });

        return $allTaxonomies;
    }

    /**
     * @param PostBot             $postBot
     * @param AbstractTransformer $transformer
     * @since 1.8.0
     */
    public function transform($postBot, $transformer): void {
        // Get a valid instance from the given value
        $postSettings = $this->getPostSettingsImplInstanceFromPostBot($postBot);

        $this->walkRegisteredFactories(function($factory) use (&$postSettings, &$transformer) {
            /** @var BasePostDetailFactory $factory */

            // Check availability
            if (!$factory->isAvailableForPost($postSettings)) return;

            // Get the data
            $data = $factory->getDetailData();
            if (!is_a($data, Transformable::class)) return;

            // Try to transform
            try {
                /** @var Transformable $data */
                $transformer->setTransformable($data, $factory->getIdentifier());

                $transformer->transform();

            } catch (Exception $e) {
                // Inform the user about the error
                Informer::addError(sprintf(
                    _wpcc('Transformation error for %1$s. Message: %2$s - Class: %3$s'),
                    get_class($data),
                    $e->getMessage(),
                    get_class($transformer)
                ))->setException($e)->addAsLog();
            }

        }, $postBot);
    }

    /**
     * Get transformable fields of registered post details
     *
     * @param SettingsImpl|array|null $postSettings
     * @return array A key-value pair where keys are post details' names, and the values are associative arrays. Value
     *               arrays are structured such that the keys are transformable field dot notation keys and the values are
     *               descriptions for them.
     * @since 1.9.0
     */
    public function getTransformableFields($postSettings) {
        // Get a valid instance from the given value
        $postSettings = $this->getPostSettingsImplInstance($postSettings);

        $result = [];

        $this->walkRegisteredFactories(function($factory) use (&$postSettings, &$result) {
            /** @var BasePostDetailFactory $factory */

            // Check availability for post
            if (!$factory->isAvailableForPost($postSettings)) return;

            // Create a data object instance for the factory
            $data = $factory->getDetailData();

            // Make sure it is transformable
            if (!is_a($data, Transformable::class)) return;

            /** @var Transformable $data */
            $transformableFields = $data->getTransformableFields()->toAssociativeArray();
            if (!$transformableFields) return;

            // Get the name and the identifier for this factory
            $name = $factory->getName();
            $identifier = $factory->getIdentifier();
            if (!$name || !$identifier) return;

            // Add the options
            $result[$name] = AbstractTransformationService::prepareTransformableFieldForSelect($transformableFields, $identifier);
        });

        return $result;
    }

    /**
     * Get factories that have {@link Transformable} data
     *
     * @param SettingsImpl|array|null $postSettings
     * @return BasePostDetailFactory[] Array of factories that have {@link Transformable} data
     * @since 1.11.0
     */
    public function getTransformableFactories($postSettings) {
        // Get a valid instance from the given value
        $postSettings = $this->getPostSettingsImplInstance($postSettings);

        $result = [];

        $this->walkRegisteredFactories(function($factory) use (&$postSettings, &$result) {
            /** @var BasePostDetailFactory $factory */

            // Check availability for post
            if (!$factory->isAvailableForPost($postSettings)) return;

            // Create a data object instance for the factory
            $data = $factory->getDetailData();

            // Make sure it is transformable
            if (!is_a($data, Transformable::class)) return;
            /** @var Transformable $data */

            $result[] = $factory;
        });

        return $result;
    }

    /**
     * Invalidate all post detail factory instances
     *
     * @since 1.8.0
     */
    public function invalidateFactoryInstances(): void {
        BasePostDetailFactory::invalidateInstances();
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * @param callable $callbackGetMetaKeys Provide the meta keys to be merged using the detail settings. Returns a
     *                                      string array. For example: function(BasePostDetailSettings $settings) {
     *                                      return $settings->getSingleMetaKeys() }
     * @param array    $metaKeys            Already-existing meta keys to which the meta keys retrieved from the
     *                                      settings will be added.
     * @return array $metaKeys with the meta keys retrieved from the settings added
     * @since 1.8.0
     */
    private function addMetaKeys($callbackGetMetaKeys, $metaKeys = []) {
        $this->walkRegisteredFactories(function($factory) use (&$callbackGetMetaKeys, &$metaKeys) {
            /** @var BasePostDetailFactory $factory */
            $settings = $factory->getSettings(null);
            if (!$settings || !is_a($settings, BasePostDetailSettings::class)) return;

            $detailMetaKeys = $callbackGetMetaKeys($settings);
            if (!$detailMetaKeys) return;

            $metaKeys = array_merge($metaKeys, $detailMetaKeys);
        });

        return $metaKeys;
    }

    /**
     * Walks registered and available factories and calls the given callback.
     *
     * @param callable|null $callback A callback that will be called for each registered detail factory if it is available.
     *                                It takes only one parameter $factory, which is a BasePostDetailFactory, and returns
     *                                nothing. E.g. function($factory) {}
     * @param null|PostBot $postBot See {@link BasePostDetailFactory::getRegisteredFactoryInstances()}
     * @since 1.8.0
     */
    private function walkRegisteredFactories($callback, $postBot = null): void {
        if (!$callback) return;

        foreach(BasePostDetailFactory::getRegisteredFactoryInstances($postBot) as $factory) {
            if (!$factory->isAvailable()) continue;

            $callback($factory);
        }
    }

    /**
     * Get a post settings instance using a post bot
     *
     * @param PostBot|null $postBot
     * @since 1.8.0
     * @return SettingsImpl
     */
    private function getPostSettingsImplInstanceFromPostBot($postBot): SettingsImpl {
        if ($postBot) return $postBot->getSettingsImpl();

        return $this->getPostSettingsImplInstance(null);
    }

    /**
     * @param SettingsImpl|array|null $postSettings
     * @param bool                    $prepare True if the settings should be prepared. Otherwise, false.
     * @return SettingsImpl
     * @since 1.8.0
     */
    private function getPostSettingsImplInstance($postSettings, $prepare = true): SettingsImpl {
        // If this is an instance, use it directly.
        if (is_object($postSettings) && is_a($postSettings, SettingsImpl::class)) {
            return $postSettings;
        }

        // Otherwise, make sure it is an array.
        if (!$postSettings || !is_array($postSettings)) {
            $postSettings = [];
        }

        // Create an instance
        return new SettingsImpl($postSettings, Factory::postService()->getSingleMetaKeys(), $prepare);
    }

}
