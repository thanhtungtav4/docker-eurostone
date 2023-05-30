<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/01/2020
 * Time: 17:36
 *
 * @since 1.10.0
 */

namespace WPCCrawler\Objects\Guides;

use Illuminate\Support\Arr;
use WPCCrawler\Objects\Enums\InnerItemSelector;
use WPCCrawler\Objects\Enums\PageType;
use WPCCrawler\Objects\Enums\TabKey;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Settings\Enums\SettingKey;

/**
 * Stores the translations for the guides
 */
class GuideTranslations {

    /** @var GuideTranslations */
    private static $instance;

    /**
     * Get the instance
     *
     * @return GuideTranslations
     * @since 1.10.0
     */
    public static function getInstance() {
        if (static::$instance === null) {
            static::$instance = new GuideTranslations();
        }

        return static::$instance;
    }

    /**
     * This is a singleton
     * @since 1.10.0
     */
    protected function __construct() { }

    /**
     * @return array All translations related to the guides
     * @since 1.10.0
     */
    public function getTranslations() {
        return [
            'guides'            => _wpcc('Guides'),
            'search_guides'     => _wpcc('Search guides...'),
            'guide_info'        => $this->getGuideInfo(),

            // "sk" is the short for "setting key". These store setting-key-related step details
            'sk_step_contents'  => $this->getSettingStepContents(),
            'sk_step_titles'    => $this->getSettingStepTitles(),

            'tab_step_contents' => $this->getTabStepContents(),

            // "isk" is the short for "inner setting key". These store details of steps targeting inner settings
            'isk_step_info'     => $this->getInnerSettingStepInfo(),
            'isk_step_def_info' => $this->getInnerSettingStepDefaultInfo(),

            // "g" is the short for "general". These store general step details
            'g_step_info'       => $this->getGeneralStepInfo(),

            'page_step_info'    => $this->getPageStepInfo(),

            'custom_step_info'  => $this->getCustomStepInfo(),
        ];
    }

    /*
     * PRIVATE TRANSLATION-CREATING METHODS
     */

