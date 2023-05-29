<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 15.10.2019
 * Time: 16:09
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects;


use WPCCrawler\Environment;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\PostDetail\PostDetailsService;
use WPCCrawler\Utils;

class SitePostTypeCreator {

    /** @var SitePostTypeCreator */
    private static $instance = null;

    /** @var bool Stores whether the post type is created or not. */
    private $created = false;

    /** @var string Key of the post type */
    private $postType;

    /*
     * META BOX IDS
     */

    /** @var string ID of the meta box that stores all of the settings */
    private $settingsMetaBoxId;

    /** @var string ID of the meta box that stores simple notes */
    private $notesMetaBoxId;

    /*
     * COLUMN KEYS
     */

    /** @var string */
    private $colKeyAuthor;

    /** @var string */
    private $colKeyActive;

    /** @var string */
    private $colKeyActiveRecrawling;

    /** @var string */
    private $colKeyActivePostDeleting;

    /** @var string */
    private $colKeyCounts;

    /** @var string */
    private $colKeyLastChecked;

    /** @var string */
    private $colKeyLastCrawled;

    /** @var string */
    private $colKeyLastRecrawled;

    /** @var string */
    private $colKeyLastDeleted;

    /** @var string */
    private $colKeyDate;

    /*
     *
     */

    /** @var string[] Keys of the sortable columns */
    private $sortableColKeys;

    /** @var string[] Keys of the columns that display a checkbox */
    private $checkboxColKeys;

    /** @var string[] Keys of the columns that display a date */
    private $dateColKeys;

    /**
     * @return SitePostTypeCreator
     * @since 1.9.0
     */
    public static function getInstance(): SitePostTypeCreator {
        if (static::$instance === null) {
            static::$instance = new SitePostTypeCreator();
        }

        return static::$instance;
    }

    /**
     * This is a singleton.
     *
     * @since 1.9.0
     */
    private function __construct() {
        $this->postType             = Environment::postType();
        $this->settingsMetaBoxId    = Environment::siteSettingsMetaBoxId();
        $this->notesMetaBoxId       = Environment::siteSettingsNotesMetaBoxId();

        $this->colKeyAuthor             = "author";
        $this->colKeyActive             = SettingKey::ACTIVE;
        $this->colKeyActiveRecrawling   = SettingKey::ACTIVE_RECRAWLING;
        $this->colKeyActivePostDeleting = SettingKey::ACTIVE_POST_DELETING;
        $this->colKeyCounts             = "counts";
        $this->colKeyLastChecked        = SettingKey::CRON_LAST_CHECKED_AT;
        $this->colKeyLastCrawled        = SettingKey::CRON_LAST_CRAWLED_AT;
        $this->colKeyLastRecrawled      = SettingKey::CRON_RECRAWL_LAST_CRAWLED_AT;
        $this->colKeyLastDeleted        = SettingKey::CRON_LAST_DELETED_AT;
        $this->colKeyDate               = "date";

        $this->checkboxColKeys = [
            $this->colKeyActive,
            $this->colKeyActiveRecrawling,
            $this->colKeyActivePostDeleting,
        ];

        $this->dateColKeys = [
            $this->colKeyLastChecked,
            $this->colKeyLastCrawled,
            $this->colKeyLastRecrawled,
            $this->colKeyLastDeleted,
        ];

        $this->sortableColKeys = [
            $this->colKeyActive,
            $this->colKeyActiveRecrawling,
            $this->colKeyActivePostDeleting,
            $this->colKeyLastChecked,
            $this->colKeyLastCrawled,
            $this->colKeyLastRecrawled,
            $this->colKeyLastDeleted,
        ];
    }

