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

class TextIsLowerCase extends AbstractTextConditionCommand {

    public function getKey(): string {
        return CommandKey::TEXT_IS_LOWERCASE;
    }

    protected function hasOptions(): bool {
        return false;
    }

    public function getName(): string {
        return _wpcc('Is lower case');
    }

    protected function getInputDescription(): string {
        return '';
    }

    protected function onCheckCondition(string $subjectValue, string $optionValue): bool {
        if ($subjectValue === '') return false;

        return Str::lower($subjectValue) === $subjectValue;
    }

}