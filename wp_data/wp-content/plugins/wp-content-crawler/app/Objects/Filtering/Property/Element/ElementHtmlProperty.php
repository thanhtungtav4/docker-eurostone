<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 12/04/2020
 * Time: 09:06
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Element;

use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractActionProperty;
use WPCCrawler\Objects\Filtering\Property\Objects\CalculationResult;
use WPCCrawler\Utils;

class ElementHtmlProperty extends AbstractActionProperty {

    /**
     * @var array<int|string, Crawler> Stores the elements whose HTML is being modified, so that the elements can be
     *      replaced with their modified versions after the processing is done.
     */
    private $elementMap = [];

    public function getKey(): string {
        return PropertyKey::ELEMENT_HTML;
    }

    public function getName(): string {
        return _wpcc('HTML');
    }

    public function getInputDataTypes(): array {
        return [ValueType::T_ELEMENT];
    }

    public function getOutputDataTypes(): array {
        return [ValueType::T_STRING];
    }

    public function canAssignNewValue(): bool {
        return true;
    }

    protected function onCalculate($key, $source, AbstractBaseCommand $cmd): ?CalculationResult {
        if (!($source instanceof Crawler)) return null;

        $this->elementMap[$key] = $source;
        return new CalculationResult($key, Utils::getNodeHTML($source));
    }

    protected function onAssignNewValue(string $key, $newValue, AbstractActionCommand $cmd): void {
        if (!is_string($newValue)) return;

        // Get the element of this key
        $element = $this->elementMap[$key] ?? null;
        if ($element === null) return;

        // Replace the element with the new value. The new value is the new HTML code of the element.
        $dummyBot = new DummyBot([]);
        $newChild = $dummyBot->replaceElement($element, $newValue);
        if (!$newChild) return;

        // The replacement is made. If the subject was among the allowed subjects, replace it with its new value. Since
        // the previous object is replaced with another one, the new object should be among the allowed subjects as
        // well. Otherwise, the new subject cannot be modified by other commands if the command is restricted to only
        // the allowed subjects.
        $prevChild = $element->getNode(0);
        if ($prevChild) {
            $cmd->replaceAllowedSubject($prevChild, $newChild);
        }

    }

}