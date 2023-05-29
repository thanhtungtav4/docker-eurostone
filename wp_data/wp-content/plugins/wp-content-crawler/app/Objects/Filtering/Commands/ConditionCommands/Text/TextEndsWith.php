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


use Illuminate\Support\Str;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractTextConditionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

class TextEndsWith extends AbstractTextConditionCommand {

    public function getKey(): string {
        return CommandKey::TEXT_ENDS_WITH;
    }

    public function getName(): string {
        return _wpcc('Ends with');
    }

    protected function getInputDescription(): string {
        return _wpcc('Enter the value that the text should end with');
    }

    protected function onCheckCondition(string $subjectValue, string $optionValue): bool {
        if ($subjectValue === '' || $optionValue === '') return false;

        return Str::endsWith($subjectValue, $optionValue);
    }

}