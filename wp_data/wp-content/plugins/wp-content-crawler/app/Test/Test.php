<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 12:16
 */

namespace WPCCrawler\Test;


use Exception;
use WPCCrawler\Test\Base\AbstractGeneralTest;
use WPCCrawler\Test\Base\AbstractTest;
use WPCCrawler\Test\Data\GeneralTestData;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Test\Enums\TestType;
use WPCCrawler\Test\General\GeneralCategoryTest;
use WPCCrawler\Test\General\GeneralPostTest;
use WPCCrawler\Test\Tests\CalculationTest;
use WPCCrawler\Test\Tests\CommandTest;
use WPCCrawler\Test\Tests\RefreshDocLinksTest;
use WPCCrawler\Test\Tests\HtmlManipulation\ExchangeElementAttributesTest;
use WPCCrawler\Test\Tests\FileCopyTest;
use WPCCrawler\Test\Tests\FileFindReplaceTest;
use WPCCrawler\Test\Tests\FileMoveTest;
use WPCCrawler\Test\Tests\FileTemplateTest;
use WPCCrawler\Test\Tests\FindReplaceInCustomMetaOrShortCodeTest;
use WPCCrawler\Test\Tests\HtmlManipulation\FindReplaceInElementAttributesTest;
use WPCCrawler\Test\Tests\HtmlManipulation\FindReplaceInElementHtmlTest;
use WPCCrawler\Test\Tests\HtmlManipulation\FindReplaceInHtmlAtFirstLoadTest;
use WPCCrawler\Test\Tests\HtmlManipulation\FindReplaceInRawHtmlTest;
use WPCCrawler\Test\Tests\FindReplaceTest;
use WPCCrawler\Test\Tests\ProxyTest;
use WPCCrawler\Test\Tests\HtmlManipulation\RemoveElementAttributesTest;
use WPCCrawler\Test\Tests\SelectorTest;
use WPCCrawler\Test\Tests\SourceCodeTest;
use WPCCrawler\Test\Tests\SpinningApiStatisticsTest;
use WPCCrawler\Test\Tests\SpinningTest;
use WPCCrawler\Test\Tests\TemplateTest;
use WPCCrawler\Test\Tests\TranslationTest;
use WPCCrawler\Utils;

class Test {

    /** @var string */
    public static $TEST_TYPE_HREF                               = 'test_type_selector_href';
    /** @var string */
    public static $TEST_TYPE_TEXT                               = 'test_type_selector_text';
    /** @var string */
    public static $TEST_TYPE_HTML                               = 'test_type_selector_html';
    /** @var string */
    public static $TEST_TYPE_SRC                                = 'test_type_selector_src';
    /** @var string */
    public static $TEST_TYPE_FIRST_POSITION                     = 'test_type_selector_first_position';
    /** @var string */
    public static $TEST_TYPE_FIND_REPLACE                       = 'test_type_find_replace';
    /** @var string */
    public static $TEST_TYPE_FIND_REPLACE_IN_RAW_HTML           = 'test_type_find_replace_raw_html';
    /** @var string */
    public static $TEST_TYPE_FIND_REPLACE_IN_HTML_AT_FIRST_LOAD = 'test_type_find_replace_in_html_at_first_load';
    /** @var string */
    public static $TEST_TYPE_SELECTOR_ATTRIBUTE                 = 'test_type_selector_attribute';
    /** @var string */
    public static $TEST_TYPE_SOURCE_CODE                        = 'test_type_source_code';
    /** @var string */
    public static $TEST_TYPE_PROXY                              = 'test_type_proxy';
    /** @var string */
    public static $TEST_TYPE_FIND_REPLACE_IN_ELEMENT_ATTRIBUTES = 'test_type_find_replace_in_element_attributes';
    /** @var string */
    public static $TEST_TYPE_EXCHANGE_ELEMENT_ATTRIBUTES        = 'test_type_exchange_element_attributes';
    /** @var string */
    public static $TEST_TYPE_REMOVE_ELEMENT_ATTRIBUTES          = 'test_type_remove_element_attributes';
    /** @var string */
    public static $TEST_TYPE_FIND_REPLACE_IN_ELEMENT_HTML       = 'test_type_find_replace_in_element_html';
    /** @var string */
    public static $TEST_TYPE_FIND_REPLACE_IN_CUSTOM_META        = 'test_type_find_replace_in_custom_meta';
    /** @var string */
    public static $TEST_TYPE_FIND_REPLACE_IN_CUSTOM_SHORT_CODE  = 'test_type_find_replace_in_custom_short_code';
    /** @var string */
    public static $TEST_TYPE_TRANSLATION                        = 'test_type_translation';
    /** @var string */
    public static $TEST_TYPE_SPINNING                           = 'test_type_spinning';
    /** @var string */
    public static $TEST_TYPE_SPINNING_API_STATS                 = 'test_type_spinning_api_stats';
    /** @var string */
    public static $TEST_TYPE_TEMPLATE                           = 'test_type_template';
    /** @var string */
    public static $TEST_TYPE_CALCULATION                        = 'test_type_calculation';

