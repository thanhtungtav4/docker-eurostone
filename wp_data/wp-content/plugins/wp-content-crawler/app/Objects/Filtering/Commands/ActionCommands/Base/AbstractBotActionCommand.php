<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/04/2020
 * Time: 17:23
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base;


use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Interfaces\NeedsBot;

/**
 * Bot action commands require a bot so that they can retrieve the crawler and perform some operations on it.
 *
 * @since 1.11.0
 */
abstract class AbstractBotActionCommand extends AbstractActionCommand implements NeedsBot {

    /** @var AbstractBot|null */
    private $bot;

    /**
     * Execute this command
     *
     * @param Crawler|mixed $node
     * @since 1.11.0
     */
    abstract protected function onExecuteCommand($node): void;

    public function getInputDataTypes(): array {
        return [ValueType::T_ELEMENT];
    }

    protected function isOutputTypeSameAsInputType(): bool {
        return true;
    }

    protected function isTestable(): bool {
        return false;
    }

    protected function shouldReassignNewValues(): bool {
        return false;
    }

    protected function onExecute($key, $subjectValue) {
        $this->onExecuteCommand($subjectValue);
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