<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/03/16
 * Time: 19:35
 */

namespace WPCCrawler\Services;

use Illuminate\Support\Str;
use WP_Post;
use WPCCrawler\Environment;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Cache\ResponseCache;
use WPCCrawler\Objects\Crawling\Data\PostData;
use WPCCrawler\Objects\Enums\ShortCodeName;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\PostPage\SetFeaturedImage;
use WPCCrawler\Objects\Filtering\Filter\FilterList;
use WPCCrawler\Objects\Filtering\FilteringService;
use WPCCrawler\Objects\OptionsBox\Boxes\File\FileOptionsBoxApplier;
use WPCCrawler\Objects\OptionsBox\Enums\OptionsBoxTab;
use WPCCrawler\Objects\OptionsBox\Enums\OptionsBoxType;
use WPCCrawler\Objects\OptionsBox\Enums\TabOptions\TemplatesTabOptions;
use WPCCrawler\Objects\OptionsBox\OptionsBoxConfiguration;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingService;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\ShortCodeButton;
use WPCCrawler\Objects\SitePostTypeCreator;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformationService;
use WPCCrawler\Permission;
use WPCCrawler\PostDetail\PostDetailsService;
use WPCCrawler\Test\Test;
use WPCCrawler\Utils;

/**
 * Service class for custom post create/edit page. This includes mostly meta box stuff.
 *
 * Class PostService
 * @package WPCCrawler
 */
class PostService {

    // TODO: Add a notification when the post's status is draft. The notification should be shown at the top of the page
    //  in site settings, informing the user that unpublished sites are not crawled.

    /**
     * @var array Meta keys used to store settings for each site
     */
    private $metaKeys;

    /**
     * @var array A key-value pair where keys are post meta keys defined in {@link $metaKeys} and the values are their
     *            default values.
     */
    private $metaKeyDefaults;

    /**
     * @var array Meta keys used to keep track of the CRON jobs
     */
    private $cronMetaKeys;

    /** @var array Meta keys used to store a string value (not array). These are very important for importing/exporting
     * settings successfully. */
    private $singleMetaKeys;

    /** @var array|null */
    private $editorButtonsMain;
    /** @var array|null */
    private $editorButtonsTitle;
    /** @var array|null */
    private $editorButtonsExcerpt;
    /** @var array|null */
    private $editorButtonsList;
    /** @var array|null */
    private $editorButtonsGallery;
    /** @var array|null */
    private $editorButtonsOptionsBoxTemplates;

    /** @var array */
    private $allPredefinedShortCodes = [];

    /** @var null|array Holds count of saved URLs and URLs in queue for each site */
    private static $urlCounts = null;

