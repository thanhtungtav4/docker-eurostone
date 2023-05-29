<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 16/02/2019
 * Time: 10:10
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Spinning;

use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformer;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformationService;

/**
 * Spins (paraphrases) a {@link Transformable} instance
 *
 * @since 1.9.0
 */
class Spinner extends AbstractTransformer {

    /**
     * @return AbstractTransformationService|SpinningService
     * @since 1.9.0
     */
    protected function getTransformationService() {
        return SpinningService::getInstance();
    }

    /**
     * @return string The option key (post meta key) that stores the values of custom transformable fields selected by
     *                the user. E.g. '_translatable_fields'
     * @since 1.9.0
     */
    protected function getTransformableFieldsOptionKey() {
        return SettingKey::SPINNABLE_FIELDS;
    }
}
