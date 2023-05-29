<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 13/04/16
 * Time: 23:13
 */

namespace WPCCrawler\Objects\AssetManager;


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use WPCCrawler\Objects\Docs;
use WPCCrawler\Objects\Enums\PageType;
use WPCCrawler\Objects\File\FileService;
use WPCCrawler\Objects\Guides\GuideTranslations;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\PostDetail\PostDetailsService;

class AssetManager extends BaseAssetManager {

    /** @var AssetManager|null */
    private static $instance;

    /** @var string */
    private $scriptApp                      = 'wcc_app_js';
    /** @var string */
    private $scriptUtils                    = 'wcc_utils_js';

    /** @var string */
    private $stylePostSettings              = 'wcc_post_settings_css';

    /** @var string */
    private $scriptTooltip                  = 'wcc_tooltipjs';

    /** @var string */
    private $scriptClipboard                = 'wcc_clipboardjs';

    /** @var string */
    private $styleGeneralSettings           = 'wcc_general_settings_css';

    /** @var string */
    private $styleSiteTester                = 'wcc_site_tester_css';

    /** @var string */
    private $styleTools                     = 'wcc_tools_css';

    /** @var string */
    private $styleDashboard                 = 'wcc_dashboard_css';

    /** @var string */
    private $styleDevTools                  = 'wcc_dev_tools_css';

    /** @var string */
    private $styleOptionsBox                = 'wcc_options_box_css';

    /** @var string */
    private $styleFeatherlight              = 'wcc_featherlight_css';
    /** @var string */
    private $scriptFeatherlight             = 'wcc_featherlight_js';
    /** @var string */
    private $scriptOptimalSelect            = 'wcc_optimal_select_js';
    /** @var string */
    private $scriptJSDetectElementResize    = 'wcc_js_detect_element_size_js';

    /** @var string */
    private $scriptFormSerializer           = 'wcc_form_serializer_js';

    /** @var string */
    private $styleBootstrapGrid             = 'wcc_bootstrap_grid_css';

    /** @var string */
    private $styleAnimate                   = 'wcc_animate_css';

    /** @var string */
    private $styleFeatureRequest            = 'wcc_feature_request_css';

    /** @var string */
    private $styleSelect2                   = 'wcc_select2_css';
    /** @var string */
    private $scriptSelect2                  = 'wcc_select2_js';

    /** @var string */
    private $styleGuides                    = 'wcc_guides_css';
    /** @var string */
    private $styleShepherd                  = 'wcc_shepherd_css';

    /** @var string */
    private $styleFontAwesome               = 'wcc_fontawesome_css';
    /** @var string */
    private $styleFontAwesomeSolid          = 'wcc_fontawesome_solid_css';

    /**
     * Get the instance
     *
     * @return AssetManager
     * @since 1.11.1
     */
    public static function getInstance(): AssetManager {
        if (static::$instance === null) {
            static::$instance = new AssetManager();
        }

        return static::$instance;
    }

    /**
     * @return string A string that will be the variable name of the JavaScript localization values. E.g. if this is
     *                'wpcc', localization values defined in {@link getLocalizationValues()} will be available under
     *                'wpcc' variable in the JS window.
     * @since 1.8.0
     */
    protected function getLocalizationName(): string {
        return 'wpcc';
    }

