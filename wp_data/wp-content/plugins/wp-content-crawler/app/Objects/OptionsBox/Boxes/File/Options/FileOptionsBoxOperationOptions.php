<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/12/2018
 * Time: 16:05
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\OptionsBox\Boxes\File\Options;


use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxOptions;
use WPCCrawler\Utils;

class FileOptionsBoxOperationOptions extends BaseOptionsBoxOptions {

    /** @var string[]|null */
    private $movePaths;

    /** @var string[]|null */
    private $copyPaths;

    protected function prepare(): void {
        $this->movePaths = $this->getPathOptions('move');
        $this->copyPaths = $this->getPathOptions('copy');
    }

    /*
     * GETTERS
     */

    /**
     * @return string[]|null
     */
    public function getMovePaths(): ?array {
        return $this->movePaths;
    }

    /**
     * @return string[]|null
     */
    public function getCopyPaths(): ?array {
        return $this->copyPaths;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Get options for an option key that stores 'path' in it.
     *
     * @param string $key Option key under which the paths are stored
     * @return array An array of strings. Each string is a path.
     * @since 1.8.0
     */
    private function getPathOptions($key) {
        return array_unique(array_map(function($v) {

            // Make sure the paths do not have a directory separator in the beginning and in the end
            return $v && isset($v['path']) ? trim(trim(trim($v['path']), '/'), DIRECTORY_SEPARATOR) : null;

        }, Utils::array_get($this->getRawData(), $key, [])));
    }
}