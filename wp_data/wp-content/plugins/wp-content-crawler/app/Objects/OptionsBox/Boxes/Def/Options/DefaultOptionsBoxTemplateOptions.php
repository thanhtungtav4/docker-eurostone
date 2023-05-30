<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 30/11/2018
 * Time: 15:10
 */

namespace WPCCrawler\Objects\OptionsBox\Boxes\Def\Options;


use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxOptions;
use WPCCrawler\Utils;

class DefaultOptionsBoxTemplateOptions extends BaseOptionsBoxOptions {

    /** @var bool True if the item should be removed when its value is empty. Otherwise, false. */
    private $isRemoveIfEmpty;

    /** @var array Array of strings. Each string is a template. */
    private $templates;

    /**
     * Prepares the instance variables using the raw data
     */
    protected function prepare(): void {
        // Prepare "remove if empty"
        $rawData = $this->getRawData();
        $this->isRemoveIfEmpty = isset($rawData['remove_if_empty']);

        // Prepare templates
        $this->templates = array_map(function($v) {
            return $v && isset($v['template']) ? $v['template'] : null;
        }, Utils::array_get($rawData, 'templates', []));
    }

    /**
     * @return bool
     */
    public function isRemoveIfEmpty() {
        return $this->isRemoveIfEmpty;
    }

    /**
     * @return array
     */
    public function getTemplates() {
        return $this->templates;
    }


}