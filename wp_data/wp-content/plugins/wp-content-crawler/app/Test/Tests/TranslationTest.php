<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/10/2018
 * Time: 08:34
 */

namespace WPCCrawler\Test\Tests;


use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformationService;
use WPCCrawler\Objects\Transformation\Translation\TranslationService;
use WPCCrawler\Test\Base\AbstractTransformationTest;

class TranslationTest extends AbstractTransformationTest {

    /**
     * Create the transformation service that will be used to perform the test
     *
     * @return AbstractTransformationService
     * @since 1.9.0
     */
    protected function createTransformationService(): AbstractTransformationService {
        return TranslationService::getInstance();
    }

    /**
     * Get the key using which serialized transformation options can be retrieved from the test data
     *
     * @return string
     * @since 1.9.0
     */
    protected function getSerializedOptionsDataKey(): string {
        return 'serializedTranslationOptions';
    }

    /**
     * Get the option key storing the selected transformation service.
     *
     * @return string
     * @since 1.9.0
     */
    protected function getSelectedServiceOptionKey(): string {
        return SettingKey::WPCC_SELECTED_TRANSLATION_SERVICE;
    }

    /**
     * Create a short message describing the test results. E.g. "Translation results for $apiName"
     *
     * @param string $apiName
     * @return string
     * @since 1.9.0
     */
    protected function createTestResultMessage(string $apiName): string {
        return sprintf(_wpcc('Translation test results for <b>%1$s</b>'), $apiName) . ':';
    }
}
