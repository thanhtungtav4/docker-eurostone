<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 12.06.2019
 * Time: 20:35
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Enums;

use WPCCrawler\Environment;

/**
 * Contains the names of the slugs of the plugin's pages defined in WordPress. For example, the plugin has a dashboard
 * page whose slug is 'dashboard'. In order not to hard-code this slug, we store it in this class.
 *
 * @package WPCCrawler\Objects\Enums
 * @since   1.8.1
 */
class PageSlug extends EnumBase {

    const DASHBOARD         = 'dashboard';
    const FEATURE_REQUEST   = 'feature-request';
    const GENERAL_SETTINGS  = 'general-settings';
    const SITE_TESTER       = 'site-tester';
    const TOOLS             = 'tools';

    /**
     * The full page name is the value of "page" parameter in the WordPress page URL. For example, the site tester
     * page's URL is "wp-admin/edit.php?post_type=wcc_sites&page=wp-content-crawler-site-tester". Here, the full page
     * name is "wp-content-crawler-site-tester" where "tester" is the slug of the page. This method creates the full
     * page name, given a valid slug.
     *
     * @param string $slug One of the constants defined in this class.
     * @return null|string If the given slug's value does not exist as a constant in this class, returns null.
     *                     Otherwise, returns the full page name.
     * @since 1.9.0
     */
    public static function getFullPageName(string $slug): ?string {
        // Check the validity of the given slug. If it is not valid, return null.
        if (!static::isValidValue($slug)) {
            return null;
        }

        return Environment::appDomain() . "-" . $slug;
    }
}