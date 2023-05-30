<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 01/04/2020
 * Time: 07:51
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\PostPage;


use WPCCrawler\Objects\Crawling\Data\PostData;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Views\Select\SelectAuthorWithLabel;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;

class SetAuthor extends AbstractActionCommand {

    public function getKey(): string {
        return CommandKey::SET_AUTHOR;
    }

    public function getName(): string {
        return _wpcc('Set author');
    }

    public function getInputDataTypes(): array {
        return [ValueType::T_POST_PAGE];
    }

    protected function isOutputTypeSameAsInputType(): bool {
        return true;
    }

    public function doesNeedSubjectValue(): bool {
        return false;
    }

    protected function isTestable(): bool {
        return false;
    }

    protected function createViews(): ?ViewDefinitionList {
        return (new ViewDefinitionList())
            ->add((new ViewDefinition(SelectAuthorWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Author'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('Select the author'))
                ->setVariable(ViewVariableName::NAME,  InputName::AUTHOR_ID));
    }

    protected function onExecute($key, $subjectValue) {
        // The data source must be a PostData
        $dataSource = $this->getDataSource();
        if (!($dataSource instanceof PostData)) {
            return;
        }

        $logger = $this->getLogger();

        // Get the defined author ID and its nickname. We get the nickname to make sure the author exists.
        $authorId       = (int) $this->getOption(InputName::AUTHOR_ID);
        $authorNickname = $authorId ? get_the_author_meta('nickname', $authorId) : null;

        // If the defined author does not exist, stop.
        if (!$authorId || !$authorNickname) {
            $message = sprintf(
                _wpcc('The author is not set by "%s" command, because the given author ID is not valid.'),
                $this->getName()
            );

            // Notify the user
            Informer::addInfo($message)->addAsLog();
            if ($logger) $logger->addMessage($message);

            return;
        }

        // The author exists. Assign the author ID to post data so that it will be used when saving the post.
        $dataSource->setAuthorId($authorId);

        // If there is a logger, add a message telling the assigned author.
        if ($logger) $logger->addMessage(sprintf(_wpcc('The post author is set as "%s".'), $authorNickname));
    }

}