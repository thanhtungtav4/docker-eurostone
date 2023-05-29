<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 19/11/2020
 * Time: 19:58
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Crawling;

use WPCCrawler\Exceptions\CancelSavingAndDeleteException;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractStopActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Views\CheckboxWithLabel;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;

/**
 * This command throws a {@link CancelSavingAndDeleteException}. {@link PostSaver} will catch the exception, stop the rest of the
 * crawling process, and delete the post. Other places that might run this command should handle the exception
 * themselves.
 *
 * @since 1.11.0
 */
class StopAndDeletePost extends AbstractStopActionCommand {

    // TODO: Although this has unit tests, integration tests must be written as well.

    public function getKey(): string {
        return CommandKey::STOP_AND_DELETE_POST;
    }

    public function getName(): string {
        return _wpcc('Stop and delete the post');
    }

    public function getDescription(): ?string {
        return _wpcc('Stops the execution of the crawling operation immediately. Then, deletes the post created in 
            your site for the current URL, if the post exists.');
    }

    protected function getMainReasonMessage(): string {
        return sprintf(_wpcc('"%1$s" command requested the saving operation to be stopped and the post 
            currently being crawled to be deleted.'), $this->getName());
    }

    protected function getReasonSettingDescription(): string {
        return _wpcc('A short explanation about why the crawling should be stopped and the post should be deleted.');
    }

    protected function createViews(): ViewDefinitionList {
        return parent::createViews()
            ->add((new ViewDefinition(CheckboxWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Delete URL?'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('Check this if the URL of the post should be deleted 
                    from the database. When the URL is deleted, the plugin will be able to save the URL to the database
                    again, if the URL is found in a category page. This means that the post can be saved again. If the
                    URL is not deleted, the post will not be saved again.'))
                ->setVariable(ViewVariableName::NAME,  InputName::DELETE_URL)
            );
    }

    protected function onStop(string $message): void {
        throw (new CancelSavingAndDeleteException($message))
            ->setDeleteUrl($this->shouldDeleteUrl());
    }

    /*
     * HELPERS
     */

    /**
     * @return bool True if the "delete URL" option is checked. Otherwise, false.
     * @since 1.11.0
     */
    protected function shouldDeleteUrl(): bool {
        return $this->getCheckboxOption(InputName::DELETE_URL);
    }
}