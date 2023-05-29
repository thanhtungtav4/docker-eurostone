<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/04/2020
 * Time: 09:19
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base;


use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Interfaces\NeedsBot;

/**
 * These condition commands require a bot and checks the conditions on the crawler retrieved from the bot.
 *
 * @since 1.11.0
 */
abstract class AbstractBotConditionCommand extends AbstractConditionCommand implements NeedsBot {

    /** @var AbstractBot|null */
    private $bot;

    /**
     * @param Crawler $node A node
     * @return bool True if the condition applies. Otherwise, false.
     * @since 1.11.0
     */
    protected abstract function onCheckCondition($node): bool;

    public function getInputDataTypes(): array {
        return [ValueType::T_ELEMENT];
    }

    protected function isTestable(): bool {
        return false;
    }

    protected function onDoesApply($subjectValue): bool {
        return $this->onCheckCondition($subjectValue);
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