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

class TextIsNotUpperCase extends AbstractTextConditionCommand {

    public function getKey(): string {
        return CommandKey::TEXT_IS_NOT_UPPERCASE;
    }

    protected function hasOptions(): bool {
        return false;
    }

    public function getName(): string {
        return _wpcc('Is not upper case');
    }

    protected function getInputDescription(): string {
        return '';
    }

    protected function onCheckCondition(string $subjectValue, string $optionValue): bool {
        if ($subjectValue === '') return false;

        return Str::upper($subjectValue) !== $subjectValue;
    }

}