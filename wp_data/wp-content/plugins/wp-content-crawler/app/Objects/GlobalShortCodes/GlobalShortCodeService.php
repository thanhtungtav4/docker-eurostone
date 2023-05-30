<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/12/2018
 * Time: 07:53
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\GlobalShortCodes;


use WPCCrawler\Objects\GlobalShortCodes\ShortCodes\Base\BaseGlobalShortCode;
use WPCCrawler\Objects\GlobalShortCodes\ShortCodes\IFrameGlobalShortCode;
use WPCCrawler\Objects\GlobalShortCodes\ShortCodes\ScriptGlobalShortCode;

class GlobalShortCodeService {

    /** @var GlobalShortCodeService */
    private static $instance = null;

    /** @var string[] */
    private $registeredClasses = [];

    /** @var BaseGlobalShortCode[] */
    private $shortCodeInstances = [];

    /**
     * Get the instance
     *
     * @return GlobalShortCodeService
     * @since 1.8.0
     */
    public static function getInstance() {
        if (static::$instance === null) static::$instance = new GlobalShortCodeService();
        return static::$instance;
    }

    /*
     *
     */

    /**
     * Registers all short codes. This is a singleton.
     */
    private function __construct() {
        // Define the global short code classes so that they can be registered.
        $this->registeredClasses = [
            IFrameGlobalShortCode::class,
            ScriptGlobalShortCode::class,
        ];

        // Register the short codes
        foreach($this->registeredClasses as $clz) {
            $this->getShortCodeInstance($clz)->register();
        }
    }

    /**
     * Get the instance of a short code class.
     *
     * @param string $className
     * @return BaseGlobalShortCode
     * @since 1.8.0
     */
    public function getShortCodeInstance($className) {
        if (!isset($this->shortCodeInstances[$className])) {
            $this->shortCodeInstances[$className] = new $className();
        }

        return $this->shortCodeInstances[$className];
    }

    /**
     * Get short code's tag name.
     *
     * @param string $globalShortCodeClass Name of a class that extends {@link BaseGlobalShortCode}.
     * @return string Tag name of the given short code class.
     * @since 1.8.0
     */
    public static function getShortCodeTagName($globalShortCodeClass) {
        return static::getInstance()->getShortCodeInstance($globalShortCodeClass)->getTagName();
    }

}