    /**
     * @return array Translations for contents of setting keys. The array is structured as key-value pairs where key is
     *               one of the constants defined in {@link SettingKey} and its value is the setting step's content,
     *               a string.
     * @since 1.10.0
     */
    private function getSettingStepContents() {
        $howToGetItDocs = " " . _wpcc("You can find out how to get it in the plugin's documentation.");

        return [
            // SITE SETTINGS
            // Main tab
            SettingKey::MAIN_PAGE_URL => _wpcc('Enter the URL of the site whose posts you want to save to your site. The URL should be entered like <code>https://domain.com/</code> instead of <code class="line-through">https://domain.com/abc/def</code>'),
            SettingKey::ACTIVE => _wpcc('This activates automatic crawling. Check this checkbox.'),
            SettingKey::ACTIVE_RECRAWLING => _wpcc('This activates automatic updating. When this is checked, it means this site is suitable for automatic recrawling. Check this checkbox.'),
            SettingKey::ACTIVE_TRANSLATION => _wpcc('This activates automatic translation for this site. Check this checkbox.'),
            SettingKey::COOKIES => _wpcc("This setting is used to define cookies. Enter the cookies as name-content pairs here. You can get the target site's cookies from your browser and then enter those cookies here. The plugin will add these cookies to every request made to the target site."),
            SettingKey::DO_NOT_USE_GENERAL_SETTINGS => _wpcc('By checking this, you tell the plugin that this site will use custom general settings. Check this. When this is checked, a new tab will appear.'),

            // Category tab
            SettingKey::CATEGORY_MAP => _wpcc('This setting stores the category URLs that will be crawled to find URLs of the posts that will be saved.'),
            SettingKey::TEST_URL_CATEGORY => _wpcc('Enter a category URL here. It can be one of the previously defined category URLs. We are going to use this to test our settings.'),
            SettingKey::CATEGORY_ADD_CATEGORY_URLS_WITH_SELECTOR => _wpcc('Check this checkbox to display the settings we will use to add category URLs automatically.'),
            SettingKey::CATEGORY_LIST_PAGE_URL => _wpcc('Enter a URL of a page containing the category URLs that you want to add.'),
            SettingKey::CATEGORY_LIST_URL_SELECTORS => _wpcc('This setting is used to define a CSS selector that will be used to find the category URLs in the target page and then add them.'),

            // Post tab
            SettingKey::TEST_URL_POST => _wpcc('Enter a post URL here. We are going to use this to test our settings.'),
            SettingKey::POST_TITLE_SELECTORS => _wpcc("Let's tell the plugin how to find the title of a post in the target post page."),
            SettingKey::POST_CONTENT_SELECTORS => _wpcc("Let's tell the plugin how to find the content of a post in the target post page."),

            SettingKey::POST_CATEGORY_NAME_SELECTORS => _wpcc('This setting is used to tell the plugin how to find names of the categories in the target post pages'),
            SettingKey::POST_CATEGORY_ADD_HIERARCHICAL => _wpcc('If you want to add the found categories hierarchically, i.e. as subcategories of each other, check this checkbox'),
            SettingKey::POST_CATEGORY_DO_NOT_ADD_CATEGORY_IN_MAP => _wpcc('By default, the plugin will create the categories under the category selected in Category URLs setting. If you do not want that category to be the parent of the new categories, check this.'),

            SettingKey::POST_SAVE_ALL_IMAGES_IN_CONTENT => _wpcc('When this checkbox is checked, images existing in the content of the post will be saved to your server.'),

            SettingKey::POST_SAVE_THUMBNAILS_IF_NOT_EXIST => _wpcc('Check this checkbox to tell the plugin that the featured image should be saved.'),
            SettingKey::POST_THUMBNAIL_SELECTORS => _wpcc('This setting is used to define a CSS selector that finds the featured image in the target post page and prepare its value.'),

            SettingKey::POST_EXCHANGE_ELEMENT_ATTRIBUTES => _wpcc('This setting is used to exchange values of two attributes of an element.'),
            SettingKey::POST_UNNECESSARY_ELEMENT_SELECTORS => _wpcc('This setting is used to remove elements from the source code retrieved from the target website.'),

            // Templates tab
            SettingKey::POST_REMOVE_LINKS_FROM_SHORT_CODES => _wpcc('When this is checked, all the links, except for the links added by you manually into the main template, will be removed. Check this.'),
            SettingKey::POST_CONVERT_IFRAMES_TO_SHORT_CODE => _wpcc('When this checkbox is checked, the plugin will convert each <code>iframe</code> element into a short code. By this way, it will be possible to show the <code>iframe</code> elements. Check this checkbox.'),

            // Dev Tools
            SettingKey::DEV_TOOLS_CSS_SELECTOR => _wpcc('The found CSS selector is shown here. You can also enter a CSS selector manually.'),

            // GENERAL SETTINGS
            SettingKey::WPCC_IS_SCHEDULING_ACTIVE => _wpcc('Activate scheduling. When it is active, the plugin will automatically find posts and save them into your site in the background.'),
            SettingKey::WPCC_IS_RECRAWLING_ACTIVE => _wpcc('Activate automatic updating. When it is active, the plugin will automatically update posts that match the defined criteria.'),
            SettingKey::WPCC_INTERVAL_URL_COLLECTION => _wpcc('Define a time interval for URL collection. For example, if you set it to 1 minute, the plugin will check a category page every minute to find new post URLs.'),
            SettingKey::WPCC_INTERVAL_POST_CRAWL => _wpcc('Define how much time apart a post should be saved to your site. For example, if you set it to 1 minute, every minute the plugin will save one of the posts whose URL was saved with URL collection event.'),
            SettingKey::WPCC_INTERVAL_POST_RECRAWL => _wpcc('Define how much time apart the recrawling event should be run. For example, if you set it to 1 minute, every minute the plugin will check the posts existing in your site, and, update the ones that match the defined criteria.'),
            SettingKey::WPCC_MIN_TIME_BETWEEN_TWO_RECRAWLS_IN_MIN => _wpcc('Define how many minutes apart the updating should occur. For example, if you set this to 1440 minutes (1 day), it means a post can only be updated if it was not updated in the last 1440 minutes.'),
            SettingKey::WPCC_RECRAWL_POSTS_NEWER_THAN_IN_MIN => _wpcc('Define how fresh a post should be so that it is suitable for updating. For example, if you set this to 43200 minutes (1 month), the posts older than 1 month will not be updated.'),

            // Post tab
            SettingKey::WPCC_ALLOWED_IFRAME_SHORT_CODE_DOMAINS => _wpcc('<code>iframe</code> elements are shown only from allowed sources, because allowing all sources poses a security risk. You need to allow the domains. To allow a domain and all of its subdomains, configure <b>two inputs</b> as <code>domain.com</code> and <code>*.domain.com</code>.')
                . ' ' . _wpcc('For example, for YouTube, you can enter <code>youtube.com</code> and <code>*.youtube.com</code>.'),

            // Translation tab
            SettingKey::WPCC_IS_TRANSLATION_ACTIVE => _wpcc('Activate translation. If this is not checked, no post will be translated, even if the translation is active in the site settings.'),
            SettingKey::WPCC_SELECTED_TRANSLATION_SERVICE => _wpcc('Select a service. The selected service will be used to translate the posts.'),
            SettingKey::WPCC_TRANSLATION_YANDEX_TRANSLATE_API_KEY => _wpcc("Enter the API key you get from Yandex Translate service.") . $howToGetItDocs,
            SettingKey::WPCC_TRANSLATION_GOOGLE_TRANSLATE_PROJECT_ID => _wpcc("Enter the project ID you get from Google Cloud Translation service.") . $howToGetItDocs,
            SettingKey::WPCC_TRANSLATION_GOOGLE_TRANSLATE_API_KEY => _wpcc("Enter the API key you get from Google Cloud Translation service.") . $howToGetItDocs,
            SettingKey::WPCC_TRANSLATION_MICROSOFT_TRANSLATOR_TEXT_CLIENT_SECRET => _wpcc("Enter the secret key you get from Microsoft Translator Text service.") . $howToGetItDocs,
            SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_ACCESS_KEY => _wpcc("Enter the access key you get from Amazon Translate service.") . $howToGetItDocs,
            SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_SECRET => _wpcc("Enter the secret you get from Amazon Translate service.") . $howToGetItDocs,
            SettingKey::WPCC_TRANSLATION_AMAZON_TRANSLATE_REGION => _wpcc("Select the region to which the requests will be sent. You can select the region closest to your server's location."),

            // TOOLS
            SettingKey::WPCC_TOOLS_SITE_ID => _wpcc('Select the site you want to manually crawl'),
            SettingKey::WPCC_TOOLS_CATEGORY_ID => _wpcc('Select the category into which the saved posts will be saved'),
            SettingKey::WPCC_TOOLS_POST_URLS => _wpcc('Enter URL of the post you want to save. If you want to save multiple posts, write each URL in a different line.'),
            SettingKey::WPCC_TOOLS_RECRAWL_POST_ID => _wpcc('Enter the ID of the post that you want to update by recrawling it'),
        ];
    }