    /**
     * Registers "wcc_sites" post type to WordPress
     *
     * @since 1.9.0
     */
    public function create(): void {
        // If already created, stop.
        if ($this->created) return;

        // Mark as created
        $this->created = true;

        // Add custom post type and configure it
        add_action('init', function () {
            $this->registerPostType();
        });

        // Set columns
        add_filter(sprintf('manage_%s_posts_columns', $this->getPostType()), function($columns) {
            return $this->getColumns($columns);
        });

        // Set sortable columns
        add_filter(sprintf('manage_edit-%s_sortable_columns', $this->getPostType()), function($columns) {
            return $this->prepareSortableColumns($columns);
        });

        // Sort the columns when the user wants it
        add_action("load-edit.php", function() {
            add_filter('request', function($vars) {
                return $this->handleSortRequest($vars);
            });
        });

        // Set column contents
        add_filter(sprintf('manage_%s_posts_custom_column', $this->getPostType()), function($columnName, $postId) {
            $this->showColumnContent($columnName, $postId);
        }, 10, 2);

        // Handle post row actions
        add_filter('post_row_actions', function ($actions) {
            return $this->preparePostRowActions($actions);
        }, 10, 1);

        // Set interaction messages
        add_filter('post_updated_messages', function ($messages) {
            return $this->prepareInteractionMessages($messages);
        });

        add_filter('enter_title_here', function($title) {
            return $this->prepareTitleInputPlaceholder($title);
        });

        add_filter('admin_head', function () {
            $this->addNonceToListingPage();
        });

        // Add the meta boxes storing the settings of the post type
        add_action('add_meta_boxes', function () {
            $this->addMetaBoxes();
        });

        // Add a class to the meta box to be able to differentiate it from other meta boxes. In this case, we want
        // the meta box not sortable, because WYSIWYG editor does not like being moved around, and the meta box will
        // have several WYSIWYG editors inside.
        add_filter(sprintf('postbox_classes_%s_%s', $this->getPostType(), $this->getSettingsMetaBoxId()),
            function($classes) {
                $classes[] = 'not-sortable';
                return $classes;
            }
        );

        add_action('admin_enqueue_scripts', function ($hook) {
            // Add styles and scripts for post settings
            $this->enqueueScriptsForEditPage($hook);

            // Add styles and scripts for site list
            $this->enqueueScriptsForListingPage($hook);
        });

        // Save options when the post is saved
        add_action('post_updated', function($postId, $postAfter, $postBefore) {
            Factory::postService()->postSettingsMetaBox($postId, $postAfter, $postBefore);
        }, 10, 3);

        // Handle what happens when a post of the custom post type is deleted
        add_action('admin_init', function() {
            add_action('delete_post', function($postId) {
                $this->handlePostDeletion($postId);
            });
        });

        // Show notices
        add_action('admin_notices', function() {
            $this->showNotices();
        });
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Registers the post type
     *
     * @since 1.9.0
     */
    private function registerPostType(): void {
        $labels = [
            'name'                  => _wpcc('Sites'),
            'singular_name'         => _wpcc('Site'),
            'menu_name'             => _wpcc('Content Crawler'),
            'name_admin_bar'        => _wpcc('Content Crawler Site'),
            'add_new'               => _wpcc('Add New'),
            'add_new_item'          => _wpcc('Add New Site'),
            'new_item'              => _wpcc('New Site'),
            'edit_item'             => _wpcc('Edit Site'),
            'view_item'             => _wpcc('View Site'),
            'all_items'             => _wpcc('All Sites'),
            'search_items'          => _wpcc('Search Sites'),
            'parent_item_colon'     => _wpcc('Parent Sites:'),
            'not_found'             => _wpcc('No sites found.'),
            'not_found_in_trash'    => _wpcc('No sites found in Trash.')
        ];

        $args = [
            'public'                => false,
            'labels'                => $labels,
            'description'           => _wpcc('A custom post type which stores sites to be crawled'),
            'menu_icon'             => 'dashicons-tickets-alt',
            'show_ui'               => true,
            'show_in_admin_bar'     => true,
            'show_in_menu'          => true,
            'supports'              => []
        ];

        // Register the post type
        register_post_type($this->getPostType(), $args);

        // Remove text editor
        remove_post_type_support($this->getPostType(), 'editor');
    }

    /**
     * Get columns of the custom post type's listing table
     *
     * @param array $existingColumns Existing columns provided in 'manage_%s_posts_columns' filter, where %s is the
     *                               post type.
     * @return array New columns
     * @since 1.9.0
     */
    private function getColumns($existingColumns) {
        unset($existingColumns["date"]);
        $newColumns = [
            $this->colKeyAuthor             => _wpcc("Author"),
            $this->colKeyActive             => _wpcc("Active for scheduling"),
            $this->colKeyActiveRecrawling   => _wpcc("Active for recrawling"),
            $this->colKeyActivePostDeleting => _wpcc("Active for deleting"),
            $this->colKeyCounts             => _wpcc("Counts"),
            $this->colKeyLastChecked        => _wpcc("Last URL Collection"),
            $this->colKeyLastCrawled        => _wpcc("Last Post Crawl"),
            $this->colKeyLastRecrawled      => _wpcc("Last Post Recrawl"),
            $this->colKeyLastDeleted        => _wpcc("Last Post Delete"),
            $this->colKeyDate               => __("Date")
        ];

        return array_merge($existingColumns, $newColumns);
    }

    /**
     * Prepare sortable columns of the post type's listing table
     *
     * @param array $existingColumns Existing columns provided in 'manage_edit-%s_sortable_columns' filter, where %s
     *                               is the post type
     * @return array Sortable columns
     * @since 1.9.0
     */
    private function prepareSortableColumns($existingColumns) {
        $sortables = $this->getSortableColumnKeys();

        foreach($sortables as $sortableColKey) {
            $existingColumns[$sortableColKey] = $sortableColKey;
        }

        return $existingColumns;
    }

    /**
     * Handle the sort request made by clicking to one of the sortable columns of the post type's listing table
     *
     * @param array $vars Request variables provided by 'request' filter
     * @return array New request variables prepared to handle the sort request
     * @since 1.9.0
     */
    private function handleSortRequest($vars) {
        $isValid = isset($vars['post_type']) && $vars['post_type'] == $this->getPostType() && isset($vars['orderby']);
        if (!$isValid) return $vars;

        // Get the value of 'orderby'
        $orderBy = $vars['orderby'];

        // If the key exists among the sortable column keys, this is a valid request
        if (in_array($orderBy, $this->getSortableColumnKeys())) {
            // Modify the variables such that the posts are ordered according to the related meta key
            $vars = array_merge($vars, [
                'meta_key'  => $orderBy,
                'orderby'   => 'meta_value'
            ]);
        }

        return $vars;
    }

    /**
     * Echoes the content of the columns of post type's listing table
     *
     * @param string $columnName Key of the column
     * @param int    $postId     ID of the post which this column belongs to
     * @since 1.9.0
     */
    private function showColumnContent($columnName, $postId): void {
        // Show "counts" column's contents
        if ($columnName === $this->colKeyCounts) {
            $allCounts = Factory::postService()->getUrlTableCounts();

            if(!isset($allCounts[$postId])) {
                echo "-";

            } else {
                $counts = $allCounts[$postId];

                $s = '<b>%1$s</b>: %2$d';
                echo
                    sprintf($s, _wpcc("Queue"),     $counts["count_queue"])     . "<br>" .
                    sprintf($s, _wpcc("Saved"),     $counts["count_saved"])     . "<br>" .
                    sprintf($s, _wpcc("Updated"),   $counts["count_updated"])   . "<br>" .
                    sprintf($s, _wpcc("Deleted"),   $counts["count_deleted"])   . "<br>" .
                    sprintf($s, _wpcc("Other"),     $counts["count_other"])     . "<br>" .
                    sprintf($s, _wpcc("Total"),     $counts["count_total"])
                ;
            }

            return;
        }

        // Show checkbox columns' contents
        if (in_array($columnName, $this->getCheckboxColKeys())) {
            $active = get_post_meta($postId, $columnName, true);
            $checkedStr = $active ? ' checked="checked"' : '';
            echo sprintf('<input type="checkbox" name="%1$s" data-post-id="%2$s"%3$s>', $columnName, $postId, $checkedStr);
            return;
        }

        // Show date columns' contents
        if (in_array($columnName, $this->getDateColKeys())) {
            $date = get_post_meta($postId, $columnName, true);
            echo Utils::getDateFormatted($date);
            return;
        }

    }

    /**
     * Prepare actions of a single post's row in the listing table
     *
     * @param array $actions Post row actions provided by 'post_row_actions' filter
     * @return array Modified post row actions
     * @since 1.9.0
     */
    private function preparePostRowActions($actions) {
        $currentScreen = get_current_screen();
        if(!isset($currentScreen->post_type) || $currentScreen->post_type != $this->getPostType()) return $actions;

        // Remove quick edit button
        unset($actions['inline hide-if-no-js']);

        return $actions;
    }

    /**
     * Prepare interaction messages for the custom post type
     *
     * @param array $messages Existing interaction messages provided by 'post_updated_messages' filter
     * @return array Interaction messages that contain the messages related to the custom post type
     * @since 1.9.0
     */
    private function prepareInteractionMessages($messages) {
        $post = get_post();

        $messages[$this->getPostType()] = [
            0 => '',
            1 => _wpcc('Site updated.'),
            2 => _wpcc('Custom field updated.'),
            3 => _wpcc('Custom field deleted.'),
            4 => _wpcc('Site updated.'),
            5 => isset($_GET['revision']) ? sprintf(_wpcc('Site restored to revision from %s'), wp_post_revision_title((int)$_GET['revision'], false)) : false,
            6 => _wpcc('Site published.'),
            7 => _wpcc('Site saved.'),
            8 => _wpcc('Site submitted.'),
            9 => sprintf(
                _wpcc('Site scheduled for: <strong>%1$s</strong>.'),
                $post 
                    ? date_i18n('M j, Y @ G:i', strtotime($post->post_date)) 
                    : ''
            ),
            10 => _wpcc('Site draft updated.'),
        ];

        return $messages;
    }

    /**
     * Get the placeholder text for the post title input
     *
     * @param string $title Existing 'enter title here' text provided by 'enter_title_here' filter
     * @return string New placeholder text for post title input
     * @since 1.9.0
     */
    private function prepareTitleInputPlaceholder($title) {
        $currentScreen = get_current_screen();
        if($currentScreen && $currentScreen->post_type == $this->getPostType()) {
            $title = _wpcc('Enter site name here');
        }

        return $title;
    }

    /**
     * Adds meta boxes storing the settings of the custom post type
     *
     * @since 1.9.0
     */
    private function addMetaBoxes(): void {
        // Add the meta box that will store the settings of the custom post type
        add_meta_box(
            $this->getSettingsMetaBoxId(),
            _wpcc('Settings'),
            function () { echo Factory::postService()->getSettingsMetaBox(); },
            $this->getPostType(),
            'normal',
            'high'
        );

        // Also add a meta box for keeping simple notes.
        add_meta_box(
            $this->getNotesMetaBoxId(),
            _wpcc('Simple Notes'),
            function() { echo Factory::postService()->getNotesMetaBox(); },
            $this->getPostType(),
            'side'
        );
    }

    /**
     * Enqueue styles and scripts of the edit page of the custom post type
     *
     * @param string $hook The hook variable provided by 'admin_enqueue_scripts' filter
     * @since 1.9.0
     */
    private function enqueueScriptsForEditPage($hook): void {
        // Check if we are on the custom post page.
        global $post;
        $valid = ($hook == 'post-new.php' && isset($_GET["post_type"]) && $_GET["post_type"] == $this->getPostType()) ||
            ($hook == 'post.php' && $post && $post->post_type == $this->getPostType());
        if(!$valid) return;

        Factory::assetManager()->addGuides();
        Factory::assetManager()->addPostSettings();

        $settings = $post && isset($post->ID) ? get_post_meta($post->ID) : [];

        // Add assets of the registered post details
        PostDetailsService::getInstance()->addSiteSettingsAssets($settings);

        Factory::assetManager()->addTooltip();
        Factory::assetManager()->addClipboard();
        Factory::assetManager()->addDevTools();
        Factory::assetManager()->addOptionsBox();
        Factory::assetManager()->addMediaEditor();
    }

    /**
     * Enqueue styles and scripts of the listing page of the custom post type
     *
     * @param string $hook The hook variable provided by 'admin_enqueue_scripts' filter
     * @since 1.9.0
     */
    private function enqueueScriptsForListingPage($hook): void {
        // Check if we are on the site list page
        $valid = $hook == 'edit.php' && isset($_GET["post_type"]) && $_GET["post_type"] == $this->getPostType();
        if(!$valid) return;

        Factory::assetManager()->addGuides();
        Factory::assetManager()->addPostList();
    }

    /**
     * Shows notices
     *
     * @since 1.9.0
     */
    private function showNotices(): void {
        // Show notices when there is an error
        $message = get_option('_wpcc_site_notice');
        if (!$message) return;

        echo Utils::view('partials/alert')->with([
            'message'   =>  $message,
            'type'      =>  'error'
        ])->render();

        update_option('_wpcc_site_notice', false);
    }

    /**
     * Adds nonce to site listing page so that we can safely get AJAX requests from site listing page
     *
     * @since 1.9.0
     */
    private function addNonceToListingPage(): void {
        $screen = get_current_screen();

        // Stop if we are not in the custom post type screen we created.
        if (!isset($screen->post_type) || $screen->post_type != $this->getPostType()) return;

        // ADD NONCE
        // This will add the nonce after "All" link above the table (near "Published" link). This is the best
        // place I can come up with.
        add_filter('views_' . $screen->id, function($views) {
            $views['all'] = $views['all'] . wp_nonce_field('wcc-site-list', Environment::nonceName());
            return $views;
        });
    }

    /**
     * Deletes URLs that belong to a custom post type when it is deleted
     *
     * @param int $postId ID of the post
     * @since 1.9.0
     */
    private function handlePostDeletion($postId): void {
        global $post_type;
        if ($post_type != $this->getPostType()) return;

        // Delete all URLs when the site is permanently deleted
        Factory::databaseService()->deleteUrlsBySiteId($postId);
    }

    /*
     * PUBLIC GETTERS
     */

    /**
     * @return string[] See {@link $checkboxColKeys}
     * @since 1.9.0
     */
    public function getCheckboxColKeys() {
        return $this->checkboxColKeys;
    }

    /**
     * @return string[] See {@link $dateColKeys}
     * @since 1.9.0
     */
    public function getDateColKeys() {
        return $this->dateColKeys;
    }

    /**
     * @return string[] See {@link $sortableColKeys}
     * @since 1.9.0
     */
    public function getSortableColumnKeys() {
        return $this->sortableColKeys;
    }

    /**
     * @return string See {@link $postType}
     * @since 1.9.0
     */
    public function getPostType() {
        return $this->postType;
    }

    /**
     * @return string See {@link $settingsMetaBoxId}
     * @since 1.9.0
     */
    public function getSettingsMetaBoxId() {
        return $this->settingsMetaBoxId;
    }

    /**
     * @return string See {@link $notesMetaBoxId}
     * @since 1.9.0
     */
    public function getNotesMetaBoxId() {
        return $this->notesMetaBoxId;
    }
}
