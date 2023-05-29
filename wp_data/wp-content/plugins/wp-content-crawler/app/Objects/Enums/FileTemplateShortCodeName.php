<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/12/2018
 * Time: 15:14
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\Enums;


class FileTemplateShortCodeName extends EnumBase {

    const ORIGINAL_FILE_NAME    = 'wcc-file-original-name';
    const PREPARED_FILE_NAME    = 'wcc-file-prepared-name';
    const FILE_EXT              = 'wcc-file-ext';
    const MIME_TYPE             = 'wcc-file-mime-type';
    const FILE_SIZE_BYTE        = 'wcc-file-size-byte';
    const FILE_SIZE_KB          = 'wcc-file-size-kb';
    const FILE_SIZE_MB          = 'wcc-file-size-mb';
    const BASE_NAME             = 'wcc-file-base-name';
    const MD5_HASH              = 'wcc-file-md5-hash';
    const ORIGINAL_TITLE        = 'wcc-file-original-title';
    const ORIGINAL_ALT          = 'wcc-file-original-alt';
    const RANDOM_HASH           = 'wcc-file-rand-hash';
    const LOCAL_URL             = 'wcc-file-local-url';

}