<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 01/04/2022
 * Time: 14:40
 *
 * @since 1.12.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Crawling;

use WPCCrawler\Exceptions\CancelSavingException;
use WPCCrawler\Objects\Enums\PostStatus;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractStopActionCommand;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

/**
 * This command throws a {@link CancelSavingException}. {@link PostSaver} will catch the exception and stop the rest of
 * the crawling process. Other places that might run this command should handle the exception themselves.
 *
 * @since 1.12.0
 */
class Stop extends AbstractStopActionCommand {

    public function getKey(): string {
        return CommandKey::STOP;
    }

    public function getName(): string {
        return _wpcc('Stop');
    }

    public function getDescription(): ?string {
        return sprintf(
            _wpcc('Stops the execution of the crawling operation immediately. If there is a post that is'
            . ' already saved for the current URL, it will not be deleted. If this is executed during the crawling of'
            . ' a multi-page post, the status of the post will remain as "%1$s".'),
            PostStatus::DRAFT
        );
    }

    protected function getMainReasonMessage(): string {
        return sprintf(_wpcc('"%1$s" command requested the saving operation to be stopped.'), $this->getName());
    }

    protected function getReasonSettingDescription(): string {
        return _wpcc('A short explanation about why the crawling should be stopped.');
    }

    protected function onStop(string $message): void {
        throw new CancelSavingException($message);
    }

}