    /**
     * @return array Translations for contents of setting keys. The array is structured as key-value pairs where key is
     *               one of the constants defined in {@link SettingKey} and its value is the setting step's title,
     *               a string.
     * @since 1.10.0
     */
    private function getSettingStepTitles() {
        // By default, we use the label of the setting as the step title in the UI. If a specific title needs to be
        // entered, a title can be specified here. If it is not specified, the default value will be used.
        return [
            SettingKey::DEV_TOOLS_CSS_SELECTOR => _wpcc('CSS Selector'),
        ];
    }

    /**
     * @return array Translations for contents of tab keys. The array is structured as key-value pairs where key is
     *               one of the constants defined in {@link TabKey} and its value is the tab step's content, a string.
     * @since 1.10.0
     */
    private function getTabStepContents() {
        return [
            TabKey::SITE_SETTINGS_TAB_MAIN => _wpcc('Activate the main tab'),
            TabKey::SITE_SETTINGS_TAB_CATEGORY => _wpcc('Category tab is mainly used to define how to find URLs of the posts we are going to save.'),
            TabKey::SITE_SETTINGS_TAB_POST => _wpcc('Post tab is used to define how to find post details such as title and content.'),
            TabKey::SITE_SETTINGS_TAB_TEMPLATES => _wpcc('Templates tab is used to prepare the template of the post content and other details about the post, such as post title, tags, and so on.'),
            TabKey::SITE_SETTINGS_TAB_GENERAL_SETTINGS => _wpcc('Settings tab is used to define custom general settings for this site.'),

            TabKey::GENERAL_SETTINGS_TAB_POST => _wpcc('Post tab is used to define post-related general settings. Activate this tab.'),
            TabKey::GENERAL_SETTINGS_TAB_TRANSLATION => _wpcc('Translation tab stores the settings related to translation. Activate this tab.'),

            TabKey::TOOLS_TAB_MANUAL_CRAWLING => _wpcc('This tab contains the manual crawling tool. Activate this tab.'),
            TabKey::TOOLS_TAB_MANUAL_RECRAWLING => _wpcc('This tab contains the manual recrawling (updating) tool. Activate this tab.'),
        ];
    }

