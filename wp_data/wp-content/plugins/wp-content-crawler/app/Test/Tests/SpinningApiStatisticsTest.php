<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/02/2019
 * Time: 12:29
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Test\Tests;


use WPCCrawler\Objects\Transformation\Base\AbstractTransformAPIClient;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformationService;
use WPCCrawler\Objects\Transformation\Spinning\Clients\AbstractSpinningAPIClient;

class SpinningApiStatisticsTest extends SpinningTest {

    protected function performTest(AbstractTransformationService $service, AbstractTransformAPIClient $client, $text) {
        /** @var AbstractSpinningAPIClient $client */
        $results = $client->getUsageStatistics();
        if (!$results) return [];

        $prepared = [];
        foreach ($results as $key => $value) {
            $prepared[] = $key . ': ' . $value;
        }

        return $prepared;
    }

    /**
     * Create a short message describing the test results. E.g. "Translation results for $apiName"
     *
     * @param string $apiName
     * @return string
     * @since 1.9.0
     */
    protected function createTestResultMessage(string $apiName): string {
        return sprintf(_wpcc('Usage statistics for <b>%1$s</b>'), $apiName) . ':';
    }

}