<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/01/2019
 * Time: 08:07
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Controllers;


use Exception;
use Illuminate\Contracts\View\View;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Enums\PageSlug;
use WPCCrawler\Objects\FeatureRequest;
use WPCCrawler\Objects\Page\AbstractMenuPage;
use WPCCrawler\Utils;

/**
 * @since 1.9.0
 */
class FeatureRequestController extends AbstractMenuPage {

    /** @var string Name of the input that stores the email address */
    private $inputNameEmail = '_wpcc_feature_request_email';

    /** @var string Name of the input that stores the request content*/
    private $inputNameRequestContent = '_wpcc_feature_request_content';

    /** @var string Query parameter key to retrieve confirmation key from the GET request */
    private $confirmationHashParamKey = 'confirmationKey';

    /** @var string Option key that stores the data of the latest feature request */
    private $optionKeyFormData = '_wpcc_feature_request_data';

    /** @var string Option key that stores the confirmation hash to be sent to the user's email address */
    private $optionKeyConfirmationHash = '_wpcc_feature_request_confirmation_hash';

    /**
     * @return string Menu title for the page
     */
    public function getMenuTitle(): string {
        return _wpcc("Feature Request");
    }

    /**
     * @return string Page title
     */
    public function getPageTitle(): string {
        return _wpcc("Feature Request");
    }

    /**
     * @return string Slug for the page
     */
    public function getPageSlug(): string {
        return PageSlug::FEATURE_REQUEST;
    }

    /**
     * Get view for the page.
     *
     * @return View Not-rendered blade view for the page
     */
    public function getView() {
        Factory::assetManager()->addPostSettings();
        Factory::assetManager()->addFeatureRequest();
        Factory::assetManager()->addTooltip();

        // Create the view for the page
        $view = Utils::view('feature-request.main');

        // Validate the email and send the request if there is a key.
        $confirmationMessage = $this->validateEmailAndSendFeatureRequest();
        if ($confirmationMessage !== null) {
            // If there is message, add it to the view.
            $view->with('confirmationMessage', $confirmationMessage);
        }

        return $view;
    }

    public function handlePOST(): void {
        parent::handlePOST();

        $data = $_POST;

        // Get the email and the request content from the form data
        $email = Utils::array_get($data, $this->inputNameEmail);
        $requestContent = Utils::array_get($data, $this->inputNameRequestContent);

        // Create a feature request object
        try {
            $featureRequest = new FeatureRequest($email, $requestContent);

        } catch (Exception $e) {
            // If there was an error, inform the user.
            $this->redirectBack(false, $e->getMessage());

            // Redirecting back actually exits. However, for the IDE to understand it, we just return. Otherwise,
            // in the following lines, it will tell there might be no $featureRequest.
            return;
        }

        // Create a confirmation hash, save it, and save the feature request
        $hash = $this->createConfirmationHash($featureRequest);
        $hashSaved = $this->saveConfirmationHash($hash);
        $featureRequestSaved = $this->saveFeatureRequestData($featureRequest);

        // If there was an error in doing previous things, stop and inform the user.
        if (!$hash || !$hashSaved || !$featureRequestSaved) {
            $this->redirectBack(false, _wpcc('Feature request could not be stored.'));
        }

        // Send the confirmation email
        $success = $this->sendConfirmationEmail($featureRequest, $hash);
        $message = $success ?
            sprintf(_wpcc('A confirmation email has just been sent to %1$s.'), $featureRequest->getEmail()) :
            _wpcc('Confirmation email could not be sent.');

        // Redirect back and inform the user
        $this->redirectBack($success, $message);
    }

    /*
     *
     */

