<?php

namespace WPCCrawler\Objects\Transformation\Translation\Clients\AwsTranslate\Credentials;

/**
 * Provides access to the AWS credentials used for accessing AWS services: AWS
 * access key ID, secret access key, and security token. These credentials are
 * used to securely sign requests to AWS services.
 *
 * This class is retrieved from https://github.com/aws/aws-sdk-php/
 */
interface CredentialsInterface {
    /**
     * Returns the AWS access key ID for this credentials object.
     *
     * @return string
     */
    public function getAccessKeyId();

    /**
     * Returns the AWS secret access key for this credentials object.
     *
     * @return string
     */
    public function getSecretKey();

    /**
     * Get the associated security token if available
     *
     * @return string|null
     */
    public function getSecurityToken();

    /**
     * Get the UNIX timestamp in which the credentials will expire
     *
     * @return int|null
     */
    public function getExpiration();

    /**
     * Check if the credentials are expired
     *
     * @return bool
     */
    public function isExpired();

    /**
     * Converts the credentials to an associative array.
     *
     * @return array
     */
    public function toArray();
}
