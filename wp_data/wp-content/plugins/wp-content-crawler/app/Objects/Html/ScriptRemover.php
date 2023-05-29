<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 17/11/2019
 * Time: 07:28
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Html;

use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;

/**
 * Removes script elements and attributes
 * 
 * @since 1.8.1
 */
class ScriptRemover {

    /** @var Crawler Crawler storing the HTML code whose scripts will be removed */
    private $crawler;
    
    /** @var DummyBot */
    private $dummyBot;

    /**
     * @var string[] Names of attributes that are used to store and run JavaScript
     */
    private static $EVENT_ATTR_NAMES = [
        'onafterprint',
        'onbeforeprint',
        'onbeforeunload',
        'onerror',
        'onhashchange',
        'onload',
        'onmessage',
        'onoffline',
        'ononline',
        'onpagehide',
        'onpageshow',
        'onpopstate',
        'onresize',
        'onstorage',
        'onunload',
        'onblur',
        'onchange',
        'oncontextmenu',
        'onfocus',
        'oninput',
        'oninvalid',
        'onreset',
        'onsearch',
        'onselect',
        'onsubmit',
        'onkeydown',
        'onkeypress',
        'onkeyup',
        'onclick',
        'ondblclick',
        'onmousedown',
        'onmousemove',
        'onmouseout',
        'onmouseover',
        'onmouseup',
        'onmousewheel',
        'onwheel',
        'ondrag',
        'ondragend',
        'ondragenter',
        'ondragleave',
        'ondragover',
        'ondragstart',
        'ondrop',
        'onscroll',
        'oncopy',
        'oncut',
        'onpaste',
        'onabort',
        'oncanplay',
        'oncanplaythrough',
        'oncuechange',
        'ondurationchange',
        'onemptied',
        'onended',
        'onerror',
        'onloadeddata',
        'onloadedmetadata',
        'onloadstart',
        'onpause',
        'onplay',
        'onplaying',
        'onprogress',
        'onratechange',
        'onseeked',
        'onseeking',
        'onstalled',
        'onsuspend',
        'ontimeupdate',
        'onvolumechange',
        'onwaiting',
        'ontoggle',
    ];

    /**
     * @param string|Crawler $html See {@link crawler}. If this is a string, a new Crawler will be created from the
     *                             provided string. Otherwise, the provided crawler will be used. Since it is an object,
     *                             the provided object will be modified.
     * @since 1.9.0
     */
    public function __construct($html) {
        $this->dummyBot = new DummyBot([]);

        $this->crawler = $html instanceof Crawler
            ? $html
            : $this->dummyBot->createDummyCrawler($html);
    }

    /**
     * Remove the scripts from the provided HTML code or the crawler
     * 
     * @return self
     * @since 1.9.0
     */
    public function removeScripts(): self {
        // Remove script elements
        $this->dummyBot->removeElementsFromCrawler($this->crawler, "script");

        // Remove element attributes that can be used to run JavaScript
        foreach(static::getEventAttrNames() as $attr) {
            $this->dummyBot->removeElementAttributes($this->crawler, "[{$attr}]", $attr);
        }

        // Remove href attributes starting with "javascript"
        $this->dummyBot->removeElementAttributes($this->crawler, '[href^="javascript"]', 'href');
        return $this;
    }

    /**
     * Get the HTML code of the crawler (see {@link getCrawler()}). This must be called after calling
     * {@link removeScripts()} if the HTML code without the scripts is needed.
     * 
     * @return string 
     * @since 1.11.1
     */
    public function getHtml(): string {
        return $this->dummyBot->getContentFromDummyCrawler($this->getCrawler());
    }

    /**
     * Get the {@link crawler}. This must be called after calling {@link removeScripts()} if the HTML code without the
     * scripts is needed.
     * 
     * @return Crawler
     * @since 1.11.1
     */
    public function getCrawler(): Crawler {
        return $this->crawler;
    }

    /*
     * STATIC METHODS
     */

    /**
     * @return string[] See {@link eventAttrNames}
     * @since 1.9.0
     */
    public static function getEventAttrNames(): array {
        return static::$EVENT_ATTR_NAMES;
    }
}