    public function __construct() {
        add_action('plugins_loaded', function() {
            // Initialize the meta keys when the plugins are loaded
            $this->initMetaKeys();
        }, 999); // Execute this as late as possible since we want the registered post detail factories add their own meta keys as well

        // Create post type
        SitePostTypeCreator::getInstance()->create();

        // Create pageActionKey JS variable, which can be used when making AJAX requests as action variable
        add_action('admin_print_scripts', function() {
            // Print the script only if we are on a site page.
            $screen = get_current_screen();
            if($screen && $screen->base == 'post' && $screen->post_type == Environment::postType()) {
                $tooltipDisabled = SettingService::isTooltipDisabled() ? 'true' : 'false';
                echo "
                    <script type='text/javascript'>
                        if(!pageActionKey || pageActionKey == 'undefined') {
                            var pageActionKey = 'wcc_test';
                        }

                        var wpccTooltipDisabled = {$tooltipDisabled};
                    </script>
                ";
            }
        });

        // Register ajax url for site list
        add_action('wp_ajax_wcc_site_list', function() {
            if(!check_admin_referer('wcc-site-list', Environment::nonceName())) wp_die("Nonce is invalid.");

            if(!isset($_POST["data"])) wp_die(_wpcc("Data does not exist in your request. The request should include 'data'"));
            if(!isset($_POST["post_id"])) wp_die(_wpcc("Post ID does not exist in your request. The request should have 'post_id'."));

            if (!Permission::canUpdateSiteSettings()) Permission::displayNotAllowedMessageAndExit();

            // We'll return JSON response.
            header('Content-Type: application/json');

            echo Factory::postService()->postSiteList($_POST["post_id"], $_POST["data"]) ?: '';
            wp_die();
        });

        // Register ajax url for tests
        add_action('wp_ajax_wcc_test', function () {
            if(!check_admin_referer('wcc-settings-metabox', Environment::nonceName())) wp_die();

            if(!isset($_POST["data"])) wp_die(_wpcc("Data does not exist in your request. The request should include 'data'"));

            // We'll return JSON response.
            header('Content-Type: application/json');

            $data = $_POST["data"];

            // Show the test results
            if(isset($data["testType"]) && $testType = $data["testType"]) {
                $result = Test::respondToTestRequest($data);
                if($result !== null) {
                    echo $result;
                }

            } else if(isset($data["requestType"]) && $requestType = $data["requestType"]) {
                $result = SettingService::respondToAjaxRequest($data);
                if($result !== null) {
                    echo $result;
                }

            // If there is a command
            } else if(isset($data["cmd"]) && $cmd = $data["cmd"]) {
                switch($cmd) {
                    case "saveDevToolsState":
                        if($data["postId"]) {
                            $result = Utils::savePostMeta($data["postId"], SettingKey::DEV_TOOLS_STATE, json_encode($data["state"]));
                            echo $result ? 1 : 0;
                        }

                        break;

                    case "loadGeneralSettings":
                    case "clearGeneralSettings":
                        $isPostPage = true;
                        $isOption = true;
                        $settings = $cmd == "clearGeneralSettings" ? [] : SettingService::getAllGeneralSettings();

                        // The views expect array-valued settings as 1-item arrays whose values stores the actual values
                        // in serialized. This is how post meta values are saved. However, we supply option values here,
                        // since these settings are general settings. To make the views handle array-valued options
                        // properly, reformat the values into the form the views expect.
                        foreach($settings as $k => &$v) {
                            if (is_array($v)) $v = [serialize($v)];
                        }

                        $view = Utils::view('general-settings.settings')
                            ->with(SettingService::getSettingsPageVariables(false))
                            ->with(compact("isPostPage", "isOption", "settings"))
                            ->render();

                        // HTML attributes with JSON values cause the attributes not to be rendered properly by browser.
                        // So, let's replace single quotes of the JSON-valued attributes with double quotes. Also, double
                        // quotes in JSON string are escaped as &quot;. Let's unescape them as well. After all this, the
                        // HTML will be valid.
                        $view = str_replace('="{', "='{", $view);
                        $view = str_replace('="[', "='[", $view);
                        $view = str_replace('}"', "}'", $view);
                        $view = str_replace(']"', "]'", $view);
                        $view = str_replace("&quot;", '"', $view);

                        $response = json_encode([
                            "view" => $view
                        ]);

                        echo $response;

                        break;

                    case "invalidate_url_response_cache":
                        $url = Utils::array_get($data, "url");

                        $result = $url ? ResponseCache::getInstance()->delete("GET", $url) : false;
                        echo $result ? 1 : 0;
                        break;

                    case "invalidate_all_url_response_caches":
                        $result = ResponseCache::getInstance()->deleteAll();
                        echo $result ? 1 : 0;
                        break;

                    case "saveSiteSettings":
                        echo $this->quickSaveSettings($data) ?: '';
                        break;

                    case "filtering":
                        if(isset($data["postId"])) {
                            $service = new FilteringService(new SettingsImpl(
                                get_post_meta($data["postId"]),
                                Factory::postService()->getSingleMetaKeys()
                            ));
                            echo json_encode($service->handleAjax($data));
                        }

                        break;
                    default:
                        $response = SettingService::respondToAjaxCmdRequest($data);
                        if ($response !== null) {
                            echo $response;
                        }

                        break;
                }
            }

            wp_die();
        });

    }

