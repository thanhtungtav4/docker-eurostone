<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/10/2018
 * Time: 23:26
 */

namespace WPCCrawler\Objects\OptionsBox\Boxes\Def;


use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxData;
use WPCCrawler\Objects\OptionsBox\Boxes\Def\Options\DefaultOptionsBoxCalculationOptions;
use WPCCrawler\Objects\OptionsBox\Boxes\Def\Options\DefaultOptionsBoxGeneralOptions;
use WPCCrawler\Objects\OptionsBox\Boxes\Def\Options\DefaultOptionsBoxTemplateOptions;
use WPCCrawler\Utils;

class DefaultOptionsBoxData extends BaseOptionsBoxData {

    /** @var DefaultOptionsBoxGeneralOptions|null */
    private $general = null;

    /** @var array|null */
    private $findReplace = null;

    /** @var DefaultOptionsBoxTemplateOptions|null */
    private $templates = null;

    /** @var DefaultOptionsBoxCalculationOptions|null */
    private $calculations = null;

    /**
     * @return null|DefaultOptionsBoxGeneralOptions
     */
    public function getGeneralOptions() {
        if ($this->general === null) {
            $general = Utils::array_get($this->getData(), 'general', []);
            $this->general = !$general ? null : new DefaultOptionsBoxGeneralOptions($general);
        }

        return $this->general;
    }

    /**
     * @return array
     */
    public function getFindReplaceOptions() {
        if ($this->findReplace === null) {
            $this->findReplace = Utils::array_get($this->getData(), 'findReplace.find_replace', []);
        }

        return $this->findReplace;
    }

    /**
     * @return null|DefaultOptionsBoxTemplateOptions
     */
    public function getTemplateOptions() {
        if ($this->templates === null) {
            $templates = Utils::array_get($this->getData(), 'templates', []);
            $this->templates = !$templates ? null : new DefaultOptionsBoxTemplateOptions($templates);
        }

        return $this->templates;
    }

    /**
     * @return null|DefaultOptionsBoxCalculationOptions
     */
    public function getCalculationOptions() {
        if ($this->calculations === null) {
            $calculations = Utils::array_get($this->getData(), 'calculations', []);
            $this->calculations = !$calculations ? null : new DefaultOptionsBoxCalculationOptions($calculations);
        }

        return $this->calculations;
    }
}