<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 07/06/16
 * Time: 14:19
 */

namespace WPCCrawler\Objects\Page;
use Illuminate\Contracts\View\View;
use WPCCrawler\Environment;
use WPCCrawler\Factory;
use WPCCrawler\Permission;

/**
 * A parent class that can be used to avoid routines when creating a submenu page for the plugin
 * @package WPTSPostTools\objects
 */
abstract class AbstractMenuPage extends AbstractPageHandler {

    /** @var string|false */
    private $pageHookSuffix = false;

    /**
     * @param bool $menuPage    If true, the menu item will be a parent menu item.
     * @param bool $doNotAdd    If true, menu page won't be added. This can be used to show/hide a menu page depending
     *                          on settings.
     */
    public function __construct($menuPage = false, $doNotAdd = false) {
        $wptslm = Factory::wptslmClient();

        if(!$wptslm->isUserCool() || $doNotAdd) return;

        // Create the menu page
        add_action('admin_menu', function() use ($menuPage) {
            $menuNameColor = $this->getMenuNameColor();

            // Prepare common parameters for add_menu_page and add_submenu_page. add_submenu_page has only 1 more parameter.
            $functionParams = [
                $this->getMenuTitle(),
                $menuNameColor ? "<span style='color: {$menuNameColor}'>" . $this->getMenuTitle() . "</span>" : $this->getMenuTitle(),
                Environment::allowedUserCapability(),
                $this->getFullPageName(),
                function () {
                    if (!$this->isAllowed()) Permission::displayNotAllowedMessageAndExit();

                    $view = $this->getView();
                    // Add a page action key that can be used when creating nonces and a hidden action for AJAX requests
                    $view->with([
                        'pageActionKey' => $this->getPageActionKey(),
                        'pageTitle'     => $this->getPageTitle(),
                    ]);
                    echo $view->render();
                }
            ];

            if(!$menuPage) {
                // We want this menu item to be a submenu page. Add the missing parameter and call the function.
                $this->pageHookSuffix = call_user_func_array('add_submenu_page', array_merge(
                    ['edit.php?post_type=' . Environment::postType()],
                    $functionParams
                ));
            } else {
                // We want this menu item to be a parent menu item.
                $this->pageHookSuffix = call_user_func_array('add_menu_page', $functionParams);
            }
        });

        // Construct the parent to handle POST and AJAX requests.
        parent::__construct();
    }

    /**
     * @return string Menu title for the page
     */
    public abstract function getMenuTitle(): string;

    /**
     * @return string Page title
     */
    public abstract function getPageTitle(): string;

    /**
     * Get view for the page.
     * @return View Not-rendered blade view for the page
     */
    public abstract function getView();

    /**
     * Get hook suffix for the page. This can be used for actions such as <b>load-$hook</b>.
     *
     * @return string|false Page hook suffix or false.
     */
    public function getPageHookSuffix() {
        return $this->pageHookSuffix;
    }

    /**
     * Override this method to change menu name color.
     *
     * @return string|null Color of menu name. E.g. #ff4400
     */
    protected function getMenuNameColor(): ?string {
        return null;
    }

    /**
     * @return bool True if the page is allowed for the current user. Otherwise, false.
     * @since 1.9.0
     */
    protected function isAllowed(): bool {
        return true;
    }
}