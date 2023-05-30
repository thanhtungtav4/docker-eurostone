<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 15/11/2020
 * Time: 01:07
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base;


use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Interfaces\NeedsBot;

abstract class AbstractCrawlingConditionCommand extends AbstractConditionCommand implements NeedsBot {

    /** @var PostBot|null */
    private $bot = null;

    public function getInputDataTypes(): array {
        return [ValueType::T_CRAWLING];
    }

    public function doesNeedSubjectValue(): bool {
        return false;
    }

    protected function isTestable(): bool {
        return false;
    }

    protected function onDoesApply($subjectValue): bool {
        $bot = $this->getBot();
        return $bot instanceof PostBot ? $this->onCheckCondition($bot) : false;
    }

    /**
     * @param PostBot $postBot The post bot that can be used to retrieve certain crawling-related information.
     * @return bool True if the condition is met. Otherwise, false.
     * @since 1.11.0
     */
    abstract protected function onCheckCondition(PostBot $postBot): bool;

    /*
     *
     */

    public function setBot(?AbstractBot $bot): void {
        $this->bot = $bot instanceof PostBot ? $bot : null;
    }

    public function getBot(): ?AbstractBot {
        return $this->bot;
    }

}