    /**
     * Initializes meta keys used by the plugin
     * @since 1.8.0
     */
    private function initMetaKeys(): void {
        $this->metaKeys         = Factory::settingRegistryService()->getRegistrySiteSettings()->getKeys();
        $this->metaKeyDefaults  = Factory::settingRegistryService()->getRegistrySiteSettings()->getDefaults();
        $this->singleMetaKeys   = Factory::settingRegistryService()->getSingleKeys();
        $this->cronMetaKeys     = Factory::settingRegistryService()->getRegistryCronSettings()->getKeys();

        // Combine meta keys for the post and keys for general settings. By this way, the user will be able to save options
        // for those keys. This is because the request is checked for $metaKeys.
        // First, remove the setting used for activating scheduling. Each site already has an "active" setting.
        $generalSettings = Factory::generalSettingsController()->getGeneralSettingsKeys();
        unset($generalSettings[array_search(SettingKey::WPCC_IS_SCHEDULING_ACTIVE, $generalSettings)]);
        $this->metaKeys = array_merge($this->metaKeys, $generalSettings);

        // Add the meta keys of the registered post details
        $this->metaKeys         = PostDetailsService::getInstance()->addAllSettingsMetaKeys($this->metaKeys);
        $this->metaKeyDefaults  = PostDetailsService::getInstance()->addAllSettingsMetaKeyDefaults($this->metaKeyDefaults);
        $this->singleMetaKeys   = PostDetailsService::getInstance()->addAllSingleSettingsMetaKeys($this->singleMetaKeys);

        /*
         * ALLOW MODIFICATION OF META KEYS WITH FILTERS
         */

        /**
         * Modify meta keys that are used to save site settings.
         *
         * @param array $metaKeys
         *
         * @since 1.6.3
         * @return array Modified meta keys
         */
        $this->metaKeys = apply_filters('wpcc/post/settings/meta-keys', $this->metaKeys);

        /**
         * Modify meta key defaults.
         *
         * @param array $metaKeyDefaults
         *
         * @since 1.8.0
         * @return array Modified meta key defaults
         */
        $this->metaKeyDefaults = apply_filters('wpcc/post/settings/meta-key-defaults', $this->metaKeyDefaults);

        /**
         * Modify CRON meta keys that are used to save information about CRON events.
         *
         * @param array $cronMetaKeys
         *
         * @since 1.6.3
         * @return array Modified CRON meta keys
         */
        $this->cronMetaKeys = apply_filters('wpcc/post/settings/cron-meta-keys', $this->cronMetaKeys);

        /**
         * Modify single meta keys. These keys can only be used to store a single value. So, they cannot store serialized
         * array etc. They can only store a single value. Indicating if a meta key is single or not has a vital importance
         * when showing already-saved settings in the form item fields and importing/exporting settings. Hence, if a meta
         * key you added to 'metaKeys' stores a single value, you have to make sure that you added that meta key among
         * 'singleMetaKeys' as well.
         *
         * @param array $singleMetaKeys
         *
         * @since 1.6.3
         * @return array Modified single meta keys
         */
        $this->singleMetaKeys = apply_filters('wpcc/post/settings/single-meta-keys', $this->singleMetaKeys);
    }

    /**
     * Handles AJAX requests made from site list page
     *
     * @param int   $postId ID of the site to be updated
     * @param array $data
     * @return string|null JSON
     */
    public function postSiteList($postId, $data): ?string {
        $allKeys = [
            SettingKey::ACTIVE,
            SettingKey::ACTIVE_RECRAWLING,
            SettingKey::ACTIVE_POST_DELETING,
        ];

        if(!Factory::wptslmClient()->isUserCool()) {
            $key = null;
            foreach($allKeys as $candidate) {
                if (isset($data[$candidate])) {
                    $key = $candidate;
                    break;
                }
            }

            if (!$key) return null;

            return json_encode([
                "data" => $data,
                $key   => $data[$key] == "true" ? false : true,
            ]) ?: null;
        }

        // Save the data
        $results = [
            "data"    => $data,
            "post_id" => $postId,
        ];

        foreach($allKeys as $key) {
            if(!isset($data[$key])) continue;

            $results[$key] = Utils::savePostMeta($postId, $key, $data[$key] == "true" ? true : false, true);
        }

        return json_encode($results) ?: null;
    }