    /**
     * @return array Step information for inner settings. Key-value pairs, each created using {@link innerInfo()}.
     * @since 1.10.0
     */
    private function getInnerSettingStepInfo() {
        return Arr::collapse([
            $this->innerInfo(SettingKey::CATEGORY_MAP, SettingInnerKey::URL, _wpcc('Category URL'),
                _wpcc('Enter the URL of a category page from target site. The plugin will look for post URLs in the given page, to be able to find posts automatically. So, enter URL of a page that lists many posts.')),

            $this->innerInfo(SettingKey::CATEGORY_MAP, SettingInnerKey::CATEGORY_ID, _wpcc('Category'),
                _wpcc('Select a category. The posts found in the given category URL will be saved into this category.')),
        ]);
    }

    /**
     * @return array Default step information for inner settings. Key-value pairs, where keys are one of the constants
     *               defined in {@link SettingInnerKey} or {@link InnerItemSelector} and the values are created by using
     *               {@link info()}. See the method for more information about the structure of the value.
     * @since 1.10.0
     */
    private function getInnerSettingStepDefaultInfo() {
        return Arr::collapse([
            $this->info(InnerItemSelector::BUTTON_DEV_TOOLS, _wpcc('Open Visual Inspector'),
                _wpcc('Visual Inspector is used to easily find CSS selectors by clicking to items existing in the target site. Click this button to open Visual Inspector for this setting.')),

            $this->info(InnerItemSelector::BUTTON_TEST, _wpcc('Test the configuration'),
                _wpcc("This button tests the setting's configuration. Click this button.")),

            $this->info(InnerItemSelector::BUTTON_ADD_CATEGORY_URLS, _wpcc('Add the category URLs'),
                _wpcc("When this button is clicked, the URLs found by the given selector will be added to the category URLs setting. Click this button.")),

            $this->info(InnerItemSelector::TEST_RESULTS_CONTAINER, _wpcc('Test results'),
                _wpcc("Test results are shown here. Make sure the results are OK.")),

            $this->info(InnerItemSelector::DEV_TOOLS_IFRAME, _wpcc('Click to an element'),
                _wpcc('Click to an element so that Visual Inspector finds a CSS selector that can be used to find the element in the target page.')),

            $this->info(InnerItemSelector::DEV_TOOLS_BTN_USE_SELECTOR, _wpcc('Use CSS selector'),
                _wpcc("When this button is clicked, the found CSS selector is copied as the setting's value. Click this button.")),

        $this->info(InnerItemSelector::DEV_TOOLS_SELECTION_BEHAVIOR, _wpcc('Selection behavior'),
                _wpcc("Set how to find CSS selectors. If you set this to <code>Similar</code>, a CSS selector that finds elements similar to the one you click will be found.")
                . ' ' . _wpcc('If you select <code>Unique</code>, a CSS selector selecting only the element you click will be found.')
                . ' ' . _wpcc('If you select <code>Contains</code>, a CSS selector for the element that contains all of the clicked elements will be found.')),
        ]);
    }

