<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 07/06/16
 * Time: 15:13
 */

namespace WPCCrawler\Objects\Page;
use WPCCrawler\Environment;
use WPCCrawler\Objects\Enums\PageSlug;
use WPCCrawler\Objects\Settings\SettingService;
use WPCCrawler\Test\Test;
use WPCCrawler\Utils;

/**
 * An abstract class for handling page creation routines, such as handling POST and AJAX requests.
 * @package WPTSPostTools\objects
 */
abstract class AbstractPageHandler {

    public function __construct() {
        // Create pageActionKey JS variable, which can be used when making AJAX requests as action variable
        add_action('admin_print_scripts', function() {
            // Print the script only if we are on this page.
            if(isset($_GET["page"]) && $_GET["page"] == $this->getFullPageName()) {
                $tooltipDisabled = SettingService::isTooltipDisabled() ? 'true' : 'false';
                echo "
                    <script type='text/javascript'>
                        var pageActionKey = '" . $this->getPageActionKey() . "';
                        var wpccTooltipDisabled = {$tooltipDisabled};
                    </script>
                ";
            }
        });

        // Listen to POST requests
        add_action('admin_post_' . $this->getPageActionKey(), function() {
            // Verify nonce
            $nonce = Utils::getValueFromArray($_POST, Environment::nonceName(), false);
            if(!$nonce || !wp_verify_nonce($nonce, $this->getPageActionKey())) {
                wp_die("Nonce is invalid.");
            }

            $this->handlePOST();
        });

        // Listen to AJAX requests
        add_action('wp_ajax_' . $this->getPageActionKey(), function() {
            if(!check_admin_referer($this->getPageActionKey(), Environment::nonceName())) wp_die();

            $this->handleAJAX();

            wp_die();
        });
    }

    /**
     * Get the page action name. This will be used for catching AJAX and POST requests that are made for this page
     * @return string Page action name
     */
    public function getPageActionKey(): string {
        return Environment::appShortName() . "_" . $this->getPageSlug();
    }

    /**
     * Get page slug with the app domain.
     * @return string|null
     */
    public function getFullPageName(): ?string {
        return PageSlug::getFullPageName($this->getPageSlug());
    }

    /**
     * Get full page URL for this page. You can also set additional URL parameters.
     *
     * @param array $args URL parameters as key,value pairs
     * @return string Prepared full URL
     */
    public function getFullPageUrl($args = []) {
        $args = array_merge([
            'page'  =>  $this->getFullPageName(),
        ], $args);
        return untrailingslashit(get_site_url()) . $this->getBaseUrl() . "&" . http_build_query($args);
    }

    /**
     * @return string Slug for the page
     */
    public abstract function getPageSlug(): string;

    /**
     * Handle POST requests
     * @return void
     */
    public function handlePOST(): void {

    }

    /**
     * Handle the AJAX requests. The AJAX data can be retrieved via {@link getAjaxData()}.
     *
     * @return void
     */
    public function handleAJAX(): void {
        // Do nothing. The child class must implement this if it wants to handle the AJAX requests.
    }

    /**
     * @return array The value of "data" key in {@link $_POST} array. If it does not exist, returns an empty array.
     * @since 1.11.1
     */
    protected function getAjaxData(): array {
        if(!isset($_POST["data"])) {
            wp_die(_wpcc("Data does not exist in your request. The request should include 'data'"));
        }

        // We'll return JSON response.
        header('Content-Type: application/json');

        $result = $_POST["data"] ?? null;
        return is_array($result) ? $result : [];
    }

    /**
     * Respond to AJAX requests. This method handles common AJAX requests that can be made via settings pages.
     *
     * @param array $data The data sent via AJAX request
     * @return bool True if the request is processed, false otherwise.
     */
    protected function respondToAJAX($data) {
        // Handle if this is a testing request
        $result = Test::respondToTestRequest($data);
        if($result !== null) {
            echo $result;
            return true;
        }

        // Handle if this is a request sent from a settings page (i.e. general settings and site settings)
        $result = SettingService::respondToAjaxRequest($data) ?? SettingService::respondToAjaxCmdRequest($data);
        if($result !== null) {
            echo $result;
            return true;
        }

        return false;
    }

    /*
     *
     */

    /**
     * Get base URL for the menu page item. This can be used to add a sub menu item under the parent menu item.
     *
     * @return string Parent page URL relative to the WordPress index page
     */
    public function getBaseUrl() {
        return '/wp-admin/edit.php?post_type=' . Environment::postType();
    }

    /**
     * @param bool $success         Whether the operation is succeeded or not
     * @param string $message       The message to be displayed to the user
     * @param array $queryParams    Additional query parameters that are appended to the redirect URL
     */
    public function redirectBack($success = true, $message = '', $queryParams = []): void {
        $params = [];
        $params['success'] = $success ? 'true' : 'false';

        if($message) $params['message'] = urlencode($message);

//        $redirectParams = 'success=' . ($success ? 'true' : 'false') . ($message ? '&message=' . urlencode($message) : '');
//        wp_redirect(admin_url(sprintf('edit.php?post_type=%1$s&page=%1$s_general_settings&' . $redirectParams, Environment::postType())));

        wp_redirect($this->getFullPageUrl(array_unique(array_merge($params, $queryParams))));
        exit;
    }
}