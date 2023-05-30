<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 17/11/2020
 * Time: 11:40
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base;


use DateTime;
use Illuminate\Support\Str;
use WPCCrawler\Environment;
use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Crawling\Interfaces\MakesCrawlRequest;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Enums\CommandShortCodeName;
use WPCCrawler\Objects\Filtering\Commands\Objects\NotificationInfo;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Filtering\Interfaces\NeedsBot;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\SettingService;
use WPCCrawler\Objects\Traits\ShortCodeReplacer;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\MaxLengthInputWithLabel;
use WPCCrawler\Objects\Views\NumericInputWithLabel;
use WPCCrawler\Objects\Views\ShortCodeButtonsWithLabelForEmailCmd;
use WPCCrawler\Objects\Views\TextAreaWithLabel;
use WPCCrawler\Utils;
use WPCCrawler\WPCCrawler;

abstract class AbstractSendNotificationCommand extends AbstractActionCommand implements NeedsBot {

    use ShortCodeReplacer;

    /** @var int Default notification interval in minutes. */
    const DEFAULT_NOTIFICATION_INTERVAL = 30;

    /** @var int Maximum length of the identifier that can be entered by the user */
    const IDENTIFIER_OPTION_MAX_LENGTH = 100;

    /** @var AbstractBot|null */
    private $bot = null;

    public function getInputDataTypes(): array {
        return [ValueType::T_NOTIFICATION];
    }

    protected function isOutputTypeSameAsInputType(): bool {
        return true;
    }

    public function doesNeedSubjectValue(): bool {
        return false;
    }

    protected function isTestable(): bool {
        return false;
    }

