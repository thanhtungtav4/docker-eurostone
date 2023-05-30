<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/10/2019
 * Time: 20:57
 *
 * @since 1.9.0
 */

namespace WPCCrawler;

/**
 * Contains methods that are used to validate if requirements of the plugin are satisfied
 *
 * @since 1.9.0
 */
class RequirementValidator {

    /** @var RequirementValidator */
    private static $instance = null;

    // Names of the required PHP extensions
    private const EXT_NAME_MBSTRING = 'mbstring';
    private const EXT_NAME_CURL     = 'curl';
    private const EXT_NAME_JSON     = 'json';
    private const EXT_NAME_DOM      = 'dom';

    /**
     * Get the instance
     *
     * @return RequirementValidator
     * @since 1.9.0
     */
    public static function getInstance(): RequirementValidator {
        if (static::$instance === null) {
            static::$instance = new RequirementValidator();
        }

        return static::$instance;
    }

    /**
     * This is a singleton
     *
     * @since 1.9.0
     */
    public function __construct() { }

    /**
     * Validates all requirements of the plugin and shows messages if requirements are not satisfied. The validation
     * is only performed in the plugin's pages. So, if the current page belongs to the plugin, the validation will
     * be performed. Otherwise, it will not be performed.
     *
     * @since 1.9.0
     */
    public function validateAll(): void {
        if (!Utils::isPluginPage()) return;

        $messages = [];

        // Validate PHP version
        if (!$this->isPhpVersionValid()) {
            $messages[] = $this->getPhpVersionRequirementMessage();
        }

        // Validate that all required extensions are enabled
        $notEnabledExtensions = array_values(array_filter($this->getRequiredPhpExtensions(), function($extensionName) {
            return !$this->isExtensionEnabled($extensionName);
        }));

        if ($notEnabledExtensions) {
            $messages[] = $this->getExtensionRequirementMessage($notEnabledExtensions);
        }

        // If there is no message, stop.
        if (!$messages) return;

        $this->showAdminNotice(implode("<br>", $messages));
    }

    /**
     * @return bool True if the current PHP version satisfies the plugin's PHP version requirement
     * @since 1.9.0
     */
    public function isPhpVersionValid(): bool {
        $version = phpversion();
        
        // If there is no version information, we cannot check the version. Let's assume the version is valid. 
        if ($version === false) {
            return true;
        }
        
        return !version_compare($version, Environment::requiredPhpVersion(), "<");
    }

    /**
     * @return string[] Names of the required PHP extensions
     * @since 1.9.0
     */
    public function getRequiredPhpExtensions(): array {
        return [
            static::EXT_NAME_MBSTRING,
            static::EXT_NAME_CURL,
            static::EXT_NAME_JSON,
            static::EXT_NAME_DOM,
        ];
    }

    /**
     * @param string $extension Name of a PHP extension that should be checked if it is enabled
     * @return bool True if the extension is currently enabled. Otherwise, false.
     * @since 1.9.0
     */
    public function isExtensionEnabled($extension): bool {
        return extension_loaded($extension);
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Get a message telling that the given extensions are required and must be enabled
     *
     * @param string|string[] $extensionNames Names of the PHP extension that should be enabled
     * @return string The message
     * @since 1.9.0
     */
    private function getExtensionRequirementMessage($extensionNames): string {
        if (!$extensionNames) return '';

        // Make it an array if it is not an array
        if(!is_array($extensionNames)) $extensionNames = [$extensionNames];

        if (sizeof($extensionNames) === 1) {
            $format = _wpcc('WP Content Crawler requires <b>%1$s</b> extension of PHP to be enabled. Please enable it.');

        } else {
            $format = _wpcc('WP Content Crawler requires <b>%1$s</b> extensions of PHP to be enabled. Please enable them.');
        }

        return sprintf($format, implode(', ', $extensionNames));
    }

    /**
     * Get a message telling that the current PHP version does not satisfy the requirements
     *
     * @return string
     * @since 1.9.0
     */
    private function getPhpVersionRequirementMessage(): string {
        $format = _wpcc('WP Content Crawler requires at least PHP <b>%1$s</b>. Your current PHP version is <b>%2$s</b>.');
        return sprintf($format, Environment::requiredPhpVersion(), phpversion());
    }

    /**
     * Show the given message as a notice in the admin panel
     *
     * @param string $message
     * @since 1.9.0
     */
    private function showAdminNotice($message): void {
        if (!$message) return;

        add_action("admin_notices", function() use (&$message) {
            ?>
            <div class="update-nag">
                <p><?php echo $message; ?></p>
            </div>
            <?php
        });
    }
}