    /**
     * Prepares and returns HTML for site settings meta box
     * @return string HTML
     */
    public function getSettingsMetaBox() {
        global $post;

        // Set Tiny MCE settings so that it allows custom HTML codes and keeps them unchanged
        add_filter('tiny_mce_before_init', function($settings) {

            // Disable autop to keep all valid HTML elements
            $settings['wpautop'] = false;

            // Don't remove line breaks
            $settings['remove_linebreaks'] = false;

            // Format the HTML
            $settings['apply_source_formatting'] = true;

            // Convert newline characters to BR
            $settings['convert_newlines_to_brs'] = true;

            // Don't remove redundant BR
            $settings['remove_redundant_brs'] = false;

            // Pass back to WordPress
            return $settings;
        });

        $settings = get_post_meta($post->ID);

        // Set the defaults only if there are no settings.
        if (!$settings) {
            $defaults = [];
            foreach($this->metaKeyDefaults as $k => $v) {
                $defaults[$k] = is_array($v) ? [serialize($v)] : $v;
            }

            $settings = $defaults;
        }

        $settingsImpl = new SettingsImpl($settings, $this->getSingleMetaKeys());

        // Create view variables
        $viewVars = array_merge([
            'postId'                        => $post->ID,
            'settings'                      => $settings,
            'settingsForExport'             => base64_encode(serialize($this->getSettingsForExport($settings))),
            'categories'                    => Utils::getCategories($settingsImpl, true),
            'buttonsMain'                   => $this->getEditorButtonsMain(),
            'buttonsTitle'                  => $this->getEditorButtonsTitle(),
            'buttonsExcerpt'                => $this->getEditorButtonsExcerpt(),
            'buttonsList'                   => $this->getEditorButtonsList(),
            'buttonsGallery'                => $this->getEditorButtonsGallery(),
            'buttonsOptionsBoxTemplates'    => $this->getEditorButtonsOptionsBoxTemplates(),
            'buttonsFileOptionsBoxTemplates'=> FileOptionsBoxApplier::getShortCodeButtons(),
            'optionsBoxConfigs'             => $this->getOptionsBoxConfigs($settingsImpl),
            'transformableFields'           => $this->getTransformableFieldsOptions($settingsImpl),
        ], SettingService::getSettingsPageVariables(false));

        // Add post detail settings if there are any
        $postDetailSettingsViews = PostDetailsService::getInstance()->getSettingsViews($settingsImpl, $viewVars);

        $viewVars['postDetailSettingsViews'] = $postDetailSettingsViews;

        return Utils::view('site-settings/main')->with($viewVars)->render();
    }

    /**
     * Get an array that can be used to show transformable fields in a select HTML element.
     *
     * @param SettingsImpl $postSettings
     * @return array A 2 dimensional array. For the first level, keys are names of the option groups and the values are
     *               the options, which are arrays. The inner arrays are associative arrays where keys are option names
     *               and the values are descriptions for them.
     * @since 1.9.0
     */
    private function getTransformableFieldsOptions(SettingsImpl $postSettings) {
        $dummyPostData = new PostData();
        $result = [
            _wpcc("Post") => AbstractTransformationService::prepareTransformableFieldForSelect(
                $dummyPostData->getTransformableFields()->toAssociativeArray(),
                Environment::defaultPostIdentifier()
            )
        ];

        $postDetailOptions = PostDetailsService::getInstance()->getTransformableFields($postSettings);
        if ($postDetailOptions) {
            $result = array_merge($result, $postDetailOptions);
        }

        return $result;
    }

    /**
     * Creates options box configurations for specific settings.
     *
     * @param SettingsImpl $postSettings
     * @return array A key-value pair. The keys are meta keys of the settings. The values are arrays storing the
     *               configuration for the options box for that setting.
     * @since 1.8.0
     */
    private function getOptionsBoxConfigs($postSettings) {

        $configs = [
            // Category post URL selectors
            SettingKey::CATEGORY_POST_LINK_SELECTORS => OptionsBoxConfiguration::init()
                ->addTabOption(OptionsBoxTab::TEMPLATES, TemplatesTabOptions::ALLOWED_SHORT_CODES, [
                    ShortCodeName::WCC_ITEM
                ])->get(),

            // Category next page selectors
            SettingKey::CATEGORY_NEXT_PAGE_SELECTORS => OptionsBoxConfiguration::init()
                ->addTabOption(OptionsBoxTab::TEMPLATES, TemplatesTabOptions::ALLOWED_SHORT_CODES, [
                    ShortCodeName::WCC_ITEM
                ])->get(),

            // Featured image selectors
            SettingKey::POST_THUMBNAIL_SELECTORS => OptionsBoxConfiguration::init()
                ->setType(OptionsBoxType::FILE)
                ->get(),

            // Gallery image selectors
            SettingKey::POST_GALLERY_IMAGE_SELECTORS => OptionsBoxConfiguration::init()
                ->setType(OptionsBoxType::FILE)
                ->get(),

            // Image selectors
            SettingKey::POST_IMAGE_SELECTORS => OptionsBoxConfiguration::init()
                ->setType(OptionsBoxType::FILE)
                ->get(),
        ];

        // Get the configurations of registered post details
        $configs = array_merge($configs, PostDetailsService::getInstance()->getOptionsBoxConfigs($postSettings));

        return $configs;
    }