    protected function createViews(): ?ViewDefinitionList {
        $infoUseShortCodes = _wpcc('You can use the short codes shown at the top of the command container to'
            . ' include certain information into the template.');

        $infoEmptyTemplate = _wpcc('If you leave this empty, a default template will be used. However, the '
            . 'default template will not include specific information that you can use to differentiate'
            . ' notifications. Hence, it is recommended that you enter a descriptive value.');

        return (new ViewDefinitionList())
            ->add((new ViewDefinition(ShortCodeButtonsWithLabelForEmailCmd::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Short codes'))
                ->setVariable(ViewVariableName::INFO,  _wpcc("Short codes that can be used in the templates of the
                    notification, such as title and message templates. You can hover over the short codes to see what
                    they do. You can click to the short code buttons to copy the short codes. Then, you can paste the
                    short codes into the templates to include them. They will be replaced with the actual information 
                    before sending the notification."))
            )

            ->add((new ViewDefinition(TextAreaWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Title'))
                ->setVariable(ViewVariableName::INFO,  _wpcc("Title of the notification.")
                    . " $infoUseShortCodes $infoEmptyTemplate")
                ->setVariable(ViewVariableName::NAME,  InputName::NOTIFICATION_TITLE_TEMPLATE)
                ->setVariable(ViewVariableName::ROWS,  2)
            )

            ->add((new ViewDefinition(TextAreaWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Message'))
                ->setVariable(ViewVariableName::INFO,  _wpcc("Enter the message that will be sent as a notification.")
                    . " $infoUseShortCodes $infoEmptyTemplate")
                ->setVariable(ViewVariableName::NAME,  InputName::NOTIFICATION_MESSAGE_TEMPLATE)
                ->setVariable(ViewVariableName::ROWS,  10)
            )

            ->add((new ViewDefinition(MaxLengthInputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Notification identifier'))
                ->setVariable(ViewVariableName::INFO, _wpcc('A text that will be used to identify this 
                    notification to limit the number of same-type notifications to be sent. Getting numerous 
                    notifications about the same thing will probably bother you. To prevent this, just enter a text so
                    that the plugin can reduce the number of notifications. If you leave this empty, the notifications
                    will not be limited. You can use the same identifier for other notification commands as well. For
                    example, you can enter "notification-about-low-price". When a notification is sent, the date of the
                    notification will be stored for this identifier. The date will be checked before sending another 
                    notification.'))
                ->setVariable(ViewVariableName::NAME,      InputName::NOTIFICATION_ID)
                ->setVariable(ViewVariableName::TYPE,      'text')
                ->setVariable(ViewVariableName::MAXLENGTH, AbstractSendNotificationCommand::IDENTIFIER_OPTION_MAX_LENGTH)
            )

            ->add((new ViewDefinition(NumericInputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Notification interval (in minutes)'))
                ->setVariable(ViewVariableName::INFO,  sprintf(
                    _wpcc('This defines the minimum duration between two notifications, in minutes. For 
                        example, if you enter %1$d, you will not get more than one notification within %1$d minutes. For
                        this to work, you have to define the notification identifier as well. If you leave this empty,
                        the default value will be used, which is %2$d.'),
                    30,
                    AbstractSendNotificationCommand::DEFAULT_NOTIFICATION_INTERVAL
                ))
                ->setVariable(ViewVariableName::NAME,  InputName::NOTIFICATION_INTERVAL)
                ->setVariable(ViewVariableName::STEP,  '1')
            );
    }

    protected function onExecute($key, $subjectValue) {
        // Check if the notification can be sent. If not, stop.
        if (!$this->onBeforeSendNotification()) return;

        // Create the info
        $info = $this->createNotificationInfo();

        // Let the child send the notification by using the info
        if ($this->onSendNotification($info)) {
            // If the child sent the notification, update the last notification date.
            $this->saveLastNotificationDateAsNow();
        }

        // If there is a logger, add the notification's title and content as the message so that they can be observed.
        $logger = $this->getLogger();
        if ($logger) {
            if ($this->shouldPrefixTemplatesForTest()) $logger
                ->addMessage(_wpcc('The notification templates are prefixed to indicate that the notification is 
                    sent for testing purposes. The prefix will not be added when not testing.'));
            $logger
                ->addMessage(_wpcc("Title")   . ": " . $info->getTitle())
                ->addMessage(_wpcc("Message") . ": " . $info->getMessage());
        }
    }

    /*
     *
     */

    /**
     * @return bool True if the notification can be sent. Otherwise, false.
     * @since 1.11.0
     */
    protected function onBeforeSendNotification(): bool {
        // If this is a unit test, do not send a notification.
        if (WPCCrawler::isDoingUnitTest()) return false;

        if (!SettingService::isNotificationActive()) {
            $message = _wpcc('Notification could not be sent, because the notifications are not active in the 
                general settings.');
            $this->addLogMessage($message, true, true);
            return false;
        }

        // If there is no identifier, we cannot limit the number of notifications. So, return true to indicate that it
        // is OK to send the notification.
        if (!$this->isIdentifierDefined()) {
            $message = _wpcc('Number of notifications is not limited, because an identifier is not defined.');
            $this->addLogMessage($message, true, false);
            return true;
        }

        // Make sure the defined interval is a numeric value.
        $intervalInMinutes = $this->getIntervalOption();
        if ($intervalInMinutes === null) {
            $message = _wpcc('The notification interval value is not numeric or it is negative. The value must be 
                a positive number. The notification is not sent.');
            $this->addLogMessage($message, true, true);

            return false;
        }

        // Get the last notification date. If it does not exist, we can send a notification.
        $lastNotificationDate = $this->getLastNotificationDate();
        if (!$lastNotificationDate) return true;

        // The last notification date exists. Now, check if there is at least "interval" amount of time between now and
        // the last notification date.
        $nowTimestamp = $this->getCurrentTimestamp();
        if (!$nowTimestamp) {
            $message = _wpcc('The current time could not be retrieved. The duration of time between the last 
                notification and now could not be checked. The notification will be sent anyway.');
            $this->addLogMessage($message, true, true);
            return true;
        }

        // Timestamp is in seconds. If the duration between now and the last notification time is greater than the
        // defined limit, return true. Otherwise, return false.
        $intervalSeconds     = $intervalInMinutes * 60;
        $passedTimeInSeconds = $nowTimestamp - $lastNotificationDate->getTimestamp();
        $shouldSend          = $passedTimeInSeconds > $intervalSeconds;

        if (!$shouldSend) {
            $passedTimeInMinutes = (int) ($passedTimeInSeconds / 60);
            $message = sprintf(
                _wpcc('Notification is not sent, because the last notification was sent %1$d minute(s) ago for 
                    %2$s identifier. The limit between two notifications is %3$d minute(s).'),
                $passedTimeInMinutes,
                "'{$this->getIdentifierOption()}'",
                $intervalInMinutes
            );

            $this->addLogMessage($message, true, false);
        }

        return $shouldSend;
    }

    /**
     * Send the notification
     *
     * @param NotificationInfo $info Contains the details of the notification such as the title and the message
     * @return bool True if the notification is sent. Otherwise, false.
     * @since 1.11.0
     */
    abstract protected function onSendNotification(NotificationInfo $info): bool;

    /*
     * PROTECTED HELPERS
     */

    /**
     * Update this notification's "last sent" date as now.
     *
     * @return bool True if the date is saved. Otherwise, false.
     * @since 1.11.0
     */
    protected function saveLastNotificationDateAsNow(): bool {
        // Get the site ID so that we can save the post meta's value for the current site
        $siteId = $this->getSiteId();
        if (!$siteId) return false;

        // Get the post meta key that will store the last notification date
        $postMetaKey = $this->getNotificationIdentifierMetaKey();
        if (!$postMetaKey) return false;

        // Update the date of the last notification as now
        $now = current_time(Environment::mysqlDateFormat());
        return update_post_meta($siteId, $postMetaKey, $now) !== false;
    }

    /**
     * @return DateTime|null The date of the last notification sent for the defined notification identifier
     * @since 1.11.0
     */
    protected function getLastNotificationDate(): ?DateTime {
        // Get the site ID so that we can retrieve the post meta's value
        $siteId = $this->getSiteId();
        if (!$siteId) return null;

        // Get the post meta key
        $postMetaKey = $this->getNotificationIdentifierMetaKey();
        if (!$postMetaKey) return null;

        // Get its value
        $lastDateString = get_post_meta($siteId, $postMetaKey, true);
        if (!is_scalar($lastDateString) || $lastDateString === '') return null;

        // The date must have been stored in MySQL date format. Try to create a DateTime for the stored date.
        $date = DateTime::createFromFormat(Environment::mysqlDateFormat(), (string) $lastDateString);
        return $date ?: null;
    }

    /**
     * Get the post meta key that is used store this notification's "last sent" date.
     *
     * @return string An identifier that is at most 255 characters. This can be used as a post meta key.
     * @since 1.11.0
     */
    protected function getNotificationIdentifierMetaKey(): ?string {
        $definedId = $this->getIdentifierOption();
        if (!$definedId) return null;

        $baseIdentifier = Str::limit($definedId, static::IDENTIFIER_OPTION_MAX_LENGTH, '');
        return "last_notif_cmd_$baseIdentifier";
    }

    /**
     * Create a {@link NotificationInfo} that contains the title and the message of the notification. This method
     * prepares the templates by replacing their short codes before creating the {@link NotificationInfo}. Hence, the
     * information in the {@link NotificationInfo} is ready to use, unless the child class defines additional short
     * codes. If the child defines additional short codes, it must replace them.
     *
     * @return NotificationInfo
     * @since 1.11.0
     */
    protected function createNotificationInfo(): NotificationInfo {
        $titleTemplate   = $this->getStringOption(InputName::NOTIFICATION_TITLE_TEMPLATE);
        $messageTemplate = $this->getStringOption(InputName::NOTIFICATION_MESSAGE_TEMPLATE);

        // If the templates do not exist, use their defaults.
        if ($titleTemplate   === null || $titleTemplate   === "") $titleTemplate   = $this->getDefaultTitleTemplate();
        if ($messageTemplate === null || $messageTemplate === "") $messageTemplate = $this->getDefaultMessageTemplate();

        // Substitute the values of the short codes and create a NotificationInfo so that the children can send the
        // notification by retrieving the title and the message from the NotificationInfo.
        $shortCodeMap = $this->getShortCodeReplacementMap();

        // If this is a test, prefix the title and the message with a value that indicates that the notification is
        // sent for testing purposes.
        $prefix = $this->shouldPrefixTemplatesForTest() ? '(' . _wpcc('Test') . ') ' : '';
        return new NotificationInfo(
            $prefix . $this->replaceShortCodesSingle($shortCodeMap, $titleTemplate),
            $prefix . $this->replaceShortCodesSingle($shortCodeMap, $messageTemplate)
        );
    }

    /**
     * @return array A key-value pair that can be provided to {@link ShortCodeReplacer::replaceShortCodes()} as the map
     *               to replace all available short codes inside a text.
     * @since 1.11.0
     */
    protected function getShortCodeReplacementMap(): array {
        $bot = $this->getBot();
        return [
            CommandShortCodeName::REQUEST_URL => function() {
                $bot = $this->getMakesCrawlRequest();
                return $bot ? $bot->getCrawlingUrl() : "";
            },

            CommandShortCodeName::STATUS_CODE => function() {
                $bot = $this->getMakesCrawlRequest();
                return $bot ? $bot->getResponseHttpStatusCode() : "";
            },

            CommandShortCodeName::SITE_NAME => function() {
                $bot = $this->getBot();
                if (!$bot) return "";

                $site = $bot->getSite();
                return $site && $site->post_title !== '' ? $site->post_title : _wpcc('No title');
            },

            CommandShortCodeName::SITE_EDIT_URL => function() {
                $bot = $this->getBot();
                $siteId = $bot ? $bot->getSiteId() : null;
                if (!$siteId) return "#";

                return trim(admin_url(), '/') . '/post.php?post=' . $siteId . '&action=edit';
            },

            CommandShortCodeName::CURRENT_TIME => function () {
                return current_time(Environment::mysqlDateFormat());
            },

            CommandShortCodeName::SITE_ID => $bot ? $bot->getSiteId() : "",
        ];
    }

    /**
     * @return string A text that can be used as the notification title in case that a title is not defined. This text
     *                contains short codes that can be inside a notification title.
     * @since 1.11.0
     */
    protected function getDefaultTitleTemplate(): string {
        $siteName = '[' . CommandShortCodeName::SITE_NAME . ']';
        return sprintf(_wpcc('Notification for %1$s'), $siteName)
            . ' - '
            . _wpcc('WP Content Crawler');
    }

    /**
     * @return string A text that can be used as the notification message in case that a message is not defined. This
     *                text contains short codes that can be inside a notification message.
     * @since 1.11.0
     */
    protected function getDefaultMessageTemplate(): string {
        return Utils::view('emails.command-notification')->render();
    }

    /**
     * @return int|null ID of the site. If it cannot be retrieved, returns null.
     * @since 1.11.0
     */
    protected function getSiteId(): ?int {
        $bot = $this->getBot();
        if (!$bot) return null;

        return $bot->getSiteId();
    }

    /**
     * Add a log message
     *
     * @param string $message       The message
     * @param bool   $addToLogger   If true and a logger exists, the message will be added to the logger
     *                              ({@link getLogger()}).
     * @param bool   $addToInformer If true, the message will be added as an information to the {@link Informer}.
     * @since 1.11.0
     */
    protected function addLogMessage(string $message, bool $addToLogger, bool $addToInformer): void {
        // Add to the logger if requested
        if ($addToLogger) {
            $logger = $this->getLogger();
            if ($logger) $logger->addMessage($message);
        }

        // Add to the informer if requested
        if ($addToInformer) Informer::addInfo($message)->addAsLog();
    }

    /**
     * @return bool True if the notification templates should be prefixed to indicate that the notification is sent for
     *              testing purposes. Otherwise, false.
     * @since 1.11.0
     */
    protected function shouldPrefixTemplatesForTest(): bool {
        return $this->isVerbose();
    }

    /*
     * INTERFACE METHODS
     */

    public function setBot(?AbstractBot $bot): void {
        $this->bot = $bot;
    }

    public function getBot(): ?AbstractBot {
        return $this->bot;
    }

    /*
     * PUBLIC HELPERS
     */

    /**
     * @return MakesCrawlRequest|null If the {@link $bot} is a {@link MakesCrawlRequest}, returns the bot. Otherwise,
     *                                returns null.
     * @since 1.11.0
     */
    public function getMakesCrawlRequest(): ?MakesCrawlRequest {
        $bot = $this->getBot();
        return $bot instanceof MakesCrawlRequest ? $bot : null;
    }

    /**
     * @return bool True if an identifier is defined for this notification. Otherwise, false.
     * @since 1.11.0
     */
    public function isIdentifierDefined(): bool {
        return $this->getIdentifierOption() !== null;
    }

    /**
     * @return string|null The identifier defined by the user. If there is no identifier, returns null.
     * @since 1.11.0
     */
    public function getIdentifierOption(): ?string {
        $id = $this->getStringOption(InputName::NOTIFICATION_ID);
        return $id === null || $id === "" ? null : $id;
    }

    /**
     * @return int|null The notification interval defined by the user. If a non-numeric or negative value is defined,
     *                  returns null. If nothing is defined, returns {@link DEFAULT_NOTIFICATION_INTERVAL}. If a numeric
     *                  positive value is defined, returns it by parsing it to integer.
     * @since 1.11.0
     */
    public function getIntervalOption(): ?int {
        $interval = $this->getOption(InputName::NOTIFICATION_INTERVAL);
        if ($interval === "") $interval = static::DEFAULT_NOTIFICATION_INTERVAL;
        if (!is_numeric($interval)) return null;

        // Check if the interval is positive. If not, return null.
        $interval = (int) $interval;
        return $interval < 0 ? null : $interval;
    }

    /**
     * @return int
     * @since 1.11.0
     */
    protected function getCurrentTimestamp(): int {
        return (int) current_time('timestamp');
    }

}