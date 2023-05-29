<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/12/2018
 * Time: 09:38
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\OptionsBox\Boxes\File;


use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxApplier;
use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxApplierFactory;
use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxData;

class FileOptionsBoxApplierFactory extends BaseOptionsBoxApplierFactory {

    /**
     * @param array|string $rawData
     * @param bool         $unslash
     * @return FileOptionsBoxData
     * @since 1.8.0
     */
    public function createData($rawData, $unslash = true): BaseOptionsBoxData {
        return new FileOptionsBoxData($rawData, $unslash);
    }

    /**
     * @param BaseOptionsBoxData $data
     * @return FileOptionsBoxApplier
     * @since 1.8.0
     */
    public function createApplier($data): BaseOptionsBoxApplier {
        return new FileOptionsBoxApplier($data);
    }
}