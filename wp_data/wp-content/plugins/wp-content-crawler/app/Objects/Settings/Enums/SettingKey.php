<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 16/04/2019
 * Time: 18:42
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Settings\Enums;


/**
 * Stores keys of the settings used in the plugin as constants. The keys in this class consist of the keys of settings
 * stored either in wp_options table or wp_postmeta table. So, keys of both general settings and site settings are here.
 * Purpose of this class is to provide the keys as variables. By using this class, the keys will NOT be hard-coded
 * everywhere in the plugin.
 *
 * @package WPCCrawler\Objects\Settings\Enums
 * @since   1.8.1
 */
class SettingKey {

    // The keys start with an underscore. When a post meta key starts with underscore, the user is not able to edit in
    // the post settings. Since we want the user not to be able to change the settings without we knowing it, we start
    // with an underscore.

    /* ***************************************
     *
     * POST META KEYS (KEYS FOR SITE SETTINGS)
     *
     */

    // Main tab
    const ACTIVE                                     = '_active';                                  // bool     Whether the site is active for being crawled or not
    const ACTIVE_RECRAWLING                          = '_active_recrawling';                       // bool     Whether the site is active for being recrawled or not
    const ACTIVE_POST_DELETING                       = '_active_post_deleting';                    // bool     Whether the site is active for post deleting or not
    const ACTIVE_TRANSLATION                         = '_active_translation';                      // bool     Whether the site is active for post translation or not
    const TRANSLATABLE_FIELDS                        = '_translatable_fields';                     // array    An array of translatable field keys
    const ACTIVE_SPINNING                            = '_active_spinning';                         // bool     Whether the site is active for post spinning or not
    const SPINNABLE_FIELDS                           = '_spinnable_fields';                        // array    An array of spinnable field keys
    const MAIN_PAGE_URL                              = '_main_page_url';                           // string   URL of the site to be crawled
    const DUPLICATE_CHECK_TYPES                      = '_duplicate_check_types';                   // array    An array of types that will be used to decide with what to check for duplicate posts
    const DO_NOT_USE_GENERAL_SETTINGS                = '_do_not_use_general_settings';             // bool     True if the user wants to specify different settings for the post
    const COOKIES                                    = '_cookies';                                 // array    An array of arrays that stores cookie keys and values. Each inner array has 'key' and 'value' keys and corresponding values.
    const REQUEST_HEADERS                            = '_request_headers';                         // array    An array of arrays that stores HTTP header keys and values. Each inner array has 'key' and 'value' keys and corresponding values.
    const CACHE_TEST_URL_RESPONSES                   = '_cache_test_url_responses';                // bool     True if the responses retrieved from the test URLs should be cached
    const FIX_TABS                                   = '_fix_tabs';                                // bool     True if the tabs should be fixed when the page is scrolled down
    const FIX_CONTENT_NAVIGATION                     = '_fix_content_navigation';                  // bool     True if the tab content navigation should be fixed when the page is scrolled down

    // Category tab
    const CATEGORY_ADD_CATEGORY_URLS_WITH_SELECTOR   = '_category_add_category_urls_with_selector';// bool     Used to show/hide options related to adding category URLs to category map using CSS selectors.
    const CATEGORY_LIST_PAGE_URL                     = '_category_list_page_url';                  // string   URL of the page which includes URLs of the categories
    const CATEGORY_LIST_URL_SELECTORS                = '_category_list_url_selectors';             // array    Selectors to get category URLs from category list page
    const CATEGORY_POST_LINK_SELECTORS               = '_category_post_link_selectors';            // array    Link selectors used to get post URLs in categories
    const CATEGORY_COLLECT_IN_REVERSE_ORDER          = '_category_collect_in_reverse_order';       // bool     True if found URLs should be ordered in reverse for each CSS selector.
    const CATEGORY_UNNECESSARY_ELEMENT_SELECTORS     = '_category_unnecessary_element_selectors';  // array    Selectors for the elements to be removed from the content of category page
    const CATEGORY_POST_SAVE_THUMBNAILS              = '_category_post_save_thumbnails';           // bool     True if the thumbnails should be saved as featured images for the posts
    const CATEGORY_POST_THUMBNAIL_SELECTORS          = '_category_post_thumbnail_selectors';       // array    Image selectors for post thumbnails
    const TEST_FIND_REPLACE_THUMBNAIL_URL_CAT        = '_test_find_replace_thumbnail_url_cat';     // string   An image URL which is used to conduct find-replace test
    const CATEGORY_FIND_REPLACE_THUMBNAIL_URL        = '_category_find_replace_thumbnail_url';     // array    An array including what to find and with what to replace for thumbnail URL
    const CATEGORY_POST_IS_LINK_BEFORE_THUMBNAIL     = '_category_post_is_link_before_thumbnail';  // bool     True if post URLs come before the thumbnails in the category's HTML
    const CATEGORY_NEXT_PAGE_SELECTORS               = '_category_next_page_selectors';            // array    Link selectors used to get next page URL of the category
    const CATEGORY_MAP                               = '_category_map';                            // array    Maps the category links to WP categories
    const TEST_FIND_REPLACE_FIRST_LOAD_CAT           = '_test_find_replace_first_load_cat';        // string   A piece of code used to test regexes for find-replace settings for first load of the category HTML
    const CATEGORY_FIND_REPLACE_RAW_HTML             = '_category_find_replace_raw_html';          // array    An array including what to find and with what to replace for raw response content of category pages
    const CATEGORY_FIND_REPLACE_FIRST_LOAD           = '_category_find_replace_first_load';        // array    An array including what to find and with what to replace for category HTML
    const CATEGORY_FIND_REPLACE_ELEMENT_ATTRIBUTES   = '_category_find_replace_element_attributes';// array    An array including what to find and with what to replace for specified elements' specified attributes
    const CATEGORY_EXCHANGE_ELEMENT_ATTRIBUTES       = '_category_exchange_element_attributes';    // array    An array including selectors of elements and the attributes whose values should be exchanged
    const CATEGORY_REMOVE_ELEMENT_ATTRIBUTES         = '_category_remove_element_attributes';      // array    An array including selectors of elements and comma-separated attributes that should be removed from the element
    const CATEGORY_FIND_REPLACE_ELEMENT_HTML         = '_category_find_replace_element_html';      // array    An array including what to find and with what to replace for specified elements' HTML
    const TEST_URL_CATEGORY                          = '_test_url_category';                       // string   Holds a test URL for the user to conduct tests on category pages
    const CATEGORY_NOTIFY_EMPTY_VALUE_SELECTORS      = '_category_notify_empty_value_selectors';   // array    CSS selectors to be used to notify the user via email when one of the selector's value is empty/not found
    const CATEGORY_REQUEST_FILTERS                   = '_category_request_filters';                // string   JSON string that stores the filters that target crawl requests made for category pages
    const CATEGORY_PAGE_FILTERS                      = '_category_page_filters';                   // string   JSON string that stores the filters that target category pages
    const CATEGORY_DATA_FILTERS                      = '_category_data_filters';                   // string   JSON string that stores the filters that target category page data (e.g. collected URLs)

