<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/01/2019
 * Time: 11:08
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects;


use Exception;

/**
 * @since 1.9.0
 */
class FeatureRequest {

    /** @var string Email of the requester. */
    private $email;

    /** @var string The feature request. */
    private $requestContent;

    /** @var int Minimum number of characters that can be entered to request content input. */
    const MIN_REQUEST_CONTENT_LENGTH = 20;

    /**
     * @param string $email          See {@link $email}
     * @param string $requestContent See {@link $$requestContent}
     * @since 1.9.0
     * @throws Exception If there is no email or feature request content. Also, if the feature request content's length
     *                    is less than {@link $minRequestContentLength}.
     */
    public function __construct($email, $requestContent) {
        if (!$email || !trim($email) || !$requestContent || !trim($requestContent)) {
            throw new Exception(_wpcc('Both the email and the feature request must be supplied'));
        }

        if (mb_strlen(trim($requestContent)) < FeatureRequest::MIN_REQUEST_CONTENT_LENGTH) {
            throw new Exception(sprintf(_wpcc('The minimum length of the request content is %1$s.'), FeatureRequest::MIN_REQUEST_CONTENT_LENGTH));
        }

        $this->email = trim($email);
        $this->requestContent = trim($requestContent);
    }

    /**
     * @return string
     * @since 1.9.0
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return string
     * @since 1.9.0
     */
    public function getRequestContent() {
        return $this->requestContent;
    }

}