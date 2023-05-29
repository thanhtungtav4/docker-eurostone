<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 23/07/17
 * Time: 09:28
 */

namespace WPCCrawler\Objects\Transformation\Translation;

use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformer;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformationService;

/**
 * Translates a {@link Transformable} instance from a language to another one
 *
 * @since 1.9.0
 */
class Translator extends AbstractTransformer {

    /**
     * @return AbstractTransformationService
     * @since 1.9.0
     */
    protected function getTransformationService() {
        return TranslationService::getInstance();
    }

    /**
     * @return string The option key (post meta key) that stores the values of custom transformable fields selected by
     *                the user. E.g. '_translatable_fields'
     * @since 1.9.0
     */
    protected function getTransformableFieldsOptionKey() {
        return SettingKey::TRANSLATABLE_FIELDS;
    }
}
