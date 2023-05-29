<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 07/04/2021
 * Time: 19:08
 *
 * @since 1.11.1
 */

namespace WPCCrawler\Objects\Dashboard;


use WP_Post;

class DashboardSite {

    /** @var WP_Post The post that stores the settings of the site, i.e. a post of the "site" post type */
    private $site;


    /** @var int Number of posts saved by the site */
    private $countSaved = 0;

    /** @var int Number of URLs in the queue of the site */
    private $countQueue = 0;

    /** @var int Number of posts deleted by the site */
    private $countDeleted = 0;

    /** @var int Number of posts recrawled (updated) by the site */
    private $countRecrawled = 0;


    /** @var string|null Date of last URL collection performed by the site */
    private $lastCheckedAt;

    /** @var string|null Date of last post crawl  performed by the site*/
    private $lastCrawledAt;

    /** @var string|null Date of last post recrawl (update) performed by the site */
    private $lastRecrawledAt;

    /** @var string|null Date of last post delete (update) performed by the site */
    private $lastDeletedAt;


    /** @var bool True if the site is active for scheduling */
    private $activeScheduling = false;

    /** @var bool True if the site is active for recrawling */
    private $activeRecrawling = false;

    /** @var bool True if the site is active for deleting */
    private $activeDeleting = false;


    /** @var int Number of URLs added to the queue of the site today */
    private $countQueueToday = 0;

    /** @var int Number of posts saved by the site today */
    private $countSavedToday = 0;

    /** @var int Number of posts recrawled by the site today */
    private $countRecrawledToday = 0;

    /** @var int Number of posts deleted by the site today */
    private $countDeletedToday = 0;

    /**
     * @param WP_Post $site See {@link site}
     * @since 1.11.1
     */
    public function __construct(WP_Post $site) {
        $this->site = $site;
    }

    /**
     * @return WP_Post
     * @since 1.11.1
     */
    public function getSite(): WP_Post {
        return $this->site;
    }

    /**
     * @return int
     * @since 1.11.1
     */
    public function getCountSaved(): int {
        return $this->countSaved;
    }

    /**
     * @param int $countSaved
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setCountSaved(int $countSaved): DashboardSite {
        $this->countSaved = $countSaved;
        return $this;
    }

    /**
     * @return int
     * @since 1.11.1
     */
    public function getCountQueue(): int {
        return $this->countQueue;
    }

    /**
     * @param int $countQueue
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setCountQueue(int $countQueue): DashboardSite {
        $this->countQueue = $countQueue;
        return $this;
    }

    /**
     * @return int
     * @since 1.11.1
     */
    public function getCountDeleted(): int {
        return $this->countDeleted;
    }

    /**
     * @param int $countDeleted
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setCountDeleted(int $countDeleted): DashboardSite {
        $this->countDeleted = $countDeleted;
        return $this;
    }

    /**
     * @return int
     * @since 1.11.1
     */
    public function getCountRecrawled(): int {
        return $this->countRecrawled;
    }

    /**
     * @param int $countRecrawled
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setCountRecrawled(int $countRecrawled): DashboardSite {
        $this->countRecrawled = $countRecrawled;
        return $this;
    }

    /**
     * @return string|null
     * @since 1.11.1
     */
    public function getLastCheckedAt(): ?string {
        return $this->lastCheckedAt;
    }

    /**
     * @param string|null $lastCheckedAt
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setLastCheckedAt(?string $lastCheckedAt): DashboardSite {
        $this->lastCheckedAt = $lastCheckedAt;
        return $this;
    }

    /**
     * @return string|null
     * @since 1.11.1
     */
    public function getLastCrawledAt(): ?string {
        return $this->lastCrawledAt;
    }

    /**
     * @param string|null $lastCrawledAt
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setLastCrawledAt(?string $lastCrawledAt): DashboardSite {
        $this->lastCrawledAt = $lastCrawledAt;
        return $this;
    }

    /**
     * @return string|null
     * @since 1.11.1
     */
    public function getLastRecrawledAt(): ?string {
        return $this->lastRecrawledAt;
    }

    /**
     * @param string|null $lastRecrawledAt
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setLastRecrawledAt(?string $lastRecrawledAt): DashboardSite {
        $this->lastRecrawledAt = $lastRecrawledAt;
        return $this;
    }

    /**
     * @return string|null
     * @since 1.11.1
     */
    public function getLastDeletedAt(): ?string {
        return $this->lastDeletedAt;
    }

    /**
     * @param string|null $lastDeletedAt
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setLastDeletedAt(?string $lastDeletedAt): DashboardSite {
        $this->lastDeletedAt = $lastDeletedAt;
        return $this;
    }

    /**
     * @return bool
     * @since 1.11.1
     */
    public function isActiveScheduling(): bool {
        return $this->activeScheduling;
    }

    /**
     * @param bool $activeScheduling
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setActiveScheduling(bool $activeScheduling): DashboardSite {
        $this->activeScheduling = $activeScheduling;
        return $this;
    }

    /**
     * @return bool
     * @since 1.11.1
     */
    public function isActiveRecrawling(): bool {
        return $this->activeRecrawling;
    }

    /**
     * @param bool $activeRecrawling
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setActiveRecrawling(bool $activeRecrawling): DashboardSite {
        $this->activeRecrawling = $activeRecrawling;
        return $this;
    }

    /**
     * @return bool
     * @since 1.11.1
     */
    public function isActiveDeleting(): bool {
        return $this->activeDeleting;
    }

    /**
     * @param bool $activeDeleting
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setActiveDeleting(bool $activeDeleting): DashboardSite {
        $this->activeDeleting = $activeDeleting;
        return $this;
    }

    /**
     * @return int
     * @since 1.11.1
     */
    public function getCountQueueToday(): int {
        return $this->countQueueToday;
    }

    /**
     * @param int $countQueueToday
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setCountQueueToday(int $countQueueToday): DashboardSite {
        $this->countQueueToday = $countQueueToday;
        return $this;
    }

    /**
     * @return int
     * @since 1.11.1
     */
    public function getCountSavedToday(): int {
        return $this->countSavedToday;
    }

    /**
     * @param int $countSavedToday
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setCountSavedToday(int $countSavedToday): DashboardSite {
        $this->countSavedToday = $countSavedToday;
        return $this;
    }

    /**
     * @return int
     * @since 1.11.1
     */
    public function getCountRecrawledToday(): int {
        return $this->countRecrawledToday;
    }

    /**
     * @param int $countRecrawledToday
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setCountRecrawledToday(int $countRecrawledToday): DashboardSite {
        $this->countRecrawledToday = $countRecrawledToday;
        return $this;
    }

    /**
     * @return int
     * @since 1.11.1
     */
    public function getCountDeletedToday(): int {
        return $this->countDeletedToday;
    }

    /**
     * @param int $countDeletedToday
     * @return DashboardSite
     * @since 1.11.1
     */
    public function setCountDeletedToday(int $countDeletedToday): DashboardSite {
        $this->countDeletedToday = $countDeletedToday;
        return $this;
    }

}