    /**
     * Get script localization values.
     *
     * @return array
     */
    protected function getLocalizationValues(): array {
        $values = [
            'an_error_occurred'                     => _wpcc("An error occurred."),
            'press_to_copy'                         => _wpcc("Press {0} to copy"),
            'copied'                                => _wpcc("Copied!"),
            'no_result'                             => _wpcc("No result."),
            'found'                                 => _wpcc("Found"),
            'required_for_test'                     => _wpcc("This is required to perform the test."),
            'required'                              => _wpcc("This is required."),
            'css_selector_found'                    => _wpcc("CSS selector found"),
            'delete_all_test_history'               => _wpcc("Do you want to delete all test history?"),
            'url_data_not_exist'                    => _wpcc("URL data cannot be found."),
            'currently_crawling'                    => _wpcc("Currently crawling"),
            'retrieving_urls_from'                  => _wpcc("Retrieving URLs from {0}"),
            'pause'                                 => _wpcc('Pause'),
            'continue'                              => _wpcc('Continue'),
            'test_data_not_retrieved'               => _wpcc('Test data could not be retrieved.'),
            'content_retrieval_response_not_valid'  => _wpcc("Response of content retrieval process is not valid."),
            'test_data_retrieval_failed'            => _wpcc("Test data retrieval failed."),
            'no_urls_found'                         => _wpcc("No URLs found."),
            'this_is_not_valid'                     => _wpcc("This is not valid."),
            'url_data_not_exist_for_this'           => _wpcc("URL data does not exist for this."),
            'this_url_not_crawled_yet'              => _wpcc("This URL has not been crawled yet."),
            'url_cannot_be_retrieved'               => _wpcc("The URL cannot be retrieved."),
            'cache_invalidated'                     => _wpcc("The cache has been invalidated."),
            'cache_could_not_be_invalidated'        => _wpcc("The cache could not be invalidated."),
            'all_cache_invalidated'                 => _wpcc("All caches have been invalidated."),
            'all_cache_could_not_be_invalidated'    => _wpcc("All caches could not be invalidated."),
            'custom_short_code'                     => _wpcc("Custom short code"),
            'post_id_not_found'                     => _wpcc("Post ID could not be found."),
            'settings_not_retrieved'                => _wpcc("Settings could not be retrieved."),
            'settings_saved'                        => _wpcc("The settings have been saved."),
            'state_not_parsed'                      => _wpcc("The state could not be parsed."),
            'top'                                   => _wpcc("Top"),
            'x_element_selected'                    => _wpcc("{0} element selected"),
            'x_elements_selected'                   => _wpcc("{0} elements selected"),
            'clear'                                 => _wpcc("Clear"),
            'or'                                    => _wpcc("or"),
            'select_category_url'                   => _wpcc("Select a category URL"),
            'see_docs_for_this_setting'             => _wpcc("See in docs"),
            'remove'                                => _wpcc('Remove'),
            'select_an_image'                       => _wpcc('Select an image'),
            'use_this_image'                        => _wpcc('Use this image'),
            'image_with_this_id_does_not_exist'     => _wpcc('An image with this ID does not exist'),
            'subject'                               => _wpcc('Subject'),
            'property'                              => _wpcc('Property'),
            'command'                               => _wpcc('Command'),
            'operator_and'                          => _wpcc('and'),
            'operator_or'                           => _wpcc('or'),
            'filter_if'                             => _wpcc('If'),
            'filter_then'                           => _wpcc('Then'),
            'add_new'                               => _wpcc('Add New'),
            'add_command'                           => _wpcc('Add command'),
            'remove_command'                        => _wpcc('Remove command'),
            'add_condition_block'                   => _wpcc('Add condition block'),
            'remove_block'                          => _wpcc('Remove block'),
            'remove_filter'                         => _wpcc('Remove filter'),
            'move_this'                             => _wpcc('Move this'),
            'value'                                 => _wpcc('Value'),
            'enter_filter_title'                    => _wpcc('Enter a title/description (optional)'),
            'import'                                => _wpcc('Import'),
            'export'                                => _wpcc('Export'),
            'export_filter_description'             => _wpcc("Copy the text below and use another filter setting's import button to import it. Click the export button again to hide this. If you changed anything after this was shown, double click the export button to refresh the export text."),
            'import_filter_description'             => _wpcc("Paste the exported filter settings below. Click the import button again to import the settings and hide this. The imported filters will be added to the top. The existing filters will not be removed."),
            'import_filter_placeholder'             => _wpcc("Paste the exported text here to import..."),
            'export_setting_description'            => _wpcc("Copy the text below and use another setting's import button to import it. Click the export button again to hide this. If you changed anything after this was shown, double click the export button to refresh the export text."),
            'import_setting_description'            => _wpcc("Paste the exported settings below. Click the import button again to import the settings and hide this. The imported settings will be added to the existing ones. The existing settings will not be removed."),
            'import_setting_placeholder'            => _wpcc("Paste the exported text here to import..."),
            'enabled'                               => _wpcc("Enabled"),
            'disabled'                              => _wpcc("Disabled"),
            'filter_enabled_description'            => _wpcc("When this is checked, the filters of this setting will be applied. Otherwise, they won't be applied."),
            'filter_disabled_explanation'           => _wpcc("This filter has been disabled in the site settings. Hence, it is not executed."),
            'filter_setting_disabled_notification'  => _wpcc('This filter setting has been disabled in the site settings. Hence, the filters are not executed.'),
            'stop_after_first_match'                => _wpcc('Stop after first match'),
            'stop_after_first_match_desc'           => _wpcc('Check this if only one match is enough to say that this condition is true when multiple items exist in the selected subject. Checking this improves the performance.'),
            'only_matched_items'                    => _wpcc('Only the items matched by conditions'),
            'only_matched_items_desc'               => _wpcc("If there is a true condition having the same subject as this command's, check this to execute this command only for the items matched by the condition(s). When checked, if no condition has the same subject, then this command will definitely be executed without any limitations."),
            'filter_when'                           => _wpcc('When'),
            'filter_setting_explanations'           => _wpcc('Filter Setting Explanations'),
            'executed'                              => _wpcc('Executed'),
            'not_executed'                          => _wpcc('Not executed'),
            'used_memory'                           => _wpcc('Used memory'),
            'elapsed_time'                          => _wpcc('Elapsed time'),
            'condition_met'                         => _wpcc('This condition is met'),
            'condition_not_met'                     => _wpcc('This condition is not met'),
            'messages'                              => _wpcc('Messages'),
            'subjects'                              => _wpcc('Subjects'),
            'modified_subjects'                     => _wpcc('Modified subjects'),
            'denied_subjects'                       => _wpcc('Denied subjects'),
            'settings'                              => _wpcc('Settings'),
            'message_count'                         => _wpcc('Message count'),
            'subject_count'                         => _wpcc('Subject count'),
            'denied_subject_count'                  => _wpcc('Denied subject count'),

            'clone'                                 => _wpcc('Clone'),
            'move_up'                               => _wpcc('Move up'),
            'move_down'                             => _wpcc('Move down'),
            'move_in'                               => _wpcc('Move in'),
            'move_out'                              => _wpcc('Move out'),
            'toggle_expand_all'                     => _wpcc('Toggle expand all'),
            'toggle_side_by_side'                   => _wpcc('Toggle side-by-side'),
            'toggle_filter_enabled'                 => _wpcc('Enable/disable'),
            'side_by_side'                          => _wpcc('Side by side'),

            'previous'                              => _wpcc("Previous"),
            'next'                                  => _wpcc("Next"),
            'complete'                              => _wpcc("Complete"),
            'cancel'                                => _wpcc("Cancel"),
            'skip'                                  => _wpcc("Skip"),
            'enable_x_tab'                          => _wpcc("Enable %s tab"),
            'fix_this_error'                        => _wpcc("Please fix this error"),
            'fix_these_errors'                      => _wpcc("Please fix these errors"),
            'start_guide'                           => _wpcc('Start the guide'),
            'start_from_this_step'                  => _wpcc('Start from this step'),
            'must_enable_tab_by_clicking'           => _wpcc('You must enable this tab by clicking to it'),
            'must_not_enable_tab'                   => _wpcc('You must not enable this tab'),
            'enter_valid_url'                       => _wpcc('Please enter a valid URL.'),
            'open_required_x_page_type_for_step'    => _wpcc('This step cannot be shown in this page. Please open %s page and start the step there.'),
            'no_prev_step'                          => _wpcc('There is no previous step.'),
            'prev_step_requires_x_page_type'        => _wpcc('Previous step cannot be shown in this page. Please open %s page and start the previous step there.'),
            'please_select_x'                       => _wpcc('Please select %s'),
            'test_this_command'                     => _wpcc("Test this command. The test considers only the command's options. The options of the subject and the property are not considered. Hover over the selected command to highlight its options."),

            'no_name'    => _wpcc('(No name)'),
            'page_names' => [
                PageType::SITE_LISTING     => _wpcc('All Sites'),
                PageType::SITE_SETTINGS    => _wpcc('Site Settings'),
                PageType::ADD_NEW_SITE     => _wpcc('Add New Site'),
                PageType::DASHBOARD        => _wpcc('Dashboard'),
                PageType::SITE_TESTER      => _wpcc('Tester'),
                PageType::TOOLS            => _wpcc('Tools'),
                PageType::GENERAL_SETTINGS => _wpcc('General Settings'),
            ],

            'validation' => [
                'must_check_checkbox'           => _wpcc('You must check the checkbox.'),
                'must_uncheck_checkbox'         => _wpcc('You must uncheck the checkbox.'),
                'value_should_be_int'           => _wpcc('The value should be an integer.'),
                'value_not_valid'               => _wpcc('The value is not valid.'),
                'format_not_correct_x_regex'    => _wpcc("Value's format is not correct. (It must match the pattern <code class=\"regex\">%s</code>)"),
                'enter_valid_url_x_regex'       => _wpcc('Enter a valid URL. (It must match the pattern <code class="regex">%s</code>)'),
                'value_must_start_with'         => _wpcc('Value must start with <code>%s</code>.'),
                'remove_duplicate_urls'         => _wpcc('Please remove the duplicate URLs which are highlighted'),
                'value_not_valid_values_x'      => _wpcc('The value is not valid. Valid values: %s'),
                'value_len_between_min_x_max_y' => _wpcc('Length of the value must be between <code class="number">{0}</code> and <code class="number">{1}</code>.'),
                'value_len_gt_or_eq_x'          => _wpcc('Length of the value must be greater than or equal to <code class="number">%s</code>.'),
            ],

            // Variables that are not localization values and that should be available for use by JavaScript
            'vars' => [
                'docs_label_index_url' => Docs::getInstance()->getLocalLabelIndexFileUrl(),
                'docs_site_url'        => Docs::getInstance()->getDocumentationBaseUrl(),
            ]
        ];

        $values = array_merge($values, GuideTranslations::getInstance()->getTranslations());

        return $values;
    }