    // Post tab
    const TEST_URL_POST                              = '_test_url_post';                           // string   Holds a test URL for the user to conduct tests on post pages
    const POST_TITLE_SELECTORS                       = '_post_title_selectors';                    // array    Selector for post title
    const POST_EXCERPT_SELECTORS                     = '_post_excerpt_selectors';                  // array    Selectors for the post summary
    const POST_CONTENT_SELECTORS                     = '_post_content_selectors';                  // array    Selectors for the post content

    const POST_CATEGORY_NAME_SELECTORS               = '_post_category_name_selectors';                 // array    CSS selectors with attributes that find category names
    const POST_CATEGORY_ADD_ALL_FOUND_CATEGORY_NAMES = '_post_category_add_all_found_category_names';   // bool     When checked, category names found by all CSS selectors will be added
    const POST_CATEGORY_NAME_SEPARATORS              = '_post_category_name_separators';                // array    Separators that will be used to separate category names in a single string
    const POST_CATEGORY_ADD_HIERARCHICAL             = '_post_category_add_hierarchical';               // bool     True if categories found by a single selector will be added hierarchically
    const POST_CATEGORY_DO_NOT_ADD_CATEGORY_IN_MAP   = '_post_category_do_not_add_category_in_map';     // bool     True if the category defined in the category map should not be added when there is at least one category found by CSS selectors

    const POST_DATE_SELECTORS                        = '_post_date_selectors';                      // array    Selectors for the post date
    const TEST_FIND_REPLACE_DATE                     = '_test_find_replace_date';                   // string   A date which is used to conduct find-replace test
    const POST_FIND_REPLACE_DATE                     = '_post_find_replace_date';                   // array    An array including what to find and with what to replace for dates
    const POST_DATE_ADD_MINUTES                      = '_post_date_add_minutes';                    // int      How many minutes that should be added to the final date
    const POST_CUSTOM_CONTENT_SHORTCODE_SELECTORS    = '_post_custom_content_shortcode_selectors';  // array    An array holding selectors with custom attributes and custom-defined shortcodes
    const POST_FIND_REPLACE_CUSTOM_SHORT_CODE        = '_post_find_replace_custom_short_code';      // array    An array including what to find and with what to replace for specified custom short codes
    const POST_TAG_SELECTORS                         = '_post_tag_selectors';                       // array    Selectors for post tag
    const POST_SLUG_SELECTORS                        = '_post_slug_selectors';                      // array    Selectors for post slug
    const POST_PAGINATE                              = '_post_paginate';                            // bool     If the original post is paginated, paginate it in WP as well
    const POST_NEXT_PAGE_URL_SELECTORS               = '_post_next_page_url_selectors';             // array    Next page selectors for the post if it is paginated
    const POST_NEXT_PAGE_ALL_PAGES_URL_SELECTORS     = '_post_next_page_all_pages_url_selectors';   // array    Sometimes the post page does not have next page URL. Instead, it has all page URLs in one place.
    const POST_SAVE_AS_SINGLE_PAGE                   = '_post_save_as_single_page';                 // bool     True if a paginated post should not be paginated but saved as a single page.
    const POST_IS_LIST_TYPE                          = '_post_is_list_type';                        // bool     Whether or not the post is created as a list
    const POST_LIST_ITEM_STARTS_AFTER_SELECTORS      = '_post_list_item_starts_after_selectors';    // array    CSS selectors to understand first list items' start position
    const POST_LIST_TITLE_SELECTORS                  = '_post_list_title_selectors';                // array    Title selectors for the list-type post
    const POST_LIST_CONTENT_SELECTORS                = '_post_list_content_selectors';              // array    Content selectors for the list-type post
    const POST_LIST_ITEM_NUMBER_SELECTORS            = '_post_list_item_number_selectors';          // array    Selectors for list item numbers
    const POST_LIST_ITEM_AUTO_NUMBER                 = '_post_list_item_auto_number';               // bool     True if item numbers can be set automatically, if item's number does not exist
    const POST_LIST_INSERT_REVERSED                  = '_post_list_insert_reversed';                // bool     True to insert the list items in reverse order
    const POST_META_KEYWORDS                         = '_post_meta_keywords';                       // bool     Whether or not to save meta keywords
    const POST_META_KEYWORDS_AS_TAGS                 = '_post_meta_keywords_as_tags';               // bool     True if meta keywords should be inserted as tags
    const POST_META_DESCRIPTION                      = '_post_meta_description';                    // bool     Whether or not to save meta description
    const POST_SAVE_ALL_IMAGES_IN_CONTENT            = '_post_save_all_images_in_content';          // bool     Whether or not to save all images in post content as media
    const POST_SAVE_IMAGES_AS_MEDIA                  = '_post_save_images_as_media';                // bool     Whether or not to upload post images to WP
    const POST_SAVE_IMAGES_AS_GALLERY                = '_post_save_images_as_gallery';              // bool     Whether or not to save to-be-specified images as gallery
    const POST_GALLERY_IMAGE_SELECTORS               = '_post_gallery_image_selectors';             // array    Selectors with attributes for image URLs in the HTML of the page
    const POST_SAVE_IMAGES_AS_WOOCOMMERCE_GALLERY    = '_post_save_images_as_woocommerce_gallery';  // bool     True if the gallery images should be saved as the value of post meta key that is used to store the gallery for WooCommerce products
    const POST_IMAGE_SELECTORS                       = '_post_image_selectors';                     // array    Selectors for image URLs in the post
    const TEST_FIND_REPLACE_IMAGE_URLS               = '_test_find_replace_image_urls';             // string   An image URL which is used to conduct find-replace test
    const POST_FIND_REPLACE_IMAGE_URLS               = '_post_find_replace_image_urls';             // array    An array including what to find and with what to replace for image URLs
    const POST_SAVE_THUMBNAILS_IF_NOT_EXIST          = '_post_save_thumbnails_if_not_exist';        // bool     True if a thumbnail image should be saved from a post page, if no thumbnail is found in category page.
    const POST_THUMBNAIL_SELECTORS                   = '_post_thumbnail_selectors';                 // array    CSS selectors for thumbnail images in post page
    const TEST_FIND_REPLACE_THUMBNAIL_URL            = '_test_find_replace_thumbnail_url';          // string   An image URL which is used to conduct find-replace test
    const POST_FIND_REPLACE_THUMBNAIL_URL            = '_post_find_replace_thumbnail_url';          // array    An array including what to find and with what to replace for thumbnail URL
    const POST_CUSTOM_META_SELECTORS                 = '_post_custom_meta_selectors';               // array    An array for selectors with attribute and their meta properties, such as meta key, and whether it is multiple or not
    const POST_CUSTOM_META                           = '_post_custom_meta';                         // array    An array containing custom post meta keys and their values.
    const POST_FIND_REPLACE_CUSTOM_META              = '_post_find_replace_custom_meta';            // array    An array including what to find and with what to replace for specified meta keys
    const POST_CUSTOM_TAXONOMY_SELECTORS             = '_post_custom_taxonomy_selectors';           // array    An array for selectors with attribute and their meta properties, such as meta key, and whether it is multiple or not
    const POST_CUSTOM_TAXONOMY                       = '_post_custom_taxonomy';                     // array    An array containing custom post taxonomy names and their values.
    const POST_NOTIFY_EMPTY_VALUE_SELECTORS          = '_post_notify_empty_value_selectors';        // array    CSS selectors to be used to notify the user via email when one of the selector's value is empty/not found
    const POST_TRIGGER_SAVE_POST_HOOK                = '_post_trigger_save_post_hook';              // bool     True if the save_post hook must be triggered after the post is completely saved

