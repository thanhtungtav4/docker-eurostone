<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/12/2018
 * Time: 18:37
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\OptionsBox\Boxes\File;


use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxData;
use WPCCrawler\Objects\OptionsBox\Boxes\File\Options\FileOptionsBoxOperationOptions;
use WPCCrawler\Objects\OptionsBox\Boxes\File\Options\FileOptionsBoxTemplateOptions;
use WPCCrawler\Utils;

class FileOptionsBoxData extends BaseOptionsBoxData {

    /** @var null|array */
    private $findReplace = null;

    /** @var null|bool True if this box has find-replace options that can be applied. */
    private $hasFindReplace = null;

    /** @var null|FileOptionsBoxOperationOptions */
    private $fileOperations = null;

    /** @var null|bool True if this box has file operation options that can be applied. */
    private $hasFileOperations = null;

    /** @var null|FileOptionsBoxTemplateOptions */
    private $templates = null;

    /** @var null|bool True if this box has template options that can be applied. */
    private $hasTemplates = null;

    /**
     * @return array
     */
    public function getFindReplaceOptions() {
        if ($this->findReplace === null) {
            $this->findReplace = Utils::array_get($this->getData(), 'fileFindReplace.file_find_replace', []);

            // Make sure it is an array.
            if (!is_array($this->findReplace)) {
                $this->findReplace = [];
            }
        }

        return $this->findReplace;
    }

    /**
     * @return bool See {@link hasFindReplace}
     * @since 1.11.0
     */
    public function hasFindReplaceOptions(): bool {
        if ($this->hasFindReplace === null) {
            $this->hasFindReplace = Utils::hasNonEmptyValues($this->getFindReplaceOptions());
        }

        return $this->hasFindReplace;
    }

    /**
     * @return null|FileOptionsBoxTemplateOptions
     * @since 1.8.0
     */
    public function getTemplateOptions() {
        if ($this->templates === null) {
            $templates = Utils::array_get($this->getData(), 'fileTemplates', []);
            $this->templates = !$templates ? null : new FileOptionsBoxTemplateOptions($templates);
        }

        return $this->templates;
    }

    /**
     * @return bool See {@link hasTemplates}
     * @since 1.11.0
     */
    public function hasTemplateOptions(): bool {
        if ($this->hasTemplates === null) {
            $options = $this->getTemplateOptions();
            $this->hasTemplates = $options && $options->hasOptions();
        }

        return $this->hasTemplates;
    }

    /**
     * @return null|FileOptionsBoxOperationOptions
     * @since 1.8.0
     */
    public function getFileOperationOptions() {
        if ($this->fileOperations === null) {
            $fileOperations = Utils::array_get($this->getData(), 'fileOperations', []);
            $this->fileOperations = !$fileOperations ? null : new FileOptionsBoxOperationOptions($fileOperations);
        }

        return $this->fileOperations;
    }

    /**
     * @return bool See {@link hasFileOperations}
     * @since 1.11.0
     */
    public function hasFileOperationOptions(): bool {
        if ($this->hasFileOperations === null) {
            $options = $this->getFileOperationOptions();
            $this->hasFileOperations = $options && $options->hasOptions();
        }

        return $this->hasFileOperations;
    }

}