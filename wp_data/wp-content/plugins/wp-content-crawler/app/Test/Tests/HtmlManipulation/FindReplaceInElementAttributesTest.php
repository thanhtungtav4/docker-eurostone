<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/10/2018
 * Time: 17:53
 */

namespace WPCCrawler\Test\Tests\HtmlManipulation;


use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Test\Base\AbstractHtmlManipulationTest;
use WPCCrawler\Test\Base\AbstractTest;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Utils;

class FindReplaceInElementAttributesTest extends AbstractHtmlManipulationTest {

    /** @var string|null */
    private $url;

    /** @var string|null */
    private $content;

    /** @var string|null */
    private $selector;

    /** @var string|null */
    private $attr;

    /** @var string|null */
    private $find;

    /** @var string|null */
    private $replace;

    /** @var bool */
    private $regex = false;

    /**
     * Get the last HTML manipulation step. See {@link applyHtmlManipulationOptions}
     *
     * @return null|int
     */
    protected function getLastHtmlManipulationStep(): ?int {
        return AbstractTest::MANIPULATION_STEP_INITIAL_REPLACEMENTS;
    }

    /**
     * Define instance variables.
     * @return void
     */
    protected function defineVariables() {
        $formItemValues = $this->getData()->getFormItemValues();
        if (!is_array($formItemValues)) $formItemValues = [];

        $this->url      = $this->getData()->get("url");
        $this->content  = $this->getData()->get("subject");
        $this->selector = Utils::array_get($formItemValues, SettingInnerKey::SELECTOR);
        $this->attr     = Utils::array_get($formItemValues, SettingInnerKey::ATTRIBUTE);
        $this->find     = Utils::array_get($formItemValues, SettingInnerKey::FIND);
        $this->replace  = Utils::array_get($formItemValues, SettingInnerKey::REPLACE);
        $this->regex    = isset($formItemValues[SettingInnerKey::REGEX]);
    }

    /**
     * @return string
     */
    protected function getMessageLastPart(): string {
        return sprintf('%1$s %2$s %3$s %4$s %5$s',
            $this->selector   ? "<span class='highlight selector'>" . $this->selector . "</span>"                   : '',
            $this->attr       ? "<span class='highlight attribute'>" . $this->attr . "</span>"                      : '',
            $this->find       ? "<span class='highlight find'>" . htmlspecialchars($this->find) . "</span>"         : '',
            $this->replace    ? "<span class='highlight replace'>" . htmlspecialchars($this->replace) . "</span>"   : '',
            $this->regex      ? _wpcc("(as regex)") : ''
        );
    }

    /**
     * Returns a manipulated {@link Crawler}. {@link AbstractBot} is the bot that is used to get the data from the
     * target URL and it can be used to manipulate the content.
     *
     * @param Crawler $crawler
     * @param AbstractBot $bot
     * @return Crawler
     */
    protected function manipulate($crawler, $bot) {
        $bot->findAndReplaceInElementAttribute($crawler, [$this->selector], $this->attr,
            $this->find, $this->replace === null ? '' : $this->replace, $this->regex);
        return $crawler;
    }

    /**
     * Conduct the test and return an array of results.
     *
     * @param TestData $data Information required for the test
     * @return array|null
     */
    protected function onCreateResults($data): ?array {
        return $this->createHtmlManipulationResults($this->url, $this->content, $this->selector, $this->getMessageLastPart());
    }
}