    const TEST_FIND_REPLACE_FIRST_LOAD               = '_test_find_replace_first_load';             // string   A piece of code used to test regexes for find-replace settings for first load of the post HTML
    const POST_FIND_REPLACE_RAW_HTML                 = '_post_find_replace_raw_html';               // array    An array including what to find and with what to replace for raw response content of post pages
    const POST_FIND_REPLACE_FIRST_LOAD               = '_post_find_replace_first_load';             // array    An array including what to find and with what to replace for post HTML
    const POST_FIND_REPLACE_ELEMENT_ATTRIBUTES       = '_post_find_replace_element_attributes';     // array    An array including what to find and with what to replace for specified elements' specified attributes
    const POST_EXCHANGE_ELEMENT_ATTRIBUTES           = '_post_exchange_element_attributes';         // array    An array including selectors of elements and the attributes whose values should be exchanged
    const POST_REMOVE_ELEMENT_ATTRIBUTES             = '_post_remove_element_attributes';           // array    An array including selectors of elements and comma-separated attributes that should be removed from the element
    const POST_FIND_REPLACE_ELEMENT_HTML             = '_post_find_replace_element_html';           // array    An array including what to find and with what to replace for specified elements' HTML

    const POST_UNNECESSARY_ELEMENT_SELECTORS         = '_post_unnecessary_element_selectors';       // array    Selectors for the elements to be removed from the content

    const POST_REQUEST_FILTERS                       = '_post_request_filters';                     // string   JSON string that stores the filters that target crawl requests made for post pages
    const POST_PAGE_FILTERS                          = '_post_page_filters';                        // string   JSON string that stores the filters that target post pages

    // Templates tab
    const POST_TEMPLATE_MAIN                         = '_post_template_main';                       // string   Main template for the post
    const POST_TEMPLATE_TITLE                        = '_post_template_title';                      // string   Title template for the post
    const POST_TEMPLATE_EXCERPT                      = '_post_template_excerpt';                    // string   Excerpt template for the post
    const POST_TEMPLATE_LIST_ITEM                    = '_post_template_list_item';                  // string   List item template for the post
    const POST_TEMPLATE_GALLERY_ITEM                 = '_post_template_gallery_item';               // string   Gallery item template for a single image
    const POST_REMOVE_LINKS_FROM_SHORT_CODES         = '_post_remove_links_from_short_codes';       // bool     True if the template should be cleared from URLs
    const POST_CONVERT_IFRAMES_TO_SHORT_CODE         = '_post_convert_iframes_to_short_code';       // bool     True if the iframes in the post template should be converted to a short code
    const POST_CONVERT_SCRIPTS_TO_SHORT_CODE         = '_post_convert_scripts_to_short_code';       // bool     True if the scripts in the post template should be converted to a short code
    const POST_REMOVE_EMPTY_HTML_TAGS                = '_post_remove_empty_html_tags';              // bool     True if empty HTML tags and comments should be removed from every possible place
    const POST_REMOVE_SCRIPTS                        = '_post_remove_scripts';                      // bool     True if script elements and attributes should be removed from every possible place
    const TEST_FIND_REPLACE                          = '_test_find_replace';                        // string   A piece of code used to test RegExes
    const POST_FIND_REPLACE_TEMPLATE                 = '_post_find_replace_template';               // array    An array including what to find and with what to replace for template
    const POST_FIND_REPLACE_TITLE                    = '_post_find_replace_title';                  // array    An array including what to find and with what to replace for title
    const POST_FIND_REPLACE_EXCERPT                  = '_post_find_replace_excerpt';                // array    An array including what to find and with what to replace for excerpt
    const POST_FIND_REPLACE_TAGS                     = '_post_find_replace_tags';                   // array    An array including what to find and with what to replace for tags
    const POST_FIND_REPLACE_META_KEYWORDS            = '_post_find_replace_meta_keywords';          // array    An array including what to find and with what to replace for meta keywords
    const POST_FIND_REPLACE_META_DESCRIPTION         = '_post_find_replace_meta_description';       // array    An array including what to find and with what to replace for meta description
    const POST_FIND_REPLACE_CUSTOM_SHORTCODES        = '_post_find_replace_custom_shortcodes';      // array    An array including what to find and with what to replace for the data of custom short codes
    const TEMPLATE_UNNECESSARY_ELEMENT_SELECTORS     = '_template_unnecessary_element_selectors';   // array    Selectors for the elements to be removed from the template

