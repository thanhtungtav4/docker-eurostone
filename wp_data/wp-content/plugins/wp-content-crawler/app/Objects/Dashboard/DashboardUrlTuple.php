<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 07/04/2021
 * Time: 21:16
 *
 * @since 1.11.1
 */

namespace WPCCrawler\Objects\Dashboard;


use WP_Post;
use WPCCrawler\Objects\Database\UrlTuple;

class DashboardUrlTuple extends UrlTuple {

    /** @var WP_Post|null The "site" post that owns the URL tuple */
    private $site;

    /**
     * @param WP_Post|null $site See {@link site}
     * @param object       $urlTuple
     * @since 1.11.1
     */
    public function __construct(?WP_Post $site, object $urlTuple) {
        parent::__construct($urlTuple);
        $this->site = $site;
    }

    /**
     * @return WP_Post|null See {@link site}
     * @since 1.11.1
     */
    public function getSite(): ?WP_Post {
        return $this->site;
    }

}