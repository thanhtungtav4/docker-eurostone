<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 07/04/2021
 * Time: 22:08
 *
 * @since 1.11.1
 */

namespace WPCCrawler\Objects\Dashboard;


use WP_Post;

class DashboardPost {

    /** @var WP_Post A WordPress post saved by the plugin */
    private $post;

    /** @var DashboardUrlTuple The URL of the saved post */
    private $urlTuple;

    /**
     * @param WP_Post           $post     See {@link post}
     * @param DashboardUrlTuple $urlTuple See {@link urlTuple}
     * @since 1.11.1
     */
    public function __construct(WP_Post $post, DashboardUrlTuple $urlTuple) {
        $this->post     = $post;
        $this->urlTuple = $urlTuple;
    }

    /**
     * @return WP_Post See {@link post}
     * @since 1.11.1
     */
    public function getPost(): WP_Post {
        return $this->post;
    }

    /**
     * @return DashboardUrlTuple See {@link urlTuple}
     * @since 1.11.1
     */
    public function getUrlTuple(): DashboardUrlTuple {
        return $this->urlTuple;
    }

}