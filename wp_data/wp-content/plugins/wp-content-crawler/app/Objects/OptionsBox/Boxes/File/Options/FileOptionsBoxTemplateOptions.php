<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/12/2018
 * Time: 15:57
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\OptionsBox\Boxes\File\Options;


use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxOptions;
use WPCCrawler\Utils;

class FileOptionsBoxTemplateOptions extends BaseOptionsBoxOptions {

    /** @var null|string[] */
    private $fileNameTemplates = null;

    /** @var null|string[] */
    private $mediaTitleTemplates = null;

    /** @var null|string[] */
    private $mediaDescriptionTemplates = null;

    /** @var null|string[] */
    private $mediaCaptionTemplates = null;

    /** @var null|string[] */
    private $mediaAltTemplates = null;

    protected function prepare(): void {
        $this->fileNameTemplates            = $this->getTemplates('templates_file_name');
        $this->mediaTitleTemplates          = $this->getTemplates('templates_media_title');
        $this->mediaDescriptionTemplates    = $this->getTemplates('templates_media_description');
        $this->mediaCaptionTemplates        = $this->getTemplates('templates_media_caption');
        $this->mediaAltTemplates            = $this->getTemplates('templates_media_alt_text');
    }

    /*
     * GETTERS
     */

    /**
     * @return string[]|null
     */
    public function getFileNameTemplates(): ?array {
        return $this->fileNameTemplates;
    }

    /**
     * @return string[]|null
     */
    public function getMediaTitleTemplates(): ?array {
        return $this->mediaTitleTemplates;
    }

    /**
     * @return string[]|null
     */
    public function getMediaDescriptionTemplates(): ?array {
        return $this->mediaDescriptionTemplates;
    }

    /**
     * @return string[]|null
     */
    public function getMediaCaptionTemplates(): ?array {
        return $this->mediaCaptionTemplates;
    }

    /**
     * @return string[]|null
     */
    public function getMediaAltTemplates(): ?array {
        return $this->mediaAltTemplates;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Get templates for an option key.
     *
     * @param string $key Option key under which the templates are stored
     * @return string[] An array of strings. Each string is a template.
     * @since 1.8.0
     */
    private function getTemplates($key): array {
        // Prepare templates
        return array_map(function($v) {
            return $v && isset($v['template']) && $v['template'] ? $v['template'] : null;
        }, Utils::array_get($this->getRawData(), $key, []));
    }
}