    /** @var string */
    public static $TEST_TYPE_FILE_FIND_REPLACE                  = 'test_type_file_find_replace';
    /** @var string */
    public static $TEST_TYPE_FILE_MOVE                          = 'test_type_file_move';
    /** @var string */
    public static $TEST_TYPE_FILE_COPY                          = 'test_type_file_copy';
    /** @var string */
    public static $TEST_TYPE_FILE_TEMPLATE                      = 'test_type_file_template';

    /** @var string */
    public static $TEST_TYPE_REFRESH_DOC_LINKS                  = 'test_type_refresh_doc_links';
    /** @var string */
    public static $TEST_TYPE_COMMAND                            = 'test_type_command';

    /**
     * @param int    $postId      The ID of the site
     * @param string $testType    One of the values of the array TestService::$GENERAL_TESTS
     * @param string $testUrlPart The URL
     *
     * @return string|null A response including rendered blade view which can be directly appended to an HTML element,
     *                     and data
     * @throws Exception
     */
    public static function respondToGeneralTestRequest($postId, $testType, $testUrlPart): ?string {
        $tests = [
            TestType::POST     => GeneralPostTest::class,
            TestType::CATEGORY => GeneralCategoryTest::class
        ];

        $testData = new GeneralTestData($postId, $testType, $testUrlPart);

        $testClass = Utils::array_get($tests, $testData->getTestType());
        if (!$testClass) return null;

        /** @var class-string<AbstractGeneralTest> $testClass */
        /** @var AbstractGeneralTest $test */
        $test = new $testClass($testData);
        return $test->run()->getResponse();
    }

    /**
     * Respond to AJAX requests made for testing things.
     *
     * @param array $data  Test data
     * @return null|string If request could not be handled, null. Otherwise, JSON.
     * @throws Exception
     */
    public static function respondToTestRequest($data): ?string {
        $tests = [
            static::$TEST_TYPE_FIND_REPLACE                         => FindReplaceTest::class,
            static::$TEST_TYPE_SOURCE_CODE                          => SourceCodeTest::class,
            static::$TEST_TYPE_PROXY                                => ProxyTest::class,
            static::$TEST_TYPE_FIND_REPLACE_IN_RAW_HTML             => FindReplaceInRawHtmlTest::class,
            static::$TEST_TYPE_FIND_REPLACE_IN_HTML_AT_FIRST_LOAD   => FindReplaceInHtmlAtFirstLoadTest::class,
            static::$TEST_TYPE_FIND_REPLACE_IN_ELEMENT_ATTRIBUTES   => FindReplaceInElementAttributesTest::class,
            static::$TEST_TYPE_EXCHANGE_ELEMENT_ATTRIBUTES          => ExchangeElementAttributesTest::class,
            static::$TEST_TYPE_REMOVE_ELEMENT_ATTRIBUTES            => RemoveElementAttributesTest::class,
            static::$TEST_TYPE_FIND_REPLACE_IN_ELEMENT_HTML         => FindReplaceInElementHtmlTest::class,
            static::$TEST_TYPE_FIND_REPLACE_IN_CUSTOM_META          => FindReplaceInCustomMetaOrShortCodeTest::class,
            static::$TEST_TYPE_FIND_REPLACE_IN_CUSTOM_SHORT_CODE    => FindReplaceInCustomMetaOrShortCodeTest::class,
            static::$TEST_TYPE_TRANSLATION                          => TranslationTest::class,
            static::$TEST_TYPE_SPINNING                             => SpinningTest::class,
            static::$TEST_TYPE_TEMPLATE                             => TemplateTest::class,
            static::$TEST_TYPE_CALCULATION                          => CalculationTest::class,
            static::$TEST_TYPE_SPINNING_API_STATS                   => SpinningApiStatisticsTest::class,

            static::$TEST_TYPE_FILE_FIND_REPLACE                    => FileFindReplaceTest::class,
            static::$TEST_TYPE_FILE_MOVE                            => FileMoveTest::class,
            static::$TEST_TYPE_FILE_COPY                            => FileCopyTest::class,
            static::$TEST_TYPE_FILE_TEMPLATE                        => FileTemplateTest::class,

            static::$TEST_TYPE_REFRESH_DOC_LINKS                    => RefreshDocLinksTest::class,
            static::$TEST_TYPE_COMMAND                              => CommandTest::class,
        ];

        $testData = new TestData($data);

        // Get the test class according to the test type
        if (isset($tests[$testData->getTestType()])) {
            $testClass = $tests[$testData->getTestType()];

        } else {
            // If the test class does not exist, then we assume that it is a selector test.
            // There must exist form item values. Otherwise, return null.
            if(!$testData->getFormItemValues()) return null;

            $testClass = SelectorTest::class;
        }

        /** @var class-string<AbstractTest> $testClass */
        /** @var AbstractTest $test */
        $test = new $testClass($testData);

        return $test->run()->getResponse();
    }
    
}