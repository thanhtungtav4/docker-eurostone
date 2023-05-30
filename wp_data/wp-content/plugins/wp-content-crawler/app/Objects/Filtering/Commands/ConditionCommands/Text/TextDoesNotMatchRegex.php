<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 28/03/2020
 * Time: 21:29
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Text;


use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractTextConditionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;
use WPCCrawler\Objects\Filtering\FilteringUtils;

class TextDoesNotMatchRegex extends AbstractTextConditionCommand {

    public function getKey(): string {
        return CommandKey::TEXT_DOES_NOT_MATCH_REGEX;
    }

    public function getName(): string {
        return _wpcc('Does not match regex');
    }

    protected function getInputName(): string {
        return _wpcc('Regular expression');
    }

    protected function getInputDescription(): string {
        return _wpcc('Enter the regular expression that the text should not match.') . ' ' . _wpcc_trans_regex_base();
    }

    protected function addCaseInsensitiveCheckbox(): bool {
        return false;
    }

    protected function onCheckCondition(string $subjectValue, string $optionValue): bool {
        // The option's value is a regular expression for this command.
        if ($optionValue === '') return false;

        return FilteringUtils::doesTextMatchRegex($subjectValue, $optionValue, $this->getName(), $this->getLogger()) === 0;
    }

}