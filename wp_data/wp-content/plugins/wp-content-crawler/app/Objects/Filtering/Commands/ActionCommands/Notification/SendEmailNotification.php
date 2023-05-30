<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 17/11/2020
 * Time: 11:39
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Notification;


use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractSendNotificationCommand;
use WPCCrawler\Objects\Filtering\Commands\Objects\NotificationInfo;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\SettingService;

class SendEmailNotification extends AbstractSendNotificationCommand {

    public function getKey(): string {
        return CommandKey::SEND_EMAIL_NOTIFICATION;
    }

    public function getName(): string {
        return _wpcc('Send email notification');
    }

    protected function onSendNotification(NotificationInfo $info): bool {
        $logger = $this->getLogger();

        // Get the email addresses
        $emailAddresses = $this->getNotificationEmails();
        if (!$emailAddresses) {
            if ($logger) $logger
                ->addMessage(_wpcc("No email notification is sent, because there are no email addresses defined."));
            return false;
        }

        // We will send HTML
        add_filter('wp_mail_content_type', function() {
            return 'text/html';
        });

        // Send the emails
        foreach($emailAddresses as $to) {
            // Make sure the email address is a string. If not, skip this one.
            if (!is_string($to)) continue;

            // Send the message. If it is sent, continue with the next email address.
            if ($this->sendEmail($to, $info)) continue;

            // The email could not be sent. Notify the user.
            $message = sprintf(_wpcc('Notification email could not be sent to %1$s. Please make sure that 
                WordPress is correctly configured to send emails.'), $to);

            Informer::addInfo($message)->addAsLog();
            if ($logger) $logger->addMessage($message);
        }

        return true;
    }

    /**
     * Send an email to an email address
     *
     * @param string           $to   Email address to which the email will be sent
     * @param NotificationInfo $info The information to be sent
     * @return bool True if the email is sent successfully. Otherwise, false.
     * @since 1.11.0
     */
    protected function sendEmail(string $to, NotificationInfo $info): bool {
        return wp_mail($to, $info->getTitle(), $info->getMessage());
    }

    /**
     * @return string[] Email addresses to which the notifications should be sent
     * @since 1.11.0
     */
    protected function getNotificationEmails(): array {
        return SettingService::getNotificationEmails();
    }
}