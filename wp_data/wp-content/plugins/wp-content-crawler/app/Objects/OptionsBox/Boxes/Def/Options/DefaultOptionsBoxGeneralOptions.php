<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 30/11/2018
 * Time: 15:42
 */

namespace WPCCrawler\Objects\OptionsBox\Boxes\Def\Options;


use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxOptions;

class DefaultOptionsBoxGeneralOptions extends BaseOptionsBoxOptions {

    /** @var bool True if the item should be treated as JSON. */
    private $isTreatAsJson;

    protected function prepare(): void {
        $rawData = $this->getRawData();
        $this->isTreatAsJson = isset($rawData['treat_as_json']);
    }

    /*
     * GETTERS
     */

    /**
     * @return bool
     */
    public function isTreatAsJson() {
        return $this->isTreatAsJson;
    }

}