    // Filters tab
    const POST_DATA_FILTERS                          = '_post_data_filters';                        // string   JSON string that stores the filters that targets post data

    // Notes tab
    const NOTES                                      = '_notes';                                    // string   A setting for the user to keep notes about the site (this is rich text editor).

    const NOTES_SIMPLE                               = '_notes_simple';                             // string   A setting for the user to keep simple (not formatted) notes about the site. (just textarea)

    // Import/export tab
    const POST_IMPORT_SETTINGS                       = '_post_import_settings';                     // string   Used to get settings imported from another site. This is not saved to the db.
    const POST_EXPORT_SETTINGS                       = '_post_export_settings';                     // string   Used to show the string that can be used to import the settings into another site. This is not saved to the db.

    // Dev Tools
    const DEV_TOOLS_STATE                            = '_dev_tools_state';                          // string   A serialized array containing the state of DEV tools for this post
    const DEV_TOOLS_URL                              = '_dt_toolbar_url';                           // string   Not saved to the db. Stores URL of Dev Tools.
    const DEV_TOOLS_CSS_SELECTOR                     = '_dt_toolbar_css_selector';                  // string   Not saved to the db. Stores CSS selector value of Dev Tools.
    const DEV_TOOLS_TARGET_HTML_TAG                  = '_dt_target_html_tag';                       // string   Not saved to the db. Stores target HTML tag of Dev Tools.
    const DEV_TOOLS_SELECTION_BEHAVIOR               = 'selection_behavior';                        // string   Not saved to the db. Stores selection behavior of Dev Tools.
    const DEV_TOOLS_TEST_BUTTON_BEHAVIOR             = 'test_button_behavior';                      // string   Not saved to the db. Stores test button behavior of Dev Tools.
    const DEV_TOOLS_APPLY_MANIPULATION_OPTIONS       = 'apply_manipulation_options';                // bool     Not saved to the db. Stores whether manipulations should be applied or not. True if applied.
    const DEV_TOOLS_USE_IMMEDIATELY                  = 'use_immediately';                           // bool     Not saved to the db. Stores true if found CSS selector should be immediately used. False otherwise.
    const DEV_TOOLS_REMOVE_SCRIPTS                   = 'remove_scripts';                            // bool     Not saved to the db. Stores true if scripts should be removed. False otherwise.
    const DEV_TOOLS_REMOVE_STYLES                    = 'remove_styles';                             // bool     Not saved to the db. Stores true if styles should be removed. False otherwise.

    // Options box
    const OPTIONS_BOX                               = '_options_box';                              // array    Not directly saved to the db. Stores options-box-related settings.
    const OPTIONS_BOX_FILE_FIND_REPLACE             = '_options_box[file_find_replace]';
    const OPTIONS_BOX_MOVE                          = '_options_box[move]';
    const OPTIONS_BOX_COPY                          = '_options_box[copy]';
    const OPTIONS_BOX_TEMPLATES_FILE_NAME           = '_options_box[templates_file_name]';
    const OPTIONS_BOX_TEMPLATES_MEDIA_TITLE         = '_options_box[templates_media_title]';
    const OPTIONS_BOX_TEMPLATES_MEDIA_DESC          = '_options_box[templates_media_description]';
    const OPTIONS_BOX_TEMPLATES_MEDIA_CAPTION       = '_options_box[templates_media_caption]';
    const OPTIONS_BOX_TEMPLATES_MEDIA_ALT_TEXT      = '_options_box[templates_media_alt_text]';
    const OPTIONS_BOX_DECIMAL_SEPARATOR_AFTER       = '_options_box[decimal_separator_after]';
    const OPTIONS_BOX_USE_THOUSANDS_SEPARATOR       = '_options_box[use_thousands_separator]';
    const OPTIONS_BOX_REMOVE_IF_NOT_NUMERIC         = '_options_box[remove_if_not_numeric]';
    const OPTIONS_BOX_PRECISION                     = '_options_box[precision]';
    const OPTIONS_BOX_FORMULAS                      = '_options_box[formulas]';
    const OPTIONS_BOX_FIND_REPLACE                  = '_options_box[find_replace]';
    const OPTIONS_BOX_TREAT_AS_JSON                 = '_options_box[treat_as_json]';
    const OPTIONS_BOX_IMPORT_SETTINGS               = '_options_box_import_settings';
    const OPTIONS_BOX_EXPORT_SETTINGS               = '_options_box_export_settings';
    const OPTIONS_BOX_NOTE                          = '_options_box[note]';
    const OPTIONS_BOX_REMOVE_IF_EMPTY               = '_options_box[remove_if_empty]';
    const OPTIONS_BOX_TEMPLATES                     = '_options_box[templates]';

    /* ***************************************
     *
     * CRON META KEYS
     * Post meta keys or option keys that are used to store things related to CRON jobs
     *
     */

    /* Keys for URL-collecting CRON event */
    const CRON_LAST_CHECKED_AT                       = '_cron_last_checked_at';                     // date     Date of last URL collection
    const CRON_LAST_CHECKED_CATEGORY_URL             = '_cron_last_checked_category_url';           // string   URL (or URL part, just how the user saves it as) of the last checked category
    const CRON_LAST_CHECKED_CATEGORY_NEXT_PAGE_URL   = '_cron_last_checked_category_next_page_url'; // string   Next page URL for the last checked category (basically, next page to crawl)
    const CRON_NO_NEW_URL_INSERTED_COUNT             = '_cron_no_new_url_inserted_count';           // int      Number of pages crawled with no new URL insertion in a row. E.g. Page 1 - none,
                                                                                                    //          Page 2 - none, Page 3 - none    => this value will be 3
    const CRON_CRAWLED_PAGE_COUNT                    = '_cron_crawled_page_count';                  // int      Holds how many pages crawled before

