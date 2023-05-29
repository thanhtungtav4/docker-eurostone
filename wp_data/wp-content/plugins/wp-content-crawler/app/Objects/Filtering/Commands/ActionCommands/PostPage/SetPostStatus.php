<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/07/2020
 * Time: 11:30
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
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\Select\SelectPostStatusWithLabel;
use WPCCrawler\Utils;

class SetPostStatus extends AbstractActionCommand {

    public function getKey(): string {
        return CommandKey::SET_POST_STATUS;
    }

    public function getName(): string {
        return _wpcc('Set post status');
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
            ->add((new ViewDefinition(SelectPostStatusWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Post status'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('Select the post status'))
                ->setVariable(ViewVariableName::NAME,  InputName::POST_STATUS));
    }

    protected function onExecute($key, $subjectValue) {
        // The data source must be a PostData
        $dataSource = $this->getDataSource();
        if (!($dataSource instanceof PostData)) {
            return;
        }

        $logger = $this->getLogger();

        // Get the defined post status
        $postStatus = $this->getOption(InputName::POST_STATUS);

        // If the defined post status does not exist, stop.
        if (!$postStatus || !in_array($postStatus, array_keys(Utils::getPostStatuses()))) {
            $message = sprintf(
                _wpcc('The post status is not set by "%s" command, because the given post status is not valid.'),
                $this->getName()
            );

            // Notify the user
            Informer::addInfo($message)->addAsLog();
            if ($logger) $logger->addMessage($message);

            return;
        }

        // The post status exists. Assign the post status to post data so that it will be used when saving the post.
        $dataSource->setPostStatus($postStatus);

        // If there is a logger, add a message telling the assigned post status.
        if ($logger) $logger->addMessage(sprintf(_wpcc('The post status is set as "%s".'), $postStatus));
    }

}