    /**
     * Prepares and returns HTML for site notes meta box
     * @return string HTML
     */
    public function getNotesMetaBox(): string {
        global $post;
        $notesSimple = get_post_meta($post->ID, SettingKey::NOTES_SIMPLE);

        return Utils::view('site-settings/meta-box-notes')->with([
            'notesSimple'   =>  $notesSimple
        ])->render();
    }

    /**
     * Handles HTTP POST requests made by create/edit page (where site settings meta box is)
     *
     * @param int          $postId
     * @param WP_Post|null $postAfter
     * @param WP_Post|null $postBefore
     */
    public function postSettingsMetaBox($postId, $postAfter, $postBefore): void {
        if(!Factory::wptslmClient()->isUserCool() || !Permission::canUpdateSiteSettings()) return;

        // If the nonce does not exist in the request or the request is not made from admin page, abort.
        if(!isset($_POST["action"]) || !$_POST["action"] == 'wcc_tools') {  // Allow requests made from Tools
            if (!isset($_POST[Environment::nonceName()]) || !check_admin_referer('wcc-settings-metabox', Environment::nonceName()))
                return;
        }

        // Do not run if the post is moved to trash.
        if ($postAfter && $postAfter->post_status == 'trash') return;

        // Do not run if the post is restored.
        if ($postBefore && $postBefore->post_status == 'trash') return;

        $this->saveSettings($postId, $_POST);
    }

    /**
     * Saves settings from AJAX data that contains serialized form values.
     *
     * @param array $data AJAX data
     * @return string|null JSON
     * @since 1.8.0
     */
    private function quickSaveSettings($data): ?string {
        if (!Factory::wptslmClient()->isUserCool() || !Permission::canUpdateSiteSettings()) {
            return json_encode([
                "success" => false,
                "message" => _wpcc("Settings could not be saved."),
            ]) ?: null;
        }

        $postId = Utils::array_get($data, "postId");
        $serializedSettings = Utils::array_get($data, "settings");
        if (!$serializedSettings) {
            return json_encode([
                "success" => false,
                "message" => _wpcc("Settings do not exist in the data.")
            ]) ?: null;
        }

        if (!$postId) {
            return json_encode([
                "success" => false,
                "message" => _wpcc("Post ID does not exist.")
            ]) ?: null;
        }

        // Prepare the serialized settings string

        // parse_str function escapes special characters. However, it cannot escape special characters that are
        // URL-encoded. Therefore, we need to escape them manually. urldecode function does not do the job either. It
        // behaves the same for some reason.
        // A backslash is URL-encoded and it needs to be escaped. Here, we replace a backslash, whose URL-encoded
        // equivalent is %5C, with double backslash, which is %5C%5C.
        $serializedSettings = str_replace('%5C', '%5C%5C', $serializedSettings);

        // Parse the serialized value to an array
        $settings = [];
        parse_str($serializedSettings, $settings);

        // Remove URL hash since it is only needed when the page is updated after saving. Here, the settings are saved
        // via AJAX. So, no update.
        if (isset($settings[Environment::keyUrlHash()])) unset($settings[Environment::keyUrlHash()]);

        // Save the settings
        $result = $this->saveSettings($postId, $settings);

        // Add export option's value
        $result["settingsForExport"] = base64_encode(serialize($this->getSettingsForExport(get_post_meta($postId))));

        return json_encode($result) ?: null;
    }