    /**
     * @return null|string Null if there is nothing to be validated. Otherwise, a message that can be shown to the user
     *                     to inform him/her about the validation/sending feature request.
     * @since 1.9.0
     */
    private function validateEmailAndSendFeatureRequest() {
        // Get the confirmation hash from the URL params
        $key = Utils::array_get($_GET, $this->confirmationHashParamKey);
        if ($key === null) return null;

        // Get the hash from the database
        $hashFromDb = $this->getConfirmationHashFromDb();
        if ($hashFromDb === null) return $this->getKeyNotValidMessage();

        // Check if the hash is the same
        $isValid = $key === $hashFromDb;
        if (!$isValid) return $this->getKeyNotValidMessage();

        // Get the feature request
        $featureRequest = $this->getFeatureRequestDataFromDb();
        if (!$featureRequest || !is_a($featureRequest, FeatureRequest::class)) {
            return _wpcc('There is no feature request in the database.');
        }

        // Remove the key from the database since it is valid
        delete_option($this->optionKeyConfirmationHash);

        // Remove the feature request from the database
        delete_option($this->optionKeyFormData);

        // TODO: Send the request and email address to the server, and depending on the response return a message.

        return null;
    }

    /**
     * Send a confirmation email to the user.
     *
     * @param FeatureRequest $featureRequest
     * @param string         $hash
     * @return bool True if the email is sent. Otherwise, false.
     * @since 1.9.0
     */
    private function sendConfirmationEmail(FeatureRequest $featureRequest, $hash) {
        // Create the confirmation URL
        $confirmationUrl = $this->createConfirmationUrl($hash);
        if (!$confirmationUrl) {
            $this->redirectBack(false, _wpcc('Confirmation URL could not be created.'));
        }

        // Create the email body using the confirmation URL
        $emailBody = Utils::view('emails.feature-request-confirmation')
            ->with('url', $confirmationUrl)
            ->render();

        // Create the email's subject
        $emailSubject = _wpcc('Feature Request Confirmation') . ' - ' . _wpcc('WP Content Crawler');

        // We will send HTML.
        add_filter('wp_mail_content_type', function() {
            return 'text/html';
        });

        // TODO: Make sure the email sending operation works
        return wp_mail($featureRequest->getEmail(), $emailSubject, $emailBody);
    }

    /**
     * Create a hash
     *
     * @param FeatureRequest $featureRequest
     * @return string Confirmation hash
     * @since 1.9.0
     */
    private function createConfirmationHash(FeatureRequest $featureRequest) {
        return sha1($featureRequest->getEmail() . $featureRequest->getRequestContent() . time());
    }

    /**
     * Create full URL that can be used to validate the hash.
     *
     * @param string $hash Confirmation hash
     * @return null|string Full URL that can be used to validate the hash. This is intended to be used to confirm the
     *                     user's email. If there is no hash, returns null.
     * @since 1.9.0
     */
    private function createConfirmationUrl($hash) {
        if (!$hash) return null;

        return $this->getFullPageUrl([
            $this->confirmationHashParamKey => $hash
        ]);
    }

    /**
     * Save the confirmation hash to the database
     *
     * @param string $hash
     * @return bool True on success, false on failure.
     * @since 1.9.0
     */
    private function saveConfirmationHash($hash) {
        // Encode it to at least prevent directly copying and pasting from the database.
        return update_option($this->optionKeyConfirmationHash, base64_encode($hash), false);
    }

    /**
     * Get confirmation hash from the database
     *
     * @return null|string
     * @since 1.9.0
     */
    private function getConfirmationHashFromDb() {
        $value = get_option($this->optionKeyConfirmationHash, null);
        if (!$value) return null;

        // Decode it
        $value = base64_decode($value);
        return $value ?: null;
    }

    /**
     * Save the confirmation hash to the database
     *
     * @param FeatureRequest $featureRequest
     * @return bool True on success, false on failure.
     * @since 1.9.0
     */
    private function saveFeatureRequestData(FeatureRequest $featureRequest) {
        return update_option($this->optionKeyFormData, serialize($featureRequest), false);
    }

    /**
     * Get the latest feature request data from the database.
     *
     * @return FeatureRequest|null A FeatureRequest instance or null if the data does not exist in the database.
     * @since 1.9.0
     */
    private function getFeatureRequestDataFromDb() {
        $value = get_option($this->optionKeyFormData, null);
        return $value && is_serialized($value) ? unserialize($value) : null;
    }

    /**
     * @return string A string that says the key is not valid.
     * @since 1.9.0
     */
    private function getKeyNotValidMessage() {
        return _wpcc('The key is not valid.');
    }
}