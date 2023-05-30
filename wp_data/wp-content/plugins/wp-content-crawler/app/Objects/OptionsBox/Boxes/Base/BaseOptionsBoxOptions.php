<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 30/11/2018
 * Time: 15:15
 */

namespace WPCCrawler\Objects\OptionsBox\Boxes\Base;


use WPCCrawler\Utils;

abstract class BaseOptionsBoxOptions {

    /** @var array Raw data retrieved from the options box directly */
    private $rawData;

    /**
     * @param array $rawData See {@link rawData}
     */
    public function __construct($rawData) {
        $this->rawData = $rawData ? $rawData : [];

        // Make sure the raw data is an array.
        if (!is_array($this->rawData)) {
            $this->rawData = [];
        }

        // Stop if there is no raw data.
        if (!$rawData) return;

        $this->prepare();
    }

    abstract protected function prepare(): void;

    /**
     * @return bool True if there are options. Otherwise, false.
     * @since 1.11.0
     */
    public function hasOptions(): bool {
        return Utils::hasNonEmptyValues($this->getRawData());
    }

    /**
     * @return array
     */
    public function getRawData() {
        return $this->rawData;
    }

}