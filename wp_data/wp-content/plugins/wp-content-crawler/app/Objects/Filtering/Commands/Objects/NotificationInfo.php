<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 17/11/2020
 * Time: 12:14
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\Objects;


class NotificationInfo {

    /** @var string Title of the email/notification */
    private $title;

    /** @var string Content of the email/notification */
    private $message;

    /**
     * @param string $title   See {@link $title}
     * @param string $message See {@link $message}
     * @since 1.11.0
     */
    public function __construct(string $title, string $message) {
        $this->title   = $title;
        $this->message = $message;
    }

    /**
     * @return string See {@link $title}
     * @since 1.11.0
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @return string See {@link $message}
     * @since 1.11.0
     */
    public function getMessage(): string {
        return $this->message;
    }

}