<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 16/02/2019
 * Time: 10:11
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Spinning\Clients;


use Exception;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformAPIClient;
use WPCCrawler\Objects\Transformation\Spinning\TextSpinner;

abstract class AbstractSpinningAPIClient extends AbstractTransformAPIClient {

    // TODO: The user should be able to select what happens when spinning is not successful. For example, the post's
    //  status might be set to draft. This may even be the default behavior we can implement without asking the user.

    /** @var string[] An array of strings that should not be spun. */
    private $protectedStrings = [];

    /**
     * @var bool True if only one request containing values of every spinnable field should be sent, instead of sending
     * every other value in a new request.
     */
    private $sendOneRequest = false;

    /**
     * Spin texts using the settings. This method might override the options given in the constructor.
     *
     * @param TextSpinner $textSpinner
     * @return array See {@link TextSpinner::spin()}
     * @uses TextSpinner::spin()
     * @since 1.9.0
     * @throws Exception When the settings do not have the required options.
     */
    public abstract function spin(TextSpinner $textSpinner);

    public function setOptionsUsingSettings(SettingsImpl $settings) {
        // If there are protected strings, assign them.
        $protectedStrings = $settings->getSetting(SettingKey::WPCC_SPINNING_PROTECTED_TERMS, '');
        if ($protectedStrings) {
            $this->setProtectedStrings(array_map('trim', explode(',', $protectedStrings)));
        }

        // Get if the user wants to send everything in one request
        $this->sendOneRequest = $settings->getSettingForCheckbox(SettingKey::WPCC_SPINNING_SEND_IN_ONE_REQUEST);

        $this->doSetOptionsUsingSettings($settings);
    }

    /**
     * @param SettingsImpl $settings See {@link setOptionsUsingSettings()}
     * @return void
     * @since 1.9.0
     */
    protected abstract function doSetOptionsUsingSettings(SettingsImpl $settings);

    /**
     * Spin an array of strings.
     *
     * @param array $texts   A flat sequential array of texts
     * @param array $options Spinning options
     * @return array Spun texts in the same order as $texts. If an error occurs, returns an empty array.
     * @since 1.8.0
     */
    public abstract function spinBatch(array $texts, $options = []);

    /**
     * Get usage statistics of the API.
     *
     * @return array|null An associative array where keys are the names of statistics and the values are the values of
     *                    the statistics
     * @since 1.9.0
     */
    public abstract function getUsageStatistics();

    /**
     * @return string[] See {@link $protectedStrings}
     * @since 1.9.0
     */
    public function getProtectedStrings() {
        return $this->protectedStrings;
    }

    /**
     * @param string[] $protectedStrings See {@link $protectedStrings}
     * @since 1.9.0
     */
    public function setProtectedStrings($protectedStrings): void {
        if (!$protectedStrings) $protectedStrings = [];

        $this->protectedStrings = $protectedStrings;
    }

    /**
     * @return bool See {@link sendOneRequest}
     * @since 1.9.0
     */
    public function isSendOneRequest(): bool {
        return $this->sendOneRequest;
    }
}