    /**
     * @return array A key-value pair where keys are one of the constants defined in {@link GeneralStepId} and
     *               the values are created via {@link info()}. See that method's docs for more information.
     * @since 1.10.0
     */
    private function getGeneralStepInfo() {
        return Arr::collapse([
            // SITE LISTING
            $this->info(GeneralStepId::BTN_ADD_NEW_SITE, _wpcc('Add New Site'),
                _wpcc('Click this button to create a new site')),

            // SITE SETTINGS
            $this->info(GeneralStepId::INPUT_SITE_TITLE, _wpcc('Site Title'),
                _wpcc("Enter a name for this site. You can enter any name. Make it short and understandable for you. If a site's URL is <code>http://an-awesome-site.com</code>, you can enter <b><i>An awesome site</i></b> for the title.")),

            $this->info(GeneralStepId::BTN_PUBLISH_UPDATE, _wpcc('Publish/update the settings'),
                _wpcc('Click this button to to save the settings and publish the site. Always make sure the settings are published, because the plugin considers only the published sites.')),

            $this->info(GeneralStepId::BTN_QUICK_SAVE_SITE_SETTINGS, _wpcc('Quick-save the settings'),
                _wpcc('Click this button to quickly save the settings. This saves the settings without needing to reload the page.')),

            $this->info(GeneralStepId::BTN_LOAD_GENERAL_SETTINGS, _wpcc('Load general settings'),
                _wpcc('Click this button to fill all the settings below by copying their values from the currently set general settings.')),

            $this->info(GeneralStepId::TAB_CONTENT_GENERAL_SETTINGS, _wpcc('Custom general settings'),
                _wpcc('Define custom general settings here. When needed, these settings will be used instead of the general settings.')),

            $this->info(GeneralStepId::SECTION_WOOCOMMERCE, _wpcc('Configure WooCommerce settings'),
                _wpcc("Configure the WooCommerce settings here. This section's design is quite similar to the design of WooCommerce's product settings. So, you already know the purpose of these settings since you are familiar with the settings of WooCommerce products.")),

            // GENERAL SETTINGS
            $this->info(GeneralStepId::BTN_SAVE_GENERAL_SETTINGS, _wpcc('Save the general settings'),
                _wpcc('Click here. This button saves the general settings.')),

            // DASHBOARD
            $this->info(GeneralStepId::DASHBOARD_ACTIVE_SITES_SECTION, _wpcc('Active sites'),
                _wpcc('This section shows the active sites. You can see how many posts and URLs are saved, in addition to other details such as active events.')),

            $this->info(GeneralStepId::DASHBOARD_WHATS_HAPPENING_SECTION, _wpcc('See what is happening'),
                _wpcc('This section shows the times of the last and the next events, as well as number of posts and URLs saved to your site.')),

            $this->info(GeneralStepId::DASHBOARD_LAST_CRAWLED_POSTS_SECTION, _wpcc('Recently saved posts'),
                _wpcc('You can track the recently saved posts in this section.')),

            $this->info(GeneralStepId::DASHBOARD_REFRESH_INPUT, _wpcc('Auto-refresh the page'),
                _wpcc('Set how many seconds apart the page should be refreshed. By this way, you can see the new posts and updates to other information available in the page.')),

            // TESTER
            $this->info(GeneralStepId::TESTER_SELECT_SITE_NAME, _wpcc('Select site'), _wpcc('Select the site whose settings you want to test')),
            $this->info(GeneralStepId::TESTER_SELECT_TEST_TYPE, _wpcc('Test type'), _wpcc('Select which type of page you want to test')),
            $this->info(GeneralStepId::TESTER_CONTAINER_TEST_URL, _wpcc('Test URL'), _wpcc("Define a URL. The plugin will crawl this URL by using the selected site's settings.")),
            $this->info(GeneralStepId::TESTER_BTN_TEST, _wpcc('Run the test'), _wpcc('Click this button. This button runs the test.')),
            $this->info(GeneralStepId::TESTER_TEST_RESULTS_CONTAINER, _wpcc('Test results'),
                _wpcc('Test results are shown here. Observe the results and check to see if everything is as you expected. For example, you can check if the found URLs work by clicking to them or you can check if all the expected information is there.')
                . '<br><br>' . _wpcc('You can also check if there are any errors displayed. If so, you can change the site settings to fix them and re-run the test to see if the errors are gone.')),

            // TOOLS
            $this->info(GeneralStepId::BTN_MANUAL_CRAWLING_TOOL_CRAWL_NOW, _wpcc('Start crawling'), _wpcc('Click this button to crawl the URLs.')),
            $this->info(GeneralStepId::MANUAL_CRAWLING_TOOL_RESULTS_CONTAINER, _wpcc('Results'), _wpcc('You can observe the crawling progress and results here. Keep your browser open until all URLs are finished being crawled.')),
            $this->info(GeneralStepId::BTN_MANUAL_RECRAWLING_TOOL_RECRAWL, _wpcc('Update the post'), _wpcc('Click this button to start the recrawling')),
            $this->info(GeneralStepId::MANUAL_RECRAWLING_TOOL_RESULTS_CONTAINER, _wpcc('Results'), _wpcc('You can check the recrawling results here')),

            // OTHER
            $this->info(GeneralStepId::BUTTON_DISPLAY_GUIDES, _wpcc('Display guides'), _wpcc('Guides help you learn how to do certain things with the plugin. Click here to see all the guides and start them whenever you feel a need.')),
        ]);
    }

    /**
     * @return array A key-value pair where keys are one of the constants defined in {@link PageType} and
     *               the values are created via {@link info()}. See that method's docs for more information.
     * @since 1.10.0
     */
    private function getPageStepInfo() {
        return Arr::collapse([
            $this->info(PageType::ADD_NEW_SITE, _wpcc('Add a new site'), _wpcc('Click here to create a new site')),
            $this->info(PageType::GENERAL_SETTINGS, _wpcc('Open General Settings page'), _wpcc('General Settings page contains scheduling, translation, spinning, and other general options.')),
            $this->info(PageType::DASHBOARD, _wpcc('Open Dashboard page'), _wpcc('Dashboard page lets you track active sites and saved posts.')),
        ]);
    }

