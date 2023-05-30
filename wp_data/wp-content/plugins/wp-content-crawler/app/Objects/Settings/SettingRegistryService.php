<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 16/04/2019
 * Time: 18:43
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Settings;


use WPCCrawler\Objects\Enums\TabKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Enums\ValueType;

/**
 * This class is used to reach data about a setting and to reach the setting keys of general and site settings. The
 * settings in the registry are {@link SettingData} instances. This class also provides a few convenience methods to
 * reach the details about a setting.
 *
 * This class creates the registries. A registry is actually an array of {SettingData} instances. {@link SettingData}
 * instances are created in this class. For more information about {SettingData}, see the phpdoc of the class.
 *
 * @package WPCCrawler\Objects\Settings
 * @since   1.8.1
 */
class SettingRegistryService {

    // TODO: Write the tests for this class. Make sure you test default values.

    /** @var SettingRegistryService */
    private static $instance;

    /** @var SettingRegistry */
    private $registryGeneralSettings;

    /** @var SettingRegistry */
    private $registrySiteSettings;
    
    /** @var SettingRegistry */
    private $registryCronSettings;

    /** @var SettingRegistry[] */
    private $allRegistries;

    /** @var string[]|null Stores all keys, existing in all registries, storing a non-array value. */
    private $singleKeys = null;

    /**
     * @var array|null Structured as [{@link TabKey} => {@link SettingKey}[]] Stores which settings exist under which
     *      tab. Structured as key-value pairs where keys are one of the constants defined in {@link TabKey} and the
     *      values are arrays of setting keys, which are constants defined in {@link SettingKey}.
     */
    private $tabKeyMap = null;

    /**
     * Get the instance
     *
     * @return SettingRegistryService
     * @since 1.9.0
     */
    public static function getInstance() {
        if (static::$instance === null) static::$instance = new SettingRegistryService();
        return static::$instance;
    }

    /**
     * This class is a singleton. Use {@link getInstance()} to get the instance.
     *
     * @since 1.9.0
     */
    private function __construct() {
        // Create the registries
        $this->registrySiteSettings     = $this->createSiteSettingsRegistry();
        $this->registryGeneralSettings  = $this->createGeneralSettingsRegistry();
        $this->registryCronSettings     = $this->createCronSettingsRegistry();

        // Store all registries in an array
        $this->allRegistries = [
            $this->registrySiteSettings,
            $this->registryGeneralSettings,
            $this->registryCronSettings,
        ];
    }

    /*
     * PUBLIC METHODS
     */

    /**
     * Get data of a setting by using the setting's key.
     *
     * @param string|null $key One of the constants defined in {@link SettingKey} or a setting key.
     * @return SettingData|null If exists, {@link SettingData}. Otherwise, null.
     * @since 1.9.0
     */
    public function getSettingData(?string $key): ?SettingData {
        foreach($this->allRegistries as $registry) {
            $settingData = $registry->getSettingData($key);

            if ($settingData) {
                return $settingData;
            }
        }

        return null;
    }

    /**
     * Get the keys of the settings storing only non-array values in all registries.
     *
     * @return string[] An array of {@link SettingKey} constants, storing only the keys having a single value, i.e. not
     *                  an array value.
     * @since 1.9.0
     */
    public function getSingleKeys(): array {
        if ($this->singleKeys === null) {

            $this->singleKeys = [];
            foreach($this->allRegistries as $registry) {
                $this->singleKeys = array_merge($this->singleKeys, $registry->getSingleKeys());
            }
        }

        return $this->singleKeys;
    }

