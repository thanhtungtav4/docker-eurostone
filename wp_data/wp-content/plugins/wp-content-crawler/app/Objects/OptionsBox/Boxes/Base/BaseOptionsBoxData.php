<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/12/2018
 * Time: 18:35
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\OptionsBox\Boxes\Base;


abstract class BaseOptionsBoxData {

    /** @var array */
    private $data;

    /**
     * @param string|array $rawData JSON or array
     * @param bool         $unslash True if you want to unslash raw JSON data.
     */
    public function __construct($rawData, $unslash = true) {
        // Prepare the data
        $this->prepareData($rawData, $unslash);
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /*
     *
     */

    /**
     * Prepares the value of {@link data}.
     *
     * @param mixed $rawData
     * @param bool  $unslash
     * @since 1.8.0
     */
    private function prepareData($rawData, $unslash): void {
        // If the raw data does not exist, set the data as an empty array.
        if (!$rawData) {
            $this->data = [];
            return;
        }

        // If the raw data is not an array, then parse it to JSON.
        if (!is_array($rawData)) {

            // If the raw data should be unslashed
            if ($unslash) {
                // Unslash and reslash backward slashes to create a valid JSON. Unescaped backslashes are not valid in JSON.
                $rawData = str_replace('\\', '\\\\', wp_unslash($rawData));
            }

            $this->data = json_decode($rawData, true);

        } else {
            $this->data = $rawData;
        }
    }
}