    /**
     * Create custom step information.
     *
     * @return array A key-value pair where keys are one of the constants defined in {@link CustomStepInfoKey} and
     *               values are created via {@link info()}.
     * @since 1.10.0
     */
    private function getCustomStepInfo() {
        return Arr::collapse([
            $this->info(CustomStepInfoKey::LETS_GET_YOU_STARTED_ADD_NEW_SITE, _wpcc('Welcome to WP Content Crawler'),
                _wpcc("Let's get you started with the plugin by adding a new site to save posts automatically. Click here to create a new site.")),

            $this->info(CustomStepInfoKey::DEV_TOOLS_CLICK_POST_LINK, _wpcc('Click to a post link'),
                _wpcc('The plugin needs to be able to find URLs of the posts. So, click to a link of a post so that a CSS selector for post URLs can be found.')),

            $this->info(CustomStepInfoKey::DEV_TOOLS_CLICK_POST_TITLE, _wpcc('Click to the post title'),
                _wpcc('Click to the title of the post so that a CSS selector for the post title can be found.')),

            $this->info(CustomStepInfoKey::DEV_TOOLS_CLICK_POST_CONTENT, _wpcc('Click to the post content'),
                _wpcc('Click to the content of the post to find a CSS selector. You can click more than one part of the content. Visual Inspector will try to find the element containing all of the clicked elements.')),

            $this->info(CustomStepInfoKey::DEV_TOOLS_CLICK_CATEGORY_LINK, _wpcc('Click to a category link'),
                _wpcc('Click to a category link to find a CSS selector that finds the category URLs you want to add.')),

            $this->info(CustomStepInfoKey::DEV_TOOLS_CLICK_CATEGORY_NAME, _wpcc('Click to a category name'),
                _wpcc('Click to a category name to find a CSS selector that finds the category names you want to add.')),

            $this->info(CustomStepInfoKey::DEV_TOOLS_CLICK_FEATURED_IMAGE, _wpcc('Click to the featured image'),
                _wpcc('Click to the image that you want to save as a featured image so that a CSS selector that finds the image can be found.')),

            $this->info(CustomStepInfoKey::DEV_TOOLS_CLICK_UNNECESSARY_ELEMENT, _wpcc('Click to an unnecessary element'),
                _wpcc('Click to the element or elements that you want to remove so that a CSS selector that matches those elements can be found.')),

            $this->info(CustomStepInfoKey::OBSERVE_FOUND_POST_URLS, _wpcc('Observe the found post URLs'),
                _wpcc('These are the URLs of the posts that will be saved. Make sure these are the URLs you want and make sure that they work when opened in your browser. You can just copy one of the URLs and open it in your browser to make sure they work.')),

            $this->info(CustomStepInfoKey::OBSERVE_ADDED_CATEGORY_URLS, _wpcc('Observe the added URLs'),
                _wpcc('Found URLs are added here. Take a look at the automatically added category URLs and remove unnecessary or wrong ones.')),

            $this->info(CustomStepInfoKey::OBSERVE_FOUND_POST_TITLE, _wpcc('Observe the found post title'),
                _wpcc('Make sure the first item in the results is the post title you want')),

            $this->info(CustomStepInfoKey::OBSERVE_FOUND_UNNECESSARY_ELEMENTS, _wpcc('Observe the found elements'),
                _wpcc('Observe the elements shown in the results. These elements will be removed. Make sure all found elements are suitable to be removed.')),

            $this->info(CustomStepInfoKey::OBSERVE_FOUND_FEATURED_IMAGE_URL, _wpcc('Observe the found featured image URL'),
                _wpcc('Observe the found featured image URL or URLs. Make sure the first URL in the results works and it is the URL of the image that you want to save as the featured image.')),

            $this->info(CustomStepInfoKey::POST_TYPE_WOOCOMMERCE_PRODUCT, _wpcc('Select WooCommerce post type'),
                _wpcc('Select <code>product</code> here. It is the post type used by the WooCommerce products. If the value is not available, then you need to install WooCommerce first.')),

            $this->info(CustomStepInfoKey::CATEGORY_MAP_WOOCOMMERCE_CATEGORY, _wpcc('Select WooCommerce categories'),
                _wpcc('Make sure WooCommerce categories are selected here. If the selected category is not a WooCommerce category, then the products will be saved into the default WooCommerce category.')),

            // Lazy-loaded images
            $this->info(CustomStepInfoKey::SHOW_LAZY_LOADED_IMAGES_SELECTOR, _wpcc('Enter image selector'),
                _wpcc('First, you need a CSS selector for the image elements. Write <code>img</code> here.')),

            $this->info(CustomStepInfoKey::SHOW_LAZY_LOADED_IMAGES_TEST_RESULTS_1, _wpcc('Find the lazy-load attribute name'),
                _wpcc('Observe the test results and find out the name of the attribute that stores the real image URL. The name of the attribute might be <code>data-src</code>, <code>data-original</code>, <code>data-lazy</code> or a similar one.')),

            $this->info(CustomStepInfoKey::SHOW_LAZY_LOADED_IMAGES_ATTR1, _wpcc('Enter the expected attribute name'),
                _wpcc('Write the name of the attribute the URL should be in. The image URLs must be in <code>src</code> attribute. Write <code>src</code> here.')),

            $this->info(CustomStepInfoKey::SHOW_LAZY_LOADED_IMAGES_ATTR2, _wpcc('Enter the lazy-load attribute name'),
                _wpcc('Enter the name of the attribute you have just found, the name of the attribute that stores the real image URL')),

            $this->info(CustomStepInfoKey::SHOW_LAZY_LOADED_IMAGES_TEST_RESULTS_2, _wpcc('Check the results'),
                _wpcc('Make sure the real image URLs are now in <code>src</code> attribute.')),

            // Translation
            $this->info(CustomStepInfoKey::TRANSLATION_FROM_LANGUAGE, _wpcc('Select the source language'),
                _wpcc('Select the language of the original content. If the languages are not shown, click to "load" button to load the languages.')),

            $this->info(CustomStepInfoKey::TRANSLATION_TO_LANGUAGE, _wpcc('Select the target language'),
                _wpcc('Select the desired language for the content. The contents will be translated to this language.')),

            $this->info(CustomStepInfoKey::TRANSLATION_TEST_TEXT, _wpcc('Enter a test text'),
                _wpcc("Enter a text in the original contents' language. We will use this text to test if the translation settings you have just configured are correct.")),
        ]);
    }