    /*
     *
     */

    /**
     * Add app.js
     * @since 1.10.0
     */
    public function addApp(): void {
        $this->addAnimate();
        $this->addjQueryAnimationAssets();
        $this->addScript($this->scriptApp, $this->scriptPath('app.js'), ['jquery', $this->scriptUtils], false, true);
    }

    /**
     * Add post-settings.css, app.js and utils.js, along with the site settings assets of the registered detail
     * factories.
     */
    public function addPostSettings(): void {
        $this->addSortable();

        $this->addStyle($this->stylePostSettings, $this->stylePath('post-settings.css'));
        $this->addFontAwesome();

        $this->addUtils();
        $this->addSelect2();

        $this->addApp();
    }

    /**
     * Add tooltip.js
     */
    public function addTooltip(): void {
        // Utils is required because it defines emulateTransitionEnd function for jQuery. This function is required for
        // tooltip to work.
        $this->addScript($this->scriptTooltip, $this->publicPath('scripts/tooltip.min.js'), ['jquery', $this->scriptUtils], '3.3.6', true);
    }

    /**
     * Add clipboard.js
     */
    public function addClipboard(): void {
        $this->addScript($this->scriptClipboard, $this->publicPath('scripts/clipboard.min.js'), [], '1.5.9', true);
    }