    /* Keys for post-crawling CRON event */
    const CRON_LAST_CRAWLED_AT                       = '_cron_last_crawled_at';                     // date     Date of last post crawl
    const CRON_LAST_CRAWLED_URL_ID                   = '_cron_last_crawled_url_id';                 // int      Stores ID of the last crawled URL from urls table
    const CRON_POST_NEXT_PAGE_URL                    = '_cron_post_next_page_url';                  // string   Stores next page URL for a paginated post
    const CRON_POST_NEXT_PAGE_URLS                   = '_cron_post_next_page_urls';                 // array    Stores next page URLs as a serialized array for a paginated post. This is used if the post has
                                                                                                    //          all of the next pages together.
    const CRON_POST_DRAFT_ID                         = '_cron_post_draft_id';                       // int      Stores the ID of the draft post. A draft post is a post created if target post is paginated. New
                                                                                                    //          content is appended to that post's content. After all pages are crawled, the draft is published.

    /* Keys for post-recrawling CRON event */
    const CRON_RECRAWL_LAST_CRAWLED_AT               = '_cron_recrawl_last_crawled_at';             // date     Date of last post recrawl
    const CRON_RECRAWL_LAST_CRAWLED_URL_ID           = '_cron_recrawl_last_crawled_url_id';         // int      Stores ID of the last recrawled URL from urls table
    const CRON_RECRAWL_POST_NEXT_PAGE_URL            = '_cron_recrawl_post_next_page_url';          // string   Stores next page URL for a paginated post
    const CRON_RECRAWL_POST_NEXT_PAGE_URLS           = '_cron_recrawl_post_next_page_urls';         // array    Stores next page URLs as a serialized array for a paginated post. This is used if the post has
                                                                                                    //          all of the next pages together.
    const CRON_RECRAWL_POST_DRAFT_ID                 = '_cron_recrawl_post_draft_id';               // int      Stores the ID of the draft post. A draft post is a post created if target post is paginated. New
                                                                                                    //          content is appended to that post's content. After all pages are recrawled, the draft is published.

    /* Keys for post-delete CRON event */
    const CRON_LAST_DELETED_AT                       = '_cron_last_deleted_at';                     // date     Date of last post delete

    /* ***************************************
     *
     * GENERAL SETTINGS' KEYS
     * Keys of general settings start with "WPCC" so that they can be easily differentiated.
     *
     */

    // Scheduling
    const WPCC_IS_SCHEDULING_ACTIVE                                 = '_wpcc_is_scheduling_active';                   // bool     If true, CRON scheduling is active
    const WPCC_NO_NEW_URL_PAGE_TRIAL_LIMIT                          = '_wpcc_no_new_url_page_trial_limit';            // int      Stores the limit for how many pages should be crawled if there is no new URL. Read the doc of
                                                                                                                      //          SchedulingService#handleNoNewUrlInsertedCount method to understand why this is necessary.
    const WPCC_MAX_PAGE_COUNT_PER_CATEGORY                          = '_wpcc_max_page_count_per_category';            // int      Max number of pages to be checked for each category
    const WPCC_INTERVAL_URL_COLLECTION                              = '_wpcc_interval_url_collection';                // string   Key of a WPCC CRON interval, indicating url collection interval
    const WPCC_INTERVAL_POST_CRAWL                                  = '_wpcc_interval_post_crawl';                    // string   Key of a WPCC CRON interval, indicating post-crawling interval

    const WPCC_IS_RECRAWLING_ACTIVE                                 = '_wpcc_is_recrawling_active';                   // bool     If true, post recrawling is active
    const WPCC_INTERVAL_POST_RECRAWL                                = '_wpcc_interval_post_recrawl';                  // string   Key of a WPCC CRON interval, indicating post-recrawling interval
    const WPCC_RUN_COUNT_URL_COLLECTION                             = '_wpcc_run_count_url_collection';               // int      How many times URL collection event should be run for each interval
    const WPCC_RUN_COUNT_POST_CRAWL                                 = '_wpcc_run_count_post_crawl';                   // int      How many times post crawling event should be run for each interval
    const WPCC_RUN_COUNT_POST_RECRAWL                               = '_wpcc_run_count_post_recrawl';                 // int      How many times post recrawling event should be run for each interval
    const WPCC_MAX_RECRAWL_COUNT                                    = '_wpcc_max_recrawl_count';                      // int      Maximum number of times a post can be recrawled
    const WPCC_MIN_TIME_BETWEEN_TWO_RECRAWLS_IN_MIN                 = '_wpcc_min_time_between_two_recrawls_in_min';   // int      Minimum time in minutes that should pass after the last recrawl so that a post is suitable for recrawling again
    const WPCC_RECRAWL_POSTS_NEWER_THAN_IN_MIN                      = '_wpcc_recrawl_posts_newer_than_in_min';        // int      Time in minutes that will be used to find new posts for recrawling event. E.g. if this is 1 month in minutes, posts older than 1 month won't be recrawled.

    const WPCC_IS_DELETING_POSTS_ACTIVE                             = '_wpcc_is_deleting_posts_active';               // bool     If true, post deleting is active
    const WPCC_INTERVAL_POST_DELETE                                 = '_wpcc_interval_post_delete';                   // string   Key of a WPCC CRON interval, indicating post-deleting interval
    const WPCC_MAX_POST_COUNT_PER_POST_DELETE_EVENT                 = '_wpcc_max_post_count_per_post_delete_event';   // int      Maximum number of posts that can be deleted in a post delete event.
    const WPCC_DELETE_POSTS_OLDER_THAN_IN_MIN                       = '_wpcc_delete_posts_older_than_in_min';         // int      Time in minutes that will be used to find old posts for post-deleting event. E.g. if this is 1 month in minutes, posts older than 1 month will be deleted.
    const WPCC_IS_DELETE_POST_ATTACHMENTS                           = '_wpcc_is_delete_post_attachments';             // bool     If true, post attachments will be deleted with the post, too.