    /**
     * @param int   $postId
     * @param array $settings Settings retrieved from form. $_POST can be directly supplied. The values must be slashed
     *                        because WP's post meta saving function requires slashed data.
     * @return array
     * @since 1.8.0
     */
    public function saveSettings($postId, $settings) {
        $data = $settings;
        $success = true;
        $message = '';

        $queryParams = [];
        if(isset($data[Environment::keyUrlHash()])) $queryParams[Environment::keyUrlHash()] = $data[Environment::keyUrlHash()];

        // Check if the user wants to import the settings
        if(isset($data[SettingKey::POST_IMPORT_SETTINGS]) && !empty($data[SettingKey::POST_IMPORT_SETTINGS])) {
            // User wants to import the settings. Parse them and replace data variable with the imported settings.
            $serializedSettings = base64_decode($data[SettingKey::POST_IMPORT_SETTINGS]);
            if($serializedSettings && is_serialized($serializedSettings)) {
                $settings = unserialize($serializedSettings);

                // When saving the data with update_post_meta or a similar function, WordPress first unslashes it.
                // So, we need to slash the values of the array using wp_slash. This does not matter when normally saving
                // the settings. Because, WordPress automatically slashes the values taken from $_POST.
                $data = Utils::arrayDeepSlash($settings);
            }
        }

        // Check if the category map is the same as before
        $categoryMapBefore = get_post_meta($postId, SettingKey::CATEGORY_MAP, true);
        if(is_array($categoryMapBefore)) $categoryMapBefore = array_values($categoryMapBefore);
        if(isset($data[SettingKey::CATEGORY_MAP])) {
            $categoryMapCurrent = array_values($data[SettingKey::CATEGORY_MAP]);

            // If category map is changed, then delete all of the unsaved URLs belonging to this site. Because, it is
            // not possible to know which URL is for which category, since we do not store category URLs in the table.
            if($categoryMapBefore !== $categoryMapCurrent) {
                Factory::databaseService()->deleteUrlsBySiteIdAndSavedStatus($postId, false);

                // Also reset (deleting does the job) the CRON meta values for this site
                $cronMetaKeys = $this->cronMetaKeys;

                unset($cronMetaKeys[array_search(SettingKey::CRON_LAST_CRAWLED_AT,          $cronMetaKeys)]);
                unset($cronMetaKeys[array_search(SettingKey::CRON_LAST_CHECKED_AT,          $cronMetaKeys)]);
                unset($cronMetaKeys[array_search(SettingKey::CRON_RECRAWL_LAST_CRAWLED_AT,  $cronMetaKeys)]);
                unset($cronMetaKeys[array_search('_cron_recrawl_last_checked_at',           $cronMetaKeys)]); // TODO: Is this CRON key used anywhere? Find out. If not, delete this.

                foreach($cronMetaKeys as $key) {
                    delete_post_meta($postId, $key);
                }
            }
        }

        $keys = $this->metaKeys;

        // Validate password fields
        $validate = Utils::validatePasswordInput($data, $keys, get_post_meta($postId, SettingKey::WPCC_POST_PASSWORD, true));
        if(!$validate["success"]) {
            // Not valid.
            $message = $validate["message"] . ' ' . _wpcc('Settings are updated, but password could not be changed.');
            $success = false;
        }

        // Save options
        foreach ($data as $key => $value) {
            if (in_array($key, $this->metaKeys)) {
                if(is_array($value)) $value = array_values($value);
                Utils::savePostMeta($postId, $key, $value, true);

                // Remove the key, since it is saved.
                unset($keys[array_search($key, $keys)]);
            }
        }

        // Delete the metas which are not set
        foreach($keys as $key) delete_post_meta($postId, $key);

        // Update notice option. This option is used to show notices on site (custom post) page.
        if(!$success) {
            update_option('_wpcc_site_notice', $message, true);
            Utils::savePostMeta($postId, '_wpcc_site_query_params', false);
        } else {
            update_option('_wpcc_site_notice', false, true);
            Utils::savePostMeta($postId, '_wpcc_site_query_params', $queryParams);
        }

        $this->updateProtectedAttachmentIds($data);
        return [
            "message" => $message,
            "success" => $success
        ];
    }

    /**
     * Extracts the attachment IDs specified in the site settings and add those attachment IDs as protected IDs so that
     * they will not be deleted while a post is being recrawled.
     *
     * @param array $settings The site settings
     * @since 1.12.0
     */
    private function updateProtectedAttachmentIds(array $settings): void {
        // Currently, the attachment IDs are specified in the "post data filters" setting only. So, get the filters
        // of that setting. If there are none, no need to do anything, stop.
        $rawJson = $settings[SettingKey::POST_DATA_FILTERS] ?? null;
        if ($rawJson === null) return;

        // If the raw JSON string is slashed, unslash it. It comes as slashed when the data is sent by submitting the
        // form. Otherwise, it is not slashed.
        $postDataFilters = FilterList::fromJson(Str::startsWith($rawJson, '{\\"')
            ? wp_unslash($rawJson)
            : $rawJson
        );
        if (!$postDataFilters) return;

        // Extract the attachment IDs from the "set featured image" setting
        $attachmentIds = [];
        foreach($postDataFilters->getItems() as $filter) {
            foreach($filter->getActions() as $action) {
                if (!($action instanceof SetFeaturedImage)) {
                    continue;
                }

                $ids = $action->getIds();
                if (!$ids) {
                    continue;
                }

                $attachmentIds = array_merge($attachmentIds, $ids);
            }
        }

        // If there are no attachments, stop.
        if (!$attachmentIds) {
            return;
        }

        // Add the new attachment IDs to the list of the protected attachment IDs
        SettingService::addProtectedAttachmentIds($attachmentIds);
    }