    /**
     * Add app.js and utils.js
     */
    public function addPostList(): void {
        $this->addUtils();
        $this->addApp();
    }

    /**
     * Add general-settings.css
     */
    public function addGeneralSettings(): void {
        $this->addStyle($this->styleGeneralSettings, $this->stylePath('general-settings.css'));
    }

    /**
     * Add site-tester.css, app.js and utils.js, along with the site tester assets of the registered detail factories.
     */
    public function addSiteTester(): void {
        $this->addStyle($this->styleSiteTester, $this->stylePath('site-tester.css'));
        $this->addFontAwesome();
        $this->addUtils();

        $this->addApp();

        // Add tester assets of the registered factories
        PostDetailsService::getInstance()->addSiteTesterAssets();
    }

    /**
     * Add tools.css, app.js and utils.js
     */
    public function addTools(): void {
        $this->addStyle($this->styleTools, $this->stylePath('tools.css'));
        $this->addUtils();
        $this->addTooltip();
        $this->addFormSerializer();

        $this->addApp();
    }

    /**
     * Add dashboard.css and app.js
     */
    public function addDashboard(): void {
        $this->addStyle($this->styleDashboard, $this->stylePath('dashboard.css'));
        $this->addApp();
    }

    /**
     * Add app.js and dev-tools.css
     */
    public function addDevTools(): void {
        $this->addStyle($this->styleDevTools, $this->stylePath('dev-tools.css'));

        // Add the lightbox library after the dev-tools style so that we can override the styles of the library.
        // Also, the lib should be added before the dev-tools script so that we can refer to the lib's script.
        $this->addFeatherlight();

        $this->addScript($this->scriptOptimalSelect, $this->publicPath('node_modules/optimal-select/dist/optimal-select.js'), [], false, true);
        $this->addScript($this->scriptJSDetectElementResize, $this->publicPath('node_modules/javascript-detect-element-resize/jquery.resize.js'), ['jquery'], false, true);

        $this->addApp();

    }

    /**
     * Add app.js and options-box.css
     */
    public function addOptionsBox(): void {
        $this->addStyle($this->styleOptionsBox, $this->stylePath('options-box.css'));

        $this->addFormSerializer();

        $this->addApp();
    }

