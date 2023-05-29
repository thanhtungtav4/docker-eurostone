<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 22/12/2018
 * Time: 10:57
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\OptionsBox\Enums\TabOptions;


use WPCCrawler\Objects\Enums\EnumBase;

class FileTemplatesTabOptions extends EnumBase {

    const OPTION_ALLOWED_TEMPLATE_TYPES     = 'allowedTemplateTypes';
    const TEMPLATE_TYPE_FILE_NAME           = 'file-name-templates';
    const TEMPLATE_TYPE_MEDIA_TITLE         = 'media-title-templates';
    const TEMPLATE_TYPE_MEDIA_DESCRIPTION   = 'media-description-templates';
    const TEMPLATE_TYPE_MEDIA_CAPTION       = 'media-caption-templates';
    const TEMPLATE_TYPE_MEDIA_ALT           = 'media-alt-templates';
}