    /**
     * Get the name and description of each guide
     *
     * @return array The names of the guides. This returns a key-value pair where key is one of the constants defined in
     *               {@link GuideId} and the value is an array that contains information about the guide. The value is
     *               created by {@link info()}. See that method's docs for more information.
     * @since 1.10.0
     */
    private function getGuideInfo() {
        $translatingWithX        = _wpcc("Automatically translating posts with %s");
        $translatingWithXContent = _wpcc('Learn how to automatically translate crawled posts and their details by using %s.');

        // Turn array of arrays into a single array so that the keys of the resultant array are GuideId constants.
        return Arr::collapse([
            $this->info(GuideId::SAVING_POSTS_AUTOMATICALLY, _wpcc('Quick Start - Saving posts automatically'),
                _wpcc('Learn how to configure site settings to automatically save title and content of the posts from the target site.')),

            $this->info(GuideId::UPDATING_POSTS_AUTOMATICALLY, _wpcc('Updating posts automatically'),
                _wpcc('Learn how to enable automatic updating for the posts saved by a site.')),

            $this->info(GuideId::TRANSLATING_POSTS_AUTOMATICALLY, _wpcc('Translating posts automatically'),
                _wpcc('Learn how to automatically translate the posts from one language to another.')),

            $this->info(GuideId::TESTING_SITE_SETTINGS, _wpcc('Testing site settings'),
                _wpcc('Before enabling automatic crawling, it is always recommended to test the settings. Learn how to make sure that your site settings work as expected.')),

            $this->info(GuideId::USING_COOKIES, _wpcc('Using cookies'),
                _wpcc('Cookies can be used, for example, to crawl a site as a logged-in user. Learn how to set cookies.')),

            $this->info(GuideId::USING_CUSTOM_GENERAL_SETTINGS, _wpcc('Using custom general settings'),
                _wpcc('Sometimes you need to override general settings for a site. Learn how to do that.')),

            $this->info(GuideId::ADDING_CATEGORY_URLS_AUTOMATICALLY, _wpcc('Adding category URLs automatically'),
                _wpcc('Sometimes the target site has too many categories such that it takes too much time to enter them manually. Learn how to add them automatically.')),

            $this->info(GuideId::SHOWING_IFRAMES_IN_POST_CONTENT, _wpcc('Showing <code>iframe</code>s in post content'),
                _wpcc('WordPress does not let <code>iframe</code> elements be shown since they pose a security risk. However, you might need to show certain <code>iframe</code>s in the post content. Learn how to do that.')),

            $this->info(GuideId::REMOVING_LINKS_IN_POST_CONTENT, _wpcc('Removing links in post content'),
                _wpcc('Sometimes you do not want any links in the post content. Learn how to remove the links.')),

            $this->info(GuideId::CREATING_POST_CATEGORIES_AUTOMATICALLY, _wpcc('Creating post categories automatically'),
                _wpcc("You may want to save the posts into the categories that are the same as the target post's categories. Learn how to automatically create the categories.")),

            $this->info(GuideId::SAVING_IMAGES_IN_POST_CONTENT, _wpcc('Saving images in post content'),
                _wpcc('The plugin does not save any images by default. Learn how to save images in the post content to serve them directly from your site.')),

            $this->info(GuideId::SHOWING_LAZY_LOADED_IMAGES, _wpcc('Showing lazy-loading images'),
                _wpcc('Lazy-loading <code>img</code> elements store the image URL in an attribute other than <code>src</code>. To show them, you should put the URL into <code>src</code> attribute. Learn how to do that.')),

            $this->info(GuideId::REMOVING_UNNECESSARY_ELEMENTS_FROM_POST, _wpcc('Removing unnecessary elements from post'),
                _wpcc('There might be certain elements that you do not want to be saved into any part of the post. Learn how to remove those elements.')),

            $this->info(GuideId::MANUALLY_SAVING_POSTS, _wpcc('Manually saving posts'),
                _wpcc('Sometimes you want to save one or more posts by using their URLs. Learn how to do that.')),

            $this->info(GuideId::MANUALLY_UPDATING_POSTS, _wpcc('Manually updating posts'),
                _wpcc('Sometimes you want to update a post by recrawling it from its source. Learn how to do that.')),

            $this->info(GuideId::SAVING_WOOCOMMERCE_PRODUCTS, _wpcc('Saving WooCommerce products'),
                _wpcc('If you have WooCommerce installed in your site, you might want to save products from other websites into your website as WooCommerce products. Learn how to do that.')),

            $this->info(GuideId::SAVING_FEATURED_IMAGES, _wpcc('Saving featured images of posts'),
                _wpcc('Learn how to save the featured images of the posts.')),

            $this->info(GuideId::TRANSLATING_WITH_YANDEX_TRANSLATE,
                sprintf($translatingWithX, 'Yandex Translate'), sprintf($translatingWithXContent, 'Yandex Translate')),

            $this->info(GuideId::TRANSLATING_WITH_GOOGLE_CLOUD_TRANSLATION,
                sprintf($translatingWithX, 'Google Cloud Translation'), sprintf($translatingWithXContent, 'Google Cloud Translation')),

            $this->info(GuideId::TRANSLATING_WITH_MICROSOFT_TRANSLATOR_TEXT,
                sprintf($translatingWithX, 'Microsoft Translator Text'), sprintf($translatingWithXContent, 'Microsoft Translator Text')),

            $this->info(GuideId::TRANSLATING_WITH_AMAZON_TRANSLATE,
                sprintf($translatingWithX, 'Amazon Translate'), sprintf($translatingWithXContent, 'Amazon Translate')),
        ]);
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * Create information for an inner setting. $key and $innerKey are used to create an ID that will be passed to
     * {@link info()} method.
     *
     * @param string $key      One of the constants defined in {@link SettingKey}
     * @param string $innerKey One of the constants defined in {@link SettingInnerKey} or {@link InnerItemSelector}
     * @param string $name     See {@link info()}
     * @param string $desc     See {@link info()}
     * @return array See {@link info()}
     * @since 1.10.0
     * @uses  info()
     */
    private function innerInfo(string $key, string $innerKey, string $name, string $desc = '') {
        return $this->info("${key}-${innerKey}", $name, $desc);
    }

    /**
     * Create an info with 'name' and 'desc' values
     *
     * @param string $id   A string
     * @param string $name Name (title)
     * @param string $desc A short explanation
     * @return array An array structured as ["id" => ["name" => "...", "desc": "..."]]
     * @since 1.10.0
     */
    private function info(string $id, string $name, string $desc = '') {
        $result = ['name' => $name];
        if ($desc) $result['desc'] = $desc;

        return [$id => $result];
    }
}