    // Post
    const WPCC_ALLOW_COMMENTS                                       = '_wpcc_allow_comments';                         // bool     True to allow comments, false otherwise
    const WPCC_POST_STATUS                                          = '_wpcc_post_status';                            // string   One of the WordPress post statuses
    const WPCC_POST_TYPE                                            = '_wpcc_post_type';                              // string   One of the WordPress post types
    const WPCC_POST_CATEGORY_TAXONOMIES                             = '_wpcc_post_category_taxonomies';               // array    An array of post category taxonomies and their descriptions.
    const WPCC_POST_AUTHOR                                          = '_wpcc_post_author';                            // int      ID of a user
    const WPCC_POST_TAG_LIMIT                                       = '_wpcc_post_tag_limit';                         // int      The number of tags that can be added to a post at max
    const WPCC_CHANGE_PASSWORD                                      = '_wpcc_change_password';                        // bool     True if the password inputs should be shown.
    const WPCC_POST_PASSWORD                                        = '_wpcc_post_password';                          // string   Password for the posts
    const WPCC_POST_SET_SRCSET                                      = '_wpcc_post_set_srcset';                        // bool     True if srcset attributes of the saved media should be set in the template
    const WPCC_PROTECTED_ATTACHMENTS                                = '_wpcc_protected_attachments';                  // array    An array of attachment IDs that should not be deleted when recrawling/deleting the post

    const WPCC_ALLOWED_IFRAME_SHORT_CODE_DOMAINS                    = '_wpcc_allowed_iframe_short_code_domains';      // array    An array of domain names that are allowed for iframe short code
    const WPCC_ALLOWED_SCRIPT_SHORT_CODE_DOMAINS                    = '_wpcc_allowed_script_short_code_domains';      // array    An array of domain names that are allowed for script short code

    // Translation
    const WPCC_IS_TRANSLATION_ACTIVE                                = '_wpcc_is_translation_active';                                  // bool     If true, content translation is active
    const WPCC_SELECTED_TRANSLATION_SERVICE                         = '_wpcc_selected_translation_service';                           // string   Selected translation service. E.g. Google or Microsoft.

    const WPCC_TRANSLATION_GOOGLE_TRANSLATE_FROM                    = '_wpcc_translation_google_translate_from';                      // string   Language of the original content for Google Translate
    const WPCC_TRANSLATION_GOOGLE_TRANSLATE_TO                      = '_wpcc_translation_google_translate_to';                        // string   Target language for Google Translate
    const WPCC_TRANSLATION_GOOGLE_TRANSLATE_PROJECT_ID              = '_wpcc_translation_google_translate_project_id';                // string   Project ID retrieved from Google Cloud Console for Google Cloud Translate API
    const WPCC_TRANSLATION_GOOGLE_TRANSLATE_API_KEY                 = '_wpcc_translation_google_translate_api_key';                   // string   API key retrieved from Google Cloud Console for the project ID
    const WPCC_TRANSLATION_GOOGLE_TRANSLATE_TEST                    = '_wpcc_translation_google_translate_test';                      // string   Text for testing Google Translate API

    const WPCC_TRANSLATION_MICROSOFT_TRANSLATOR_TEXT_FROM           = '_wpcc_translation_microsoft_translator_text_from';             // string   Language of the original content for Microsoft Translator Text
    const WPCC_TRANSLATION_MICROSOFT_TRANSLATOR_TEXT_TO             = '_wpcc_translation_microsoft_translator_text_to';               // string   Target language for Microsoft Translator Text
    const WPCC_TRANSLATION_MICROSOFT_TRANSLATOR_TEXT_CLIENT_SECRET  = '_wpcc_translation_microsoft_translator_text_client_secret';    // string   Client secret for Microsoft Translator Text API
    const WPCC_TRANSLATION_MICROSOFT_TRANSLATOR_TEXT_TEST           = '_wpcc_translation_microsoft_translator_text_test';             // string   Text for testing Microsoft Translator Text API

    const WPCC_TRANSLATION_YANDEX_TRANSLATE_FROM                    = '_wpcc_translation_yandex_translate_from';                      // string   Language of the original content for Yandex Translate
    const WPCC_TRANSLATION_YANDEX_TRANSLATE_TO                      = '_wpcc_translation_yandex_translate_to';                        // string   Target language for Yandex Translate
    const WPCC_TRANSLATION_YANDEX_TRANSLATE_API_KEY                 = '_wpcc_translation_yandex_translate_api_key';                   // string   API key for Yandex Translate API
    const WPCC_TRANSLATION_YANDEX_TRANSLATE_TEST                    = '_wpcc_translation_yandex_translate_test';                      // string   Text for testing Yandex Translate API

    const WPCC_TRANSLATION_AMAZON_TRANSLATE_FROM                    = '_wpcc_translation_amazon_translate_from';                      // string   Language of the original content for Amazon Translate
    const WPCC_TRANSLATION_AMAZON_TRANSLATE_TO                      = '_wpcc_translation_amazon_translate_to';                        // string   Target language for Amazon Translate
    const WPCC_TRANSLATION_AMAZON_TRANSLATE_ACCESS_KEY              = '_wpcc_translation_amazon_translate_access_key';                // string   Access key for Amazon Translate API
    const WPCC_TRANSLATION_AMAZON_TRANSLATE_SECRET                  = '_wpcc_translation_amazon_translate_secret';                    // string   Secret key for Amazon Translate API
    const WPCC_TRANSLATION_AMAZON_TRANSLATE_REGION                  = '_wpcc_translation_amazon_translate_region';                    // string   A region from AWS to which the API requests will be sent
    const WPCC_TRANSLATION_AMAZON_TRANSLATE_TEST                    = '_wpcc_translation_amazon_translate_test';                      // string   Text for testing Amazon Translate API

