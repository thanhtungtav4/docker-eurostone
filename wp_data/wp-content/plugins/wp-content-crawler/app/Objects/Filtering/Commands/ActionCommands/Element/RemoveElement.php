<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/04/2020
 * Time: 17:23
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Element;


use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractBotActionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

/**
 * Used to remove elements from the crawler by using the given CSS selectors
 *
 * @since 1.11.0
 */
class RemoveElement extends AbstractBotActionCommand {

    public function getKey(): string {
        return CommandKey::REMOVE_ELEMENT;
    }

    public function getName(): string {
        return _wpcc('Remove');
    }

    protected function onExecuteCommand($node): void {
        if (!$node) return;

        $bot = $this->getBot();
        if (!$bot) return;

        $bot->removeNode($node);
    }

}