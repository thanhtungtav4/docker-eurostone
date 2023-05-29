<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/12/2018
 * Time: 18:18
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\Base;


use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Crawling\Data\PostSaverData;

abstract class BasePostDetailDuplicateChecker {

    /** @var array|null */
    private $options;

    /** @var PostBot|null */
    private $postBot;

    /** @var BasePostDetailData|null */
    private $detailData;

    /**
     * @param PostBot|null            $postBot
     * @param BasePostDetailData|null $detailData
     */
    public function __construct(?PostBot $postBot, ?BasePostDetailData $detailData) {
        $this->postBot    = $postBot;
        $this->detailData = $detailData;
    }

    /**
     * @param PostBot|null $postBot
     */
    public function setPostBot(?PostBot $postBot): void {
        $this->postBot = $postBot;
    }

    /**
     * @param BasePostDetailData|null $detailData
     */
    public function setDetailData(?BasePostDetailData $detailData): void {
        $this->detailData = $detailData;
    }

    /**
     * Create options that will be shown in "duplicate check types" option.
     *
     * @return null|array A key-value pair. Keys are the keys of the options, values are the names that will be shown
     *                    to the user.
     * @since 1.8.0
     */
    abstract protected function createOptions(): ?array;

    /**
     * Implement the logic for checking if the post is duplicate.
     *
     * @param PostSaverData $saverData Data that stores information that can be used for duplicate checking
     * @param array         $values    An array that stores the duplicate check options selected by the user.
     * @return int|false ID of the post if this is duplicate. Otherwise, false.
     * @since 1.8.0
     */
    abstract public function checkForDuplicate(PostSaverData $saverData, array $values);

    /**
     * Get duplicate check options.
     *
     * @return null|array
     * @since 1.8.0
     */
    public function getOptions(): ?array {
        if (!$this->options) $this->options = $this->createOptions();

        return is_array($this->options) ? $this->options : null;
    }

    /**
     * @return PostBot|null
     * @since 1.8.0
     */
    public function getPostBot(): ?PostBot {
        return $this->postBot;
    }

    /**
     * @return BasePostDetailData|null
     */
    public function getDetailData(): ?BasePostDetailData {
        return $this->detailData;
    }
}