    /**
     * Prepares and returns an array for exporting settings.
     *
     * @param array $settings
     * @return array
     */
    private function getSettingsForExport($settings): array {
        foreach($settings as $key => &$mSetting) {
            // If current key is not in our meta keys, remove it from the array. We should export only related settings.
            // Otherwise, we have to deal with this when importing.
            if(!in_array($key, $this->metaKeys)) {
                unset($settings[$key]);
                continue;
            }

            $mSetting = $this->getUnserialized($mSetting);

            // Set single meta key values as string
            if(in_array($key, $this->singleMetaKeys) && is_array($mSetting) && !empty($mSetting)) {
                $mSetting = array_values($mSetting)[0];
            }
        }

        return $settings;
    }

    /**
     * Checks a parameter if it should be unserialized, and if so, does so. If the parameter has serialized values inside,
     * those will be unserialized as well. Hence, at the end, there will be no serialized strings inside the value.
     *
     * @param mixed $metaValue The value to be unserialized
     * @return mixed Unserialized value
     */
    private function getUnserialized($metaValue) {
        $val = (!empty($metaValue) && isset($metaValue[0])) ? $metaValue[0] : $metaValue;
        return is_serialized($val) ? $this->getUnserialized(unserialize($val)) : $metaValue;
    }

    /**
     * Get counts of URLs grouped by site ID and whether they are saved or not.
     *
     * @return array An array with keys being site IDs and values being an array containing post counts. Each value
     * array has <b>count_saved</b>, <b>count_updated</b>, <b>count_queue</b>, <b>count_deleted</b>, <b>count_other</b>, <b>count_total</b>.
     * These values are either <b>integer or null</b>.
     */
    public function getUrlTableCounts() {
        // If it is already found before, return it.
        if(static::$urlCounts) return static::$urlCounts;

        // Find URL counts
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();

        /** @noinspection SqlNoDataSourceInspection */
        /** @noinspection SqlResolve */
        $query = "SELECT t_total.post_id, count_saved, count_updated, count_queue, count_deleted,
                (IFNULL(count_total, 0) - IFNULL(count_saved, 0) - IFNULL(count_queue, 0) - IFNULL(count_deleted, 0)) as count_other, count_total
            FROM
                (SELECT post_id, count(*) as count_total FROM {$tableUrls} GROUP BY post_id) t_total
            
            LEFT JOIN (
                SELECT post_id, count(*) as count_queue 
                FROM {$tableUrls} 
                WHERE saved_post_id IS NULL 
                    AND is_saved = FALSE 
                GROUP BY post_id) t_queue ON t_total.post_id = t_queue.post_id
            
            LEFT JOIN (
                SELECT post_id, count(*) as count_saved
                FROM {$tableUrls} 
                WHERE saved_post_id IS NOT NULL 
                    AND is_saved = TRUE
                GROUP BY post_id) t_saved ON t_total.post_id = t_saved.post_id
                
            LEFT JOIN (
                SELECT post_id, count(*) as count_updated
                FROM {$tableUrls} 
                WHERE saved_post_id IS NOT NULL 
                    AND is_saved = TRUE
                    AND update_count > 0
                GROUP BY post_id) t_updated ON t_total.post_id = t_updated.post_id
            
            LEFT JOIN (
                SELECT post_id, count(*) as count_deleted
                FROM {$tableUrls}
                WHERE saved_post_id IS NULL
                    AND deleted_at IS NOT NULL
                GROUP BY post_id) t_deleted ON t_total.post_id = t_deleted.post_id";

        $results = $wpdb->get_results($query, ARRAY_A);
        $data = [];

        foreach($results as $result) {
            // Get post id from current result
            $currentPostId = $result["post_id"];

            // Unset the post id
            unset($result["post_id"]);

            // Add the result to the data under post ID key.
            $data[$currentPostId] = $result;
        }

        static::$urlCounts = $data;

        return static::$urlCounts;
    }

    /*
     * EDITOR BUTTONS
     */

