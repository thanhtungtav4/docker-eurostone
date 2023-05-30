<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 21/02/2019
 * Time: 21:59
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Test\Base;


use Exception;
use Illuminate\Contracts\View\View;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformAPIClient;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformationService;
use WPCCrawler\Objects\Transformation\Exceptions\TransformationFailedException;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Utils;

abstract class AbstractTransformationTest extends AbstractTest {

    /** @var string|null */
    private $message;

    /**
     * Create the transformation service that will be used to perform the test
     *
     * @return AbstractTransformationService
     * @since 1.9.0
     */
    protected abstract function createTransformationService(): AbstractTransformationService;

    /**
     * Get the key using which serialized transformation options can be retrieved from the test data
     *
     * @return string
     * @since 1.9.0
     */
    protected abstract function getSerializedOptionsDataKey(): string;

    /**
     * Get the option key storing the selected transformation service.
     *
     * @return string
     * @since 1.9.0
     */
    protected abstract function getSelectedServiceOptionKey(): string;

    /**
     * Create a short message describing the test results. E.g. "Translation results for $apiName"
     *
     * @param string $apiName
     * @return string
     * @since 1.9.0
     */
    protected abstract function createTestResultMessage(string $apiName): string;

    /**
     * Conduct the test and return an array of results.
     *
     * @param TestData $data Information required for the test
     * @return array|null
     */
    protected function createResults($data): ?array {
        // Here, form item values must be a string and it must contain the test text to be transformed.
        if(!$data->getFormItemValues() || is_array($data->getFormItemValues())) return [];

        $serializedOptionsKey = $this->getSerializedOptionsDataKey();

        // Serialized transformation options must exist as well.
        $serializedTransformationOptions = $data->get($serializedOptionsKey);
        if (!$serializedTransformationOptions) return [];

        // Get the transformation settings as an array
        parse_str($serializedTransformationOptions, $transformationSettings);
        if (!$transformationSettings) return [];

        $settings = new SettingsImpl($transformationSettings, Factory::postService()->getSingleMetaKeys());
        $settings->setDoNotUseGeneralSettings(true);

        $service = $this->createTransformationService();
        $selectedServiceOptionKey = $this->getSelectedServiceOptionKey();

        // Get the selected API service key from settings so that we can find the name of the API. We get the name just
        // to show it in the message of this test.
        $apiOptionsForSelect = $service->getOptionsForSelect();
        $serviceType = $settings->getSetting($selectedServiceOptionKey);
        $apiName = Utils::array_get($apiOptionsForSelect, "{$serviceType}.name", _wpcc('Unknown'));

        // Try to transform the text using the given settings
        $clientMessage = '';
        $transformed = [];
        try {
            // Create the API client
            $client = $service->createApiClientUsingSettings($settings);
            if ($client) {
                // Add the client message if it exists
                $clientMessage = $client->getTestResultMessage($settings);

                // Get the text to be transformed
                $text = $data->getFormItemValues();
                $transformed = $this->performTest($service, $client, $text);
            }

        } catch (Exception $e) {
            // Inform the user about the error.
            Informer::addError($e->getMessage())->setException($e)->addAsLog();
        }

        // Create the message
        $this->message = $this->createTestResultMessage($apiName);
        if ($clientMessage) $this->message .= ' ' . $clientMessage;

        return $transformed;
    }

    /**
     * @param AbstractTransformationService $service
     * @param AbstractTransformAPIClient    $client
     * @param string|null                   $text
     * @return array|null
     * @throws TransformationFailedException
     * @since 1.9.0
     */
    protected function performTest(AbstractTransformationService $service, AbstractTransformAPIClient $client, $text) {
        return $service->transformWithApiClient($client, [$text]);
    }

    /**
     * Create the view of the response
     *
     * @return View
     * @throws Exception
     */
    protected function createView() {
        return Utils::view('partials/test-result')
            ->with("results", $this->getResults())
            ->with("message", $this->message ?: '');
    }

}