    /**
     * @return array See {@link tabKeyMap}
     * @since 1.10.0
     */
    public function getTabKeyMap(): array {
        if ($this->tabKeyMap === null) {
            $this->tabKeyMap = [];
            $this->walkSettingData(function ($data) {
                /** @var SettingData $data */
                $tabKey = $data->getTabKey();
                if (!$tabKey) return;

                if (!isset($this->tabKeyMap[$tabKey])) {
                    $this->tabKeyMap[$tabKey] = [];
                }

                $this->tabKeyMap[$tabKey][] = $data->getKey();
            });
        }

        return $this->tabKeyMap ?: [];
    }

    /*
     * GETTERS
     */

    /**
     * @return SettingRegistry
     * @since 1.9.0
     */
    public function getRegistrySiteSettings(): SettingRegistry {
        return $this->registrySiteSettings;
    }

    /**
     * @return SettingRegistry
     * @since 1.9.0
     */
    public function getRegistryGeneralSettings(): SettingRegistry {
        return $this->registryGeneralSettings;
    }

    /**
     * @return SettingRegistry
     * @since 1.9.0
     */
    public function getRegistryCronSettings(): SettingRegistry {
        return $this->registryCronSettings;
    }

    /*
     * PROTECTED HELPERS
     */

    /**
     * Walk registered setting data
     *
     * @param callable|null $callback A function that takes only one parameter, SettingData instance. E.g.
     *                                func(SettingData $data) { ... }
     * @since 1.10.0
     */
    protected function walkSettingData($callback): void {
        if (!$callback) return;

        foreach($this->allRegistries as $registry) {
            foreach($registry->getAllSettingData() as $data) {
                $callback($data);
            }
        }
    }

    /*
     * PRIVATE REGISTRY-CREATING METHODS
     */

    /**
     * Creates site settings registry
     *
     * @return SettingRegistry
     * @since 1.9.0
     */
    private function createSiteSettingsRegistry(): SettingRegistry {
        // TODO: Test the default values. The defaults are set when a new site is created. Use this knowledge and test
        //  the defaults.
        return new SettingRegistry([
            // Main tab
            (new SettingData(SettingKey::ACTIVE,                         ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::ACTIVE_RECRAWLING,              ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::ACTIVE_POST_DELETING,           ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::ACTIVE_TRANSLATION,             ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::TRANSLATABLE_FIELDS,            ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::ACTIVE_SPINNING,                ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::SPINNABLE_FIELDS,               ValueType::T_ARRAY, ['post.template']))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::MAIN_PAGE_URL,                  ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::DUPLICATE_CHECK_TYPES,          ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::DO_NOT_USE_GENERAL_SETTINGS,    ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::COOKIES,                        ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::REQUEST_HEADERS,                ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::CACHE_TEST_URL_RESPONSES,       ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::FIX_TABS,                       ValueType::T_BOOLEAN, ['on']))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),
            (new SettingData(SettingKey::FIX_CONTENT_NAVIGATION,         ValueType::T_BOOLEAN, ['on']))->setTabKey(TabKey::SITE_SETTINGS_TAB_MAIN),

