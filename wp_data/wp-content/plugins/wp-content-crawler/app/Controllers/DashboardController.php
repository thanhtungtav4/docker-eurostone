<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/02/17
 * Time: 14:33
 */

namespace WPCCrawler\Controllers;


use Illuminate\Contracts\View\View;
use WPCCrawler\Factory;
use WPCCrawler\Objects\Dashboard\Dashboard;
use WPCCrawler\Objects\Enums\PageSlug;
use WPCCrawler\Objects\Page\AbstractMenuPage;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Permission;
use WPCCrawler\Utils;

class DashboardController extends AbstractMenuPage {

    /**
     * @var array Structured as ['option name' => 'default value']
     */
    public $settings = [
        SettingKey::WPCC_DASHBOARD_COUNT_LAST_RECRAWLED_POSTS => 10,       // int  Number of items that should be shown in last recrawled posts table
        SettingKey::WPCC_DASHBOARD_COUNT_LAST_CRAWLED_POSTS   => 10,       // int  Number of items that should be shown in last crawled posts table
        SettingKey::WPCC_DASHBOARD_COUNT_LAST_URLS            => 10,       // int  Number of items that should be shown in last added URLs table
        SettingKey::WPCC_DASHBOARD_COUNT_LAST_DELETED_URLS    => 10,       // int  Number of items that should be shown in last deleted URLs table
    ];

    /**
     * @return string Menu title for the page
     */
    public function getMenuTitle(): string {
        return _wpcc("Dashboard");
    }

    /**
     * @return string Page title
     */
    public function getPageTitle(): string {
        return _wpcc("Dashboard");
    }

    /**
     * @return string Slug for the page
     */
    public function getPageSlug(): string {
        return PageSlug::DASHBOARD;
    }

    /**
     * @return bool
     * @since 1.9.0
     */
    protected function isAllowed(): bool {
        return Permission::canViewDashboard();
    }

    /**
     * Get view for the page.
     *
     * @return View Not-rendered blade view for the page
     */
    public function getView() {
        // Add assets
        Factory::assetManager()->addGuides();
        Factory::assetManager()->addPostSettings();
        Factory::assetManager()->addBootstrapGrid();
        Factory::assetManager()->addDashboard();

        // Create a new Dashboard so that we can get the statistics from the view.
        $dashboard = new Dashboard();

        // Prepare the settings
        $settings = [];
        foreach($this->settings as $key => $default) $settings[$key] = get_option($key, $default);

        // This is important to appropriately assign the values of the already-saved options to the form elements.
        $isOption = true;

        // Attach page action key to the view, as well. This is important because this method is utilized to send
        // AJAX responses. Normally, all menu pages include this variable by default. However, they should be loaded
        // completely. When this is utilized for AJAX responses, $pageActionKey is not included automatically. So,
        // we need to do it here manually.
        $pageActionKey = $this->getPageActionKey();

        return Utils::view('dashboard/main')
            ->with(compact('dashboard', 'settings', 'isOption', 'pageActionKey'));
    }

    public function handlePOST(): void {
        parent::handlePOST();

        $data = $_POST;
        $keys = array_keys($this->settings);

        // Save options
        foreach($data as $key => $value) {
            if(in_array($key, $keys)) {
                update_option($key, $value, false);

                // Remove the key, since it is saved.
                unset($keys[array_search($key, $keys)]);
            }
        }

        // Redirect back
        $this->redirectBack(true);
    }

    public function handleAJAX(): void {
        parent::handleAJAX();
        $data = $this->getAjaxData();

        $cmd = Utils::array_get($data, "cmd");
        if(!$cmd) return;

        switch($cmd) {
            case "refresh_dashboard":
                echo json_encode([
                    'view' => $this->getView()->render()
                ]);

                break;

            case "refresh_section":
                $value = (int) Utils::array_get($data, "value");
                $optionKey = Utils::array_get($data, "optionKey");

                if($optionKey && array_key_exists($optionKey, $this->settings) && $value > 0) {
                    update_option($optionKey, $value, false);
                }

                echo json_encode([
                    'view' => $this->getView()->render()
                ]);

                break;
        }
    }

}