    // Spinning
    const WPCC_IS_SPINNING_ACTIVE                                   = '_wpcc_is_spinning_active';                             // bool     If true, content spinning is active.
    const WPCC_SELECTED_SPINNING_SERVICE                            = '_wpcc_selected_spinning_service';                      // string   Selected spinning service. E.g. Chimp Rewriter
    const WPCC_SPINNING_SEND_IN_ONE_REQUEST                         = '_wpcc_spinning_send_in_one_request';                   // bool     If true, every spinnable field's value will be sent to the API in one request.
    const WPCC_SPINNING_PROTECTED_TERMS                             = '_wpcc_spinning_protected_terms';                       // string   Terms that should not be spun

//    const WPCC_SPINNING_CHIMP_REWRITER_EMAIL                        = '_wpcc_spinning_chimp_rewriter_email';                          // string   Email address used for API key in Chimp Rewriter
//    const WPCC_SPINNING_CHIMP_REWRITER_API_KEY                      = '_wpcc_spinning_chimp_rewriter_api_key';                        // string   API key for Chimp Rewriter
//    const WPCC_SPINNING_CHIMP_REWRITER_APP_ID                       = '_wpcc_spinning_chimp_rewriter_app_id';                         // string   App ID for Chimp Rewriter
//    const WPCC_SPINNING_CHIMP_REWRITER_QUALITY                      = '_wpcc_spinning_chimp_rewriter_quality';                        // int
//    const WPCC_SPINNING_CHIMP_REWRITER_PHRASE_QUALITY               = '_wpcc_spinning_chimp_rewriter_phrase_quality';                 // int
//    const WPCC_SPINNING_CHIMP_REWRITER_POS_MATCH                    = '_wpcc_spinning_chimp_rewriter_pos_match';                      // int
//    const WPCC_SPINNING_CHIMP_REWRITER_DO_NOT_REWRITE               = '_wpcc_spinning_chimp_rewriter_do_not_rewrite';                 // bool
//    const WPCC_SPINNING_CHIMP_REWRITER_LANGUAGE                     = '_wpcc_spinning_chimp_rewriter_language';                       // string
//    const WPCC_SPINNING_CHIMP_REWRITER_SENTENCE_REWRITE             = '_wpcc_spinning_chimp_rewriter_sentence_rewrite';               // bool
//    const WPCC_SPINNING_CHIMP_REWRITER_GRAMMAR_CHECK                = '_wpcc_spinning_chimp_rewriter_grammar_check';                  // bool
//    const WPCC_SPINNING_CHIMP_REWRITER_REORDER_PARAGRAPHS           = '_wpcc_spinning_chimp_rewriter_reorder_paragraphs';             // bool
//    const WPCC_SPINNING_CHIMP_REWRITER_REPLACE_PHRASES_WITH_PHRASES = '_wpcc_spinning_chimp_rewriter_replace_phrases_with_phrases';   // bool
//    const WPCC_SPINNING_CHIMP_REWRITER_SPIN_WITHIN_SPIN             = '_wpcc_spinning_chimp_rewriter_spin_within_spin';               // bool
//    const WPCC_SPINNING_CHIMP_REWRITER_SPIN_TIDY                    = '_wpcc_spinning_chimp_rewriter_spin_tidy';                      // bool
//    const WPCC_SPINNING_CHIMP_REWRITER_EXCLUDE_ORIGINAL             = '_wpcc_spinning_chimp_rewriter_exclude_original';               // bool
//    const WPCC_SPINNING_CHIMP_REWRITER_REPLACE_FREQUENCY            = '_wpcc_spinning_chimp_rewriter_replace_frequency';              // int
//    const WPCC_SPINNING_CHIMP_REWRITER_MAX_SYNS                     = '_wpcc_spinning_chimp_rewriter_max_syns';                       // int
//    const WPCC_SPINNING_CHIMP_REWRITER_INSTANT_UNIQUE               = '_wpcc_spinning_chimp_rewriter_instant_unique';                 // int
//    const WPCC_SPINNING_CHIMP_REWRITER_MAX_SPIN_DEPTH               = '_wpcc_spinning_chimp_rewriter_max_spin_depth';                 // int
//    const WPCC_SPINNING_CHIMP_REWRITER_TEST                         = '_wpcc_spinning_chimp_rewriter_test';                           // string   Text for testing Chimp Rewriter

    const WPCC_SPINNING_SPIN_REWRITER_EMAIL                         = '_wpcc_spinning_spin_rewriter_email';                   // string   Email address used for API key in Spin Rewriter
    const WPCC_SPINNING_SPIN_REWRITER_API_KEY                       = '_wpcc_spinning_spin_rewriter_api_key';                 // string   API key for Spin Rewriter
    const WPCC_SPINNING_SPIN_REWRITER_CONFIDENCE_LEVEL              = '_wpcc_spinning_spin_rewriter_confidence_level';        // string   Confidence level
    const WPCC_SPINNING_SPIN_REWRITER_AUTO_PROTECTED_TERMS          = '_wpcc_spinning_spin_rewriter_auto_protected_terms';    // bool     True if auto protected terms feature should be enabled
    const WPCC_SPINNING_SPIN_REWRITER_NESTED_SPINTAX                = '_wpcc_spinning_spin_rewriter_nested_spintax';          // bool     True if nested spintax feature should be enabled
    const WPCC_SPINNING_SPIN_REWRITER_AUTO_SENTENCES                = '_wpcc_spinning_spin_rewriter_auto_sentences';          // bool     True if auto sentences feature should be enabled
    const WPCC_SPINNING_SPIN_REWRITER_AUTO_PARAGRAPHS               = '_wpcc_spinning_spin_rewriter_auto_paragraphs';         // bool     True if auto paragraphs feature should be enabled
    const WPCC_SPINNING_SPIN_REWRITER_AUTO_NEW_PARAGRAPHS           = '_wpcc_spinning_spin_rewriter_auto_new_paragraphs';     // bool     True if auto new paragraphs feature should be enabled
    const WPCC_SPINNING_SPIN_REWRITER_AUTO_SENTENCE_TREES           = '_wpcc_spinning_spin_rewriter_auto_sentence_trees';     // bool     True if auto sentence trees feature should be enabled
    const WPCC_SPINNING_SPIN_REWRITER_USE_ONLY_SYNONYMS             = '_wpcc_spinning_spin_rewriter_use_only_synonyms';       // bool     True if use only synonyms feature should be enabled
    const WPCC_SPINNING_SPIN_REWRITER_REORDER_PARAGRAPHS            = '_wpcc_spinning_spin_rewriter_reorder_paragraphs';      // bool     True if reorder paragraphs feature should be enabled
    const WPCC_SPINNING_SPIN_REWRITER_TEXT_WITH_SPINTAX             = '_wpcc_spinning_spin_rewriter_text_with_spintax';       // bool     True if the text should be returned in spintax format
    const WPCC_SPINNING_SPIN_REWRITER_TEST                          = '_wpcc_spinning_spin_rewriter_test';                    // string   Text for testing Spin Rewriter

    const WPCC_SPINNING_TURKCE_SPIN_API_TOKEN                       = '_wpcc_spinning_turkce_spin_api_token';                 // string   API token for Turkce Spin
    const WPCC_SPINNING_TURKCE_SPIN_TEST                            = '_wpcc_spinning_turkce_spin_test';                      // string   Text for testing Turkce Spin