    /**
     * Add the assets required to display the media selection dialog of WordPress
     * @since 1.12.0
     */
    public function addMediaEditor(): void {
        wp_enqueue_media();
    }

    /**
     * Add featherlight.css and featherlight.js
     */
    public function addFeatherlight(): void {
        $this->addStyle($this->styleFeatherlight, $this->publicPath('node_modules/featherlight/release/featherlight.min.css'));
        $this->addScript($this->scriptFeatherlight, $this->publicPath('node_modules/featherlight/release/featherlight.min.js'), ['jquery'], false, true);
    }

    /**
     * Add utils.js
     */
    public function addUtils(): void {
        $this->addScript($this->scriptUtils, $this->publicPath('scripts/utils.js'), ['jquery'], false, true);
    }

    /**
     * Adds bootstrap-grid.css
     */
    public function addBootstrapGrid(): void {
        $this->addStyle($this->styleBootstrapGrid, $this->publicPath('styles/bootstrap-grid.css'));
    }

    /**
     * Adds WordPress' default jquery UI sortable library
     */
    public function addSortable(): void {
        $this->addScript('jquery-ui-sortable', null, [], false, true);
    }

    /**
     * Adds jquery.serialize-object.min.js
     */
    public function addFormSerializer(): void {
        $this->addScript($this->scriptFormSerializer, $this->publicPath('node_modules/form-serializer/dist/jquery.serialize-object.min.js'), ['jquery'], false, true);
    }

    /**
     * Adds animate.min.css
     * @since 1.8.0
     */
    public function addAnimate(): void {
        $this->addStyle($this->styleAnimate, $this->publicPath('node_modules/animate.css/animate.min.css'));
    }

    /**
     * Adds feature-request.css and app.js
     * @since 1.9.0
     */
    public function addFeatureRequest(): void {
        $this->addStyle($this->styleFeatureRequest, $this->stylePath('feature-request.css'));
        $this->addApp();
    }

    /**
     * Adds select2.css and select2.min.js
     * @since 1.9.0
     */
    public function addSelect2(): void {
        $this->addStyle($this->styleSelect2, $this->publicPath('node_modules/select2/dist/css/select2.min.css'));
        $this->addScript($this->scriptSelect2, $this->publicPath('node_modules/select2/dist/js/select2.min.js'), ['jquery'], false, true);
    }

    /**
     * Adds shepherd.min.js and app.js
     * @since 1.9.0
     */
    public function addGuides(): void {
        $this->addStyle($this->styleGuides, $this->stylePath('guides.css'));
        $this->addStyle($this->styleShepherd, $this->publicPath('node_modules/shepherd.js/dist/css/shepherd.css'));
        $this->addApp();
    }

    /**
     * Add FontAwesome CSS files
     * @since 1.11.0
     */
    public function addFontAwesome(): void {
        $this->addStyle($this->styleFontAwesome,      $this->publicPath('lib/fontawesome/css/fontawesome.min.css'));
        $this->addStyle($this->styleFontAwesomeSolid, $this->publicPath('lib/fontawesome/css/solid.min.css'));
    }

    /*
     *
     */

    /**
     * Get contents of the iframe style file.
     *
     * @return string|null
     * @since 1.9.0
     */
    public function getDevToolsIframeStyle(): ?string {
        return $this->getFileContent($this->stylePath('dev-tools-iframe.css'));
    }

    /**
     * Get contents of info.css
     *
     * @return string|null
     */
    public function getInformationStyle(): ?string {
        return $this->getFileContent($this->stylePath('info.css'));
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * Get contents of a file in app directory of the plugin
     *
     * @param string $absPath Absolute path of the file
     * @return null|string Contents of the file if the file exists. Otherwise, null.
     * @since 1.9.0
     */
    private function getFileContent($absPath): ?string {
        $fs = FileService::getInstance()->getFileSystem();

        if (!$fs->exists($absPath) || !$fs->isFile($absPath)) {
            Informer::addError(sprintf(_wpcc('File "%1$s" could not be found.'), $absPath))->addAsLog();
            return null;
        }

        try {
            return $fs->get($absPath);

        } catch (FileNotFoundException $e) {
            Informer::addError($e->getMessage())->setException($e)->addAsLog();
            return null;
        }
    }

    private function addjQueryAnimationAssets(): void {
        // These are required for using animate feature of jQuery.
        $this->addScript('jquery-ui-core');
        $this->addScript('jquery-color');
    }
}