    private function getEditorButtonsMain(): array {
        if(!$this->editorButtonsMain) $this->editorButtonsMain = [
            $this->createButtonInfo(ShortCodeName::WCC_MAIN_TITLE,   _wpcc("Prepared post title"), true),
            $this->createButtonInfo(ShortCodeName::WCC_MAIN_EXCERPT, _wpcc("Prepared post excerpt"), true),
            $this->createButtonInfo(ShortCodeName::WCC_MAIN_CONTENT, _wpcc("Main post content")),
            $this->createButtonInfo(ShortCodeName::WCC_MAIN_LIST,    _wpcc("List items")),
            $this->createButtonInfo(ShortCodeName::WCC_MAIN_GALLERY, _wpcc("Gallery items")),
            $this->createButtonInfo(ShortCodeName::WCC_SOURCE_URL,   sprintf(_wpcc('Full URL of the target page. You can use this to reference the source page. E.g. <a href="%1$s">Source</a>'), '[' . ShortCodeName::WCC_SOURCE_URL .']')),
        ];

        return $this->editorButtonsMain;
    }

    private function getEditorButtonsTitle(): array {
        if(!$this->editorButtonsTitle) $this->editorButtonsTitle = [
            $this->createButtonInfo(ShortCodeName::WCC_MAIN_TITLE, _wpcc("Original post title"), true),
        ];

        return $this->editorButtonsTitle;
    }

    private function getEditorButtonsExcerpt(): array {
        if(!$this->editorButtonsExcerpt) $this->editorButtonsExcerpt = [
            $this->createButtonInfo(ShortCodeName::WCC_MAIN_TITLE,   _wpcc("Prepared post title"), true),
            $this->createButtonInfo(ShortCodeName::WCC_MAIN_EXCERPT, _wpcc("Original post excerpt"), true),
        ];

        return $this->editorButtonsExcerpt;
    }

    private function getEditorButtonsList(): array {
        if(!$this->editorButtonsList) $this->editorButtonsList = [
            $this->createButtonInfo(ShortCodeName::WCC_LIST_ITEM_TITLE, _wpcc("List item title")),
            $this->createButtonInfo(ShortCodeName::WCC_LIST_ITEM_CONTENT, _wpcc("List item content")),
            $this->createButtonInfo(ShortCodeName::WCC_LIST_ITEM_POSITION, _wpcc("The position of the item.")),
        ];

        return $this->editorButtonsList;
    }

    private function getEditorButtonsGallery(): array {
        if(!$this->editorButtonsGallery) $this->editorButtonsGallery = [
            $this->createButtonInfo(ShortCodeName::WCC_GALLERY_ITEM_URL, _wpcc("Gallery item URL"))
        ];

        return $this->editorButtonsGallery;
    }

    private function getEditorButtonsOptionsBoxTemplates(): array {
        if (!$this->editorButtonsOptionsBoxTemplates) {
            $this->editorButtonsOptionsBoxTemplates = array_merge([
                $this->createButtonInfo(ShortCodeName::WCC_ITEM, _wpcc("Found item"))
            ], $this->getEditorButtonsMain());
        }

        return $this->editorButtonsOptionsBoxTemplates;
    }

    /**
     * @param string      $code        Short code without square brackets
     * @param string|null $description Description for what the short code does
     * @param bool        $fresh       True if a fresh instance should be returned. Otherwise, if the code created before,
     *                                 the previously-created instance will be returned.
     * @return ShortCodeButton Short code button
     */
    private function createButtonInfo(string $code, ?string $description = '', bool $fresh = false): ShortCodeButton {
        return ShortCodeButton::getShortCodeButton($code, $description, $fresh);
    }

    /**
     * Get an array of all predefined short codes
     * @return array An array of short codes with square brackets
     */
    public function getPredefinedShortCodes() {
        if(!$this->allPredefinedShortCodes) {
            $combinedButtons = array_merge(
                $this->getEditorButtonsMain(),
                $this->getEditorButtonsTitle(),
                $this->getEditorButtonsExcerpt(),
                $this->getEditorButtonsList(),
                $this->getEditorButtonsGallery()
            );
            $result = [];
            foreach ($combinedButtons as $btn) {
                /** @var ShortCodeButton $btn */
                $result[] = $btn->getCodeWithBrackets();
            }

            $this->allPredefinedShortCodes = $result;
        }

        return $this->allPredefinedShortCodes;
    }

    /*
     *
     */

    /**
     * Get single meta keys
     *
     * @return array An array of keys
     */
    public function getSingleMetaKeys() {
        return $this->singleMetaKeys;
    }
}