    // SEO
    const WPCC_META_KEYWORDS_META_KEY                               = '_wpcc_meta_keywords_meta_key';    // string   Post meta key to store meta keywords
    const WPCC_META_DESCRIPTION_META_KEY                            = '_wpcc_meta_description_meta_key'; // string   Post meta key to store meta description
    const WPCC_TEST_FIND_REPLACE                                    = '_wpcc_test_find_replace';         // string   Test code for find-and-replaces
    const WPCC_FIND_REPLACE                                         = '_wpcc_find_replace';              // array    An array including what to find and with what to replace for page

    // Notifications
    const WPCC_IS_NOTIFICATION_ACTIVE                               = '_wpcc_is_notification_active';               // bool     True if the notifications should be activated.
    const WPCC_NOTIFICATION_EMAIL_INTERVAL_FOR_SITE                 = '_wpcc_notification_email_interval_for_site'; // int      Number of minutes that should pass before sending another similar notification about the same site
    const WPCC_NOTIFICATION_EMAILS                                  = '_wpcc_notification_emails';                  // array    An array of emails to which notifications can be sent

    // Advanced
    const WPCC_MAKE_SURE_ENCODING_UTF8                              = '_wpcc_make_sure_encoding_utf8';  // bool     True if the target pages should be crawled in UTF8, false otherwise.
    const WPCC_CONVERT_CHARSET_TO_UTF8                              = '_wpcc_convert_charset_to_utf8';  // bool     True if the charset of the HTML should be converted to UTF8
    const WPCC_HTTP_USER_AGENT                                      = '_wpcc_http_user_agent';          // string   The user agent for the crawler
    const WPCC_HTTP_ACCEPT                                          = '_wpcc_http_accept';              // string   The user agent for the crawler
    const WPCC_HTTP_ALLOW_COOKIES                                   = '_wpcc_http_allow_cookies';       // bool     True if cookies are allowed, false otherwise
    const WPCC_DISABLE_SSL_VERIFICATION                             = '_wpcc_disable_ssl_verification'; // bool     True if SSL check should be disabled, false otherwise
    const WPCC_USE_PROXY                                            = '_wpcc_use_proxy';                // bool     True if a proxy should be used when the target page cannot be opened
    const WPCC_CONNECTION_TIMEOUT                                   = '_wpcc_connection_timeout';       // int      Maximum allowed number of seconds in which the response should be retrieved
    const WPCC_TEST_URL_PROXY                                       = '_wpcc_test_url_proxy';           // string   A URL that will be used when testing proxies
    const WPCC_PROXIES                                              = '_wpcc_proxies';                  // string   New line-separated proxy addresses
    const WPCC_PROXY_TRY_LIMIT                                      = '_wpcc_proxy_try_limit';          // int      Maximum number of proxies that can be tried for one request
    const WPCC_PROXY_RANDOMIZE                                      = '_wpcc_proxy_randomize';          // bool     True if the proxies should be randomized before usage.

    const WPCC_DISABLE_TOOLTIP                                      = '_wpcc_disable_tooltip';          // bool     True if Tooltip used in the UI should be disabled. Otherwise, false.
    const WPCC_REFRESH_DOCS_LABEL_INDEX                             = '_wpcc_refresh_docs_label_index'; //          This does not store any value in the database. This is just a button.

    /* ***************************************
     *
     * TOOLS PAGE'S SETTING KEYS
     *
     */

    // Manual crawling tool
    const WPCC_TOOLS_SITE_ID                                    = '_wpcc_tools_site_id';                        // int      ID of the site whose settings should be used to crawl the posts
    const WPCC_TOOLS_CATEGORY_ID                                = '_wpcc_tools_category_id';                    // int      ID of the category into which the posts should be saved
    const WPCC_TOOLS_POST_URLS                                  = '_post_urls';                                 // string   URLs of the posts that should be crawled
    const WPCC_TOOLS_POST_AND_FEATURED_IMAGE_URLS               = '_post_and_featured_image_urls';              // array    URLs of posts and their featured images that should be crawled
    const WPCC_TOOLS_CATEGORY_URLS                              = '_category_urls';                             // array    URLs of the categories from which the post URLs should be extracted
    const WPCC_TOOLS_MAX_POSTS_TO_BE_CRAWLED                    = '_max_posts_to_be_crawled';                   // int      Maximum number of posts that should be crawled before pausing
    const WPCC_TOOLS_MAX_PARALLEL_CRAWLING_COUNT                = '_max_parallel_crawling_count';               // int      Maximum number of posts that can crawled in parallel
    const WPCC_TOOLS_MANUAL_CRAWLING_TOOL_CLEAR_AFTER_SUBMIT    = '_manual_crawling_tool_clear_after_submit';   // bool     When checked, indicates that URLs entered into the manual crawling tool should be cleared

    // Manual recrawling tool
    const WPCC_TOOLS_RECRAWL_POST_ID                            = '_wpcc_tools_recrawl_post_id';                // int      ID of the post that should be recrawled

    // Clear URLs tool
    const WPCC_TOOLS_CLEAR_URLS_SITE_ID                         = '_wpcc_tools_clear_urls_site_id';             // int      ID of the site whose URL should be deleted
    const WPCC_TOOLS_URL_TYPE                                   = '_wpcc_tools_url_type';                       // string   Type of URLs that should be deleted
    const WPCC_TOOLS_SAFETY_CHECK                               = '_wpcc_tools_safety_check';                   // bool     User checks this to indicate he/she is sure about what will happen next

    /* ***************************************
     *
     * DASHBOARD PAGE'S SETTING KEYS
     *
     */

    const WPCC_DASHBOARD_COUNT_LAST_CRAWLED_POSTS   = '_wpcc_dashboard_count_last_crawled_posts';       // int      Stores how many posts in "last crawled" section should be shown
    const WPCC_DASHBOARD_COUNT_LAST_URLS            = '_wpcc_dashboard_count_last_urls';                // int      Stores how many posts in "last URLs added to queue" section should be shown
    const WPCC_DASHBOARD_COUNT_LAST_RECRAWLED_POSTS = '_wpcc_dashboard_count_last_recrawled_posts';     // int      Stores how many posts in "last recrawled" section should be shown
    const WPCC_DASHBOARD_COUNT_LAST_DELETED_URLS    = '_wpcc_dashboard_count_last_deleted_urls';        // int      Stores how many posts in "URLs of last deleted posts" section should be shown
}