            // Category tab
            (new SettingData(SettingKey::CATEGORY_ADD_CATEGORY_URLS_WITH_SELECTOR,   ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_LIST_PAGE_URL,                     ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_LIST_URL_SELECTORS,                ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_POST_LINK_SELECTORS,               ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_COLLECT_IN_REVERSE_ORDER,          ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_UNNECESSARY_ELEMENT_SELECTORS,     ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_POST_SAVE_THUMBNAILS,              ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_POST_THUMBNAIL_SELECTORS,          ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::TEST_FIND_REPLACE_THUMBNAIL_URL_CAT,        ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_FIND_REPLACE_THUMBNAIL_URL,        ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_POST_IS_LINK_BEFORE_THUMBNAIL,     ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_NEXT_PAGE_SELECTORS,               ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_MAP,                               ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::TEST_FIND_REPLACE_FIRST_LOAD_CAT,           ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_FIND_REPLACE_RAW_HTML,             ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_FIND_REPLACE_FIRST_LOAD,           ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_FIND_REPLACE_ELEMENT_ATTRIBUTES,   ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_EXCHANGE_ELEMENT_ATTRIBUTES,       ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_REMOVE_ELEMENT_ATTRIBUTES,         ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_FIND_REPLACE_ELEMENT_HTML,         ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::TEST_URL_CATEGORY,                          ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_NOTIFY_EMPTY_VALUE_SELECTORS,      ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_REQUEST_FILTERS,                   ValueType::T_JSON))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_PAGE_FILTERS,                      ValueType::T_JSON))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),
            (new SettingData(SettingKey::CATEGORY_DATA_FILTERS,                      ValueType::T_JSON))->setTabKey(TabKey::SITE_SETTINGS_TAB_CATEGORY),

            // Post tab
            (new SettingData(SettingKey::TEST_URL_POST,          ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_TITLE_SELECTORS,   ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_EXCERPT_SELECTORS, ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_CONTENT_SELECTORS, ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),

            (new SettingData(SettingKey::POST_CATEGORY_NAME_SELECTORS,               ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_CATEGORY_ADD_ALL_FOUND_CATEGORY_NAMES, ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_CATEGORY_NAME_SEPARATORS,              ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_CATEGORY_ADD_HIERARCHICAL,             ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_CATEGORY_DO_NOT_ADD_CATEGORY_IN_MAP,   ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),

            (new SettingData(SettingKey::POST_DATE_SELECTORS,                        ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::TEST_FIND_REPLACE_DATE,                     ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_FIND_REPLACE_DATE,                     ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_DATE_ADD_MINUTES,                      ValueType::T_INTEGER))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_CUSTOM_CONTENT_SHORTCODE_SELECTORS,    ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_TAG_SELECTORS,                         ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_SLUG_SELECTORS,                        ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_PAGINATE,                              ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_NEXT_PAGE_URL_SELECTORS,               ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_NEXT_PAGE_ALL_PAGES_URL_SELECTORS,     ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_SAVE_AS_SINGLE_PAGE,                   ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_IS_LIST_TYPE,                          ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_LIST_ITEM_STARTS_AFTER_SELECTORS,      ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_LIST_TITLE_SELECTORS,                  ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_LIST_CONTENT_SELECTORS,                ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_LIST_ITEM_NUMBER_SELECTORS,            ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_LIST_ITEM_AUTO_NUMBER,                 ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_LIST_INSERT_REVERSED,                  ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_META_KEYWORDS,                         ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_META_KEYWORDS_AS_TAGS,                 ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_META_DESCRIPTION,                      ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_UNNECESSARY_ELEMENT_SELECTORS,         ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_SAVE_ALL_IMAGES_IN_CONTENT,            ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_SAVE_IMAGES_AS_MEDIA,                  ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_SAVE_IMAGES_AS_GALLERY,                ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_GALLERY_IMAGE_SELECTORS,               ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_SAVE_IMAGES_AS_WOOCOMMERCE_GALLERY,    ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_IMAGE_SELECTORS,                       ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::TEST_FIND_REPLACE_IMAGE_URLS,               ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_FIND_REPLACE_IMAGE_URLS,               ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_SAVE_THUMBNAILS_IF_NOT_EXIST,          ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_THUMBNAIL_SELECTORS,                   ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::TEST_FIND_REPLACE_THUMBNAIL_URL,            ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_FIND_REPLACE_THUMBNAIL_URL,            ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_CUSTOM_META_SELECTORS,                 ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_CUSTOM_META,                           ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_CUSTOM_TAXONOMY_SELECTORS,             ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_CUSTOM_TAXONOMY,                       ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_NOTIFY_EMPTY_VALUE_SELECTORS,          ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_TRIGGER_SAVE_POST_HOOK,                ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_REQUEST_FILTERS,                       ValueType::T_JSON))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::POST_PAGE_FILTERS,                          ValueType::T_JSON))->setTabKey(TabKey::SITE_SETTINGS_TAB_POST),

            // Templates tab
            (new SettingData(SettingKey::POST_TEMPLATE_MAIN,                     ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_TEMPLATE_TITLE,                    ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_TEMPLATE_EXCERPT,                  ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_TEMPLATE_LIST_ITEM,                ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_TEMPLATE_GALLERY_ITEM,             ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_REMOVE_LINKS_FROM_SHORT_CODES,     ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_CONVERT_IFRAMES_TO_SHORT_CODE,     ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_CONVERT_SCRIPTS_TO_SHORT_CODE,     ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_REMOVE_EMPTY_HTML_TAGS,            ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_REMOVE_SCRIPTS,                    ValueType::T_BOOLEAN))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::TEST_FIND_REPLACE,                      ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_TEMPLATE,             ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_TITLE,                ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_EXCERPT,              ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_TAGS,                 ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_META_KEYWORDS,        ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_META_DESCRIPTION,     ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_CUSTOM_SHORTCODES,    ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::TEST_FIND_REPLACE_FIRST_LOAD,           ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_RAW_HTML,             ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_FIRST_LOAD,           ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_ELEMENT_ATTRIBUTES,   ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_EXCHANGE_ELEMENT_ATTRIBUTES,       ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_REMOVE_ELEMENT_ATTRIBUTES,         ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_ELEMENT_HTML,         ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_CUSTOM_META,          ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::POST_FIND_REPLACE_CUSTOM_SHORT_CODE,    ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),
            (new SettingData(SettingKey::TEMPLATE_UNNECESSARY_ELEMENT_SELECTORS, ValueType::T_ARRAY))->setTabKey(TabKey::SITE_SETTINGS_TAB_TEMPLATES),

            // Filters tab
            (new SettingData(SettingKey::POST_DATA_FILTERS, ValueType::T_JSON))->setTabKey(TabKey::SITE_SETTINGS_TAB_FILTERS),

            // Notes tab
            (new SettingData(SettingKey::NOTES, ValueType::T_STRING))->setTabKey(TabKey::SITE_SETTINGS_TAB_NOTES),

            new SettingData(SettingKey::NOTES_SIMPLE, ValueType::T_STRING),

            // Others
            new SettingData(SettingKey::DEV_TOOLS_STATE, ValueType::T_STRING),
        ]);
    }

    /**
     * Creates general settings registry
     *
     * @return SettingRegistry
     * @since 1.9.0
     */
    private function createGeneralSettingsRegistry(): SettingRegistry {
        // TODO: Test default values. The defaults are assigned when the plugin is installed for the first time. So,
        //  use this knowledge to test the default values. Also, assign default values for the ones that do not have a
        //  default value, in case it is necessary to assign a default value.
        return new SettingRegistry([
            // Scheduling
            (new SettingData(SettingKey::WPCC_IS_SCHEDULING_ACTIVE,          ValueType::T_BOOLEAN, ''))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_NO_NEW_URL_PAGE_TRIAL_LIMIT,   ValueType::T_INTEGER, 4))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_MAX_PAGE_COUNT_PER_CATEGORY,   ValueType::T_INTEGER, 0))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_INTERVAL_URL_COLLECTION,       ValueType::T_STRING, '_wpcc_10_minutes'))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_INTERVAL_POST_CRAWL,           ValueType::T_STRING, '_wpcc_2_minutes'))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),

            (new SettingData(SettingKey::WPCC_IS_RECRAWLING_ACTIVE,                  ValueType::T_BOOLEAN, ''))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_INTERVAL_POST_RECRAWL,                 ValueType::T_STRING, '_wpcc_2_minutes'))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_RUN_COUNT_URL_COLLECTION,              ValueType::T_INTEGER, 1))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_RUN_COUNT_POST_CRAWL,                  ValueType::T_INTEGER, 1))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_RUN_COUNT_POST_RECRAWL,                ValueType::T_INTEGER, 1))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_MAX_RECRAWL_COUNT,                     ValueType::T_INTEGER, 0))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_MIN_TIME_BETWEEN_TWO_RECRAWLS_IN_MIN,  ValueType::T_INTEGER, 1440))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING), // 1 day
            (new SettingData(SettingKey::WPCC_RECRAWL_POSTS_NEWER_THAN_IN_MIN,       ValueType::T_INTEGER, 43200))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING), // 1 month

            (new SettingData(SettingKey::WPCC_IS_DELETING_POSTS_ACTIVE,              ValueType::T_BOOLEAN, ''))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_INTERVAL_POST_DELETE,                  ValueType::T_STRING, '_wpcc_2_hours'))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_MAX_POST_COUNT_PER_POST_DELETE_EVENT,  ValueType::T_INTEGER, 30))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),
            (new SettingData(SettingKey::WPCC_DELETE_POSTS_OLDER_THAN_IN_MIN,        ValueType::T_INTEGER, 43200))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING), // 1 month
            (new SettingData(SettingKey::WPCC_IS_DELETE_POST_ATTACHMENTS,            ValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SCHEDULING),

            // Post
            (new SettingData(SettingKey::WPCC_ALLOW_COMMENTS,            ValueType::T_BOOLEAN, true))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::WPCC_POST_STATUS,               ValueType::T_STRING, 'publish'))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::WPCC_POST_TYPE,                 ValueType::T_STRING, 'post'))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::WPCC_POST_CATEGORY_TAXONOMIES,  ValueType::T_ARRAY))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::WPCC_POST_AUTHOR,               ValueType::T_INTEGER, ''))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::WPCC_POST_TAG_LIMIT,            ValueType::T_INTEGER, 0))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::WPCC_CHANGE_PASSWORD,           ValueType::T_BOOLEAN, false))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::WPCC_POST_PASSWORD,             ValueType::T_STRING, ''))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::WPCC_POST_SET_SRCSET,           ValueType::T_BOOLEAN, false))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::WPCC_PROTECTED_ATTACHMENTS,     ValueType::T_ARRAY))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_POST),

            (new SettingData(SettingKey::WPCC_ALLOWED_IFRAME_SHORT_CODE_DOMAINS, ValueType::T_ARRAY, ''))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_POST),
            (new SettingData(SettingKey::WPCC_ALLOWED_SCRIPT_SHORT_CODE_DOMAINS, ValueType::T_ARRAY, ''))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_POST),

            // Translation
            (new SettingData(SettingKey::WPCC_IS_TRANSLATION_ACTIVE,         ValueType::T_BOOLEAN, false))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_SELECTED_TRANSLATION_SERVICE,  ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),

            (new SettingData(SettingKey::WPCC_TRANSLATION_GOOGLE_TRANSLATE_FROM,         ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_GOOGLE_TRANSLATE_TO,           ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_GOOGLE_TRANSLATE_PROJECT_ID,   ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_GOOGLE_TRANSLATE_API_KEY,      ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_GOOGLE_TRANSLATE_TEST,         ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),

            (new SettingData(SettingKey::WPCC_TRANSLATION_MICROSOFT_TRANSLATOR_TEXT_FROM,            ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_MICROSOFT_TRANSLATOR_TEXT_TO,              ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_MICROSOFT_TRANSLATOR_TEXT_CLIENT_SECRET,   ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_MICROSOFT_TRANSLATOR_TEXT_TEST,            ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),

            (new SettingData(SettingKey::WPCC_TRANSLATION_YANDEX_TRANSLATE_FROM,     ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_YANDEX_TRANSLATE_TO,       ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_YANDEX_TRANSLATE_API_KEY,  ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_YANDEX_TRANSLATE_TEST,     ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),

            (new SettingData(SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_FROM,         ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_TO,           ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_ACCESS_KEY,   ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_SECRET,       ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_REGION,       ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),
            (new SettingData(SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_TEST,         ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_TRANSLATION),

            // Spinning
            (new SettingData(SettingKey::WPCC_IS_SPINNING_ACTIVE,            ValueType::T_BOOLEAN, false))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SELECTED_SPINNING_SERVICE,     ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SEND_IN_ONE_REQUEST,  ValueType::T_BOOLEAN, false))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_PROTECTED_TERMS,      ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),

            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_EMAIL,                         SettingValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_API_KEY,                       SettingValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_APP_ID,                        SettingValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_QUALITY,                       SettingValueType::T_INTEGER))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_PHRASE_QUALITY,                SettingValueType::T_INTEGER))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_POS_MATCH,                     SettingValueType::T_INTEGER))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_DO_NOT_REWRITE,                SettingValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_LANGUAGE,                      SettingValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_SENTENCE_REWRITE,              SettingValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_GRAMMAR_CHECK,                 SettingValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_REORDER_PARAGRAPHS,            SettingValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_REPLACE_PHRASES_WITH_PHRASES,  SettingValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_SPIN_WITHIN_SPIN,              SettingValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_SPIN_TIDY,                     SettingValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_EXCLUDE_ORIGINAL,              SettingValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_REPLACE_FREQUENCY,             SettingValueType::T_INTEGER))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_MAX_SYNS,                      SettingValueType::T_INTEGER))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_INSTANT_UNIQUE,                SettingValueType::T_INTEGER))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_MAX_SPIN_DEPTH,                SettingValueType::T_INTEGER))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            //            (new SettingData(SettingKey::WPCC_SPINNING_CHIMP_REWRITER_TEST,                          SettingValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),

            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_EMAIL,                  ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_API_KEY,                ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_CONFIDENCE_LEVEL,       ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_AUTO_PROTECTED_TERMS,   ValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_NESTED_SPINTAX,         ValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_AUTO_SENTENCES,         ValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_AUTO_PARAGRAPHS,        ValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_AUTO_NEW_PARAGRAPHS,    ValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_AUTO_SENTENCE_TREES,    ValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_USE_ONLY_SYNONYMS,      ValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_REORDER_PARAGRAPHS,     ValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_TEXT_WITH_SPINTAX,      ValueType::T_BOOLEAN))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_SPIN_REWRITER_TEST,                   ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),

            (new SettingData(SettingKey::WPCC_SPINNING_TURKCE_SPIN_API_TOKEN,                ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),
            (new SettingData(SettingKey::WPCC_SPINNING_TURKCE_SPIN_TEST,                     ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SPINNING),

            // SEO
            (new SettingData(SettingKey::WPCC_META_KEYWORDS_META_KEY,    ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SEO),
            (new SettingData(SettingKey::WPCC_META_DESCRIPTION_META_KEY, ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SEO),
            (new SettingData(SettingKey::WPCC_TEST_FIND_REPLACE,         ValueType::T_STRING))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SEO),
            (new SettingData(SettingKey::WPCC_FIND_REPLACE,              ValueType::T_ARRAY))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_SEO),

            // Notifications
            (new SettingData(SettingKey::WPCC_IS_NOTIFICATION_ACTIVE,                ValueType::T_BOOLEAN, false))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_NOTIFICATIONS),
            (new SettingData(SettingKey::WPCC_NOTIFICATION_EMAIL_INTERVAL_FOR_SITE,  ValueType::T_INTEGER, 30))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_NOTIFICATIONS),
            (new SettingData(SettingKey::WPCC_NOTIFICATION_EMAILS,                   ValueType::T_ARRAY))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_NOTIFICATIONS),

            // Advanced
            (new SettingData(SettingKey::WPCC_MAKE_SURE_ENCODING_UTF8,   ValueType::T_BOOLEAN, true))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),
            (new SettingData(SettingKey::WPCC_CONVERT_CHARSET_TO_UTF8,   ValueType::T_BOOLEAN, false))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),
            (new SettingData(SettingKey::WPCC_HTTP_USER_AGENT,           ValueType::T_STRING, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36'))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),
            (new SettingData(SettingKey::WPCC_HTTP_ACCEPT,               ValueType::T_STRING, "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8"))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),
            (new SettingData(SettingKey::WPCC_HTTP_ALLOW_COOKIES,        ValueType::T_BOOLEAN, true))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),
            (new SettingData(SettingKey::WPCC_DISABLE_SSL_VERIFICATION,  ValueType::T_BOOLEAN, false))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),
            (new SettingData(SettingKey::WPCC_USE_PROXY,                 ValueType::T_BOOLEAN, false))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),
            (new SettingData(SettingKey::WPCC_CONNECTION_TIMEOUT,        ValueType::T_INTEGER, 0))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),
            (new SettingData(SettingKey::WPCC_TEST_URL_PROXY,            ValueType::T_STRING, ''))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),
            (new SettingData(SettingKey::WPCC_PROXIES,                   ValueType::T_STRING, ''))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),
            (new SettingData(SettingKey::WPCC_PROXY_TRY_LIMIT,           ValueType::T_INTEGER, 0))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),
            (new SettingData(SettingKey::WPCC_PROXY_RANDOMIZE,           ValueType::T_BOOLEAN, ''))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),

            (new SettingData(SettingKey::WPCC_DISABLE_TOOLTIP,           ValueType::T_BOOLEAN, false))->setTabKey(TabKey::GENERAL_SETTINGS_TAB_ADVANCED),
        ]);

    }

    /**
     * Creates CRON settings registry
     *
     * @return SettingRegistry
     * @since 1.9.0
     */
    private function createCronSettingsRegistry(): SettingRegistry {
        return new SettingRegistry([
            /* Keys for url-collection CRON event */
            new SettingData(SettingKey::CRON_LAST_CHECKED_AT,                       ValueType::T_DATE_STR),
            new SettingData(SettingKey::CRON_LAST_CHECKED_CATEGORY_URL,             ValueType::T_STRING),
            new SettingData(SettingKey::CRON_LAST_CHECKED_CATEGORY_NEXT_PAGE_URL,   ValueType::T_STRING),
            new SettingData(SettingKey::CRON_NO_NEW_URL_INSERTED_COUNT,             ValueType::T_INTEGER),

            new SettingData(SettingKey::CRON_CRAWLED_PAGE_COUNT, ValueType::T_INTEGER),

            /* Keys for post-crawling CRON event */
            new SettingData(SettingKey::CRON_LAST_CRAWLED_AT,       ValueType::T_DATE_STR),
            new SettingData(SettingKey::CRON_LAST_CRAWLED_URL_ID,   ValueType::T_INTEGER),
            new SettingData(SettingKey::CRON_POST_NEXT_PAGE_URL,    ValueType::T_STRING),
            new SettingData(SettingKey::CRON_POST_NEXT_PAGE_URLS,   ValueType::T_ARRAY),

            new SettingData(SettingKey::CRON_POST_DRAFT_ID, ValueType::T_INTEGER),

            /* Keys for post-recrawling CRON event */
            new SettingData(SettingKey::CRON_RECRAWL_LAST_CRAWLED_AT,       ValueType::T_DATE_STR),
            new SettingData(SettingKey::CRON_RECRAWL_LAST_CRAWLED_URL_ID,   ValueType::T_INTEGER),
            new SettingData(SettingKey::CRON_RECRAWL_POST_NEXT_PAGE_URL,    ValueType::T_STRING),
            new SettingData(SettingKey::CRON_RECRAWL_POST_NEXT_PAGE_URLS,   ValueType::T_ARRAY),

            new SettingData(SettingKey::CRON_RECRAWL_POST_DRAFT_ID, ValueType::T_INTEGER),

            /* Keys for post-delete CRON event */
            new SettingData(SettingKey::CRON_LAST_DELETED_AT, ValueType::T_DATE_STR),
        ]);
    }

}