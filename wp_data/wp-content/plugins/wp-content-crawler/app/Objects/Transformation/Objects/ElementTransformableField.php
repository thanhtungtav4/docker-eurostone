<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 12/04/2020
 * Time: 14:21
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Transformation\Objects;


use DOMNode;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Filtering\Interfaces\NeedsBot;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;
use WPCCrawler\Utils;

class ElementTransformableField extends TransformableField implements NeedsBot {

    /** @var AbstractBot|null */
    private $bot;

    public function getSubjectItem($key, $value) {
        // We work with DOM elements in this field. That is because the elements do not have a unique key that we can
        // use. Objects are persisted. So, to be able to identify the same elements later in the action commands, we use
        // objects. But, we cannot use Crawler instances because the same DOM element might exist in different Crawler
        // instances. Instead, we directly use DOM elements, since they are persisted and unique.
        //  * Persisted: Not like "saved to the db" but like "the same object is maintained"

        /** @var Crawler $value */
        if (is_a($value, Crawler::class)) {
            return $value->getNode(0);
        }

        $cmd = $this->getCommand();
        if (!$cmd) return null;

        // This value is not a Crawler. It is probably a property of the Crawler, such as an attribute value or HTML.
        // Try to get this item from the original extracted values, if it is possible.
        $lastSubjectValues = $cmd->getLastExtractedSubjectValues();
        if (!$lastSubjectValues) return null;

        $subject = $lastSubjectValues[$key] ?? null;
        if ($subject === null || !is_a($subject, Crawler::class)) return null;

        /** @var Crawler $subject */
        return $subject->getNode(0);
    }

    public function getSubjectItemForHumans($key, $value, $subjectItem): ?string {
        if (!$subjectItem || !is_a($subjectItem, DOMNode::class)) return null;

        /** @var DOMNode $subjectItem */
        return Utils::getDomNodeHtml($subjectItem);
    }

    protected function onExtractSubjectValues(?Transformable $dataSource): ?array {
        $cmd = $this->getCommand();
        if (!$cmd) return null;

        $bot = $this->getBot();
        if (!$bot) return null;

        $crawler = $bot->getCrawler();
        if (!$crawler) return null;

        // Get the assigned selectors. If the option does not exist, return null.
        $selectorOption = $this->getSelectorOption();
        if ($selectorOption === null) return null;

        // Find the elements matching the selectors and collect them in an array
        $results = [];
        foreach($selectorOption as $selectorData) {
            $nodes = $bot->getElementsFromCrawler($crawler, $selectorData);
            if ($nodes === null) continue;

            $results = array_merge($results, $nodes);
        }

        return $results;
    }

    /**
     * @return array|null Selector option's value. If there is no command, no selector option or no selector option
     *                    with a non-empty CSS selector, returns null. Otherwise, the selector option, which is an
     *                    array of selector data (associative array). The result is filtered such that each selector
     *                    data contains a non-empty CSS selector.
     * @since 1.11.0
     */
    public function getSelectorOption(): ?array {
        $cmd = $this->getCommand();
        if (!$cmd) return null;

        $selectorOption = $cmd->getOption(InputName::CSS_SELECTOR);
        $value = !is_array($selectorOption) || !$selectorOption
            ? null
            : $selectorOption;

        if ($value === null) return null;

        // Remove items with empty CSS selectors
        $value = array_filter($value, function($item) {
            // Include the item only if it is an array with a non-empty CSS selector
            return is_array($item) && isset($item[SettingInnerKey::SELECTOR]) &&
                trim($item[SettingInnerKey::SELECTOR]) !== '';
        });

        // If, after removal, there is no item in the array, return null. Otherwise, return the value.
        return $value ?: null;
    }

    /*
     *
     */

    public function setBot(?AbstractBot $bot): void {
        $this->bot = $bot;
    }

    public function getBot(): ?AbstractBot {
        return $this->bot;
    }

}