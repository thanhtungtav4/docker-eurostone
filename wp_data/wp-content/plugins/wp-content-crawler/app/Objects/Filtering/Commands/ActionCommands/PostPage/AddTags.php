<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 22/02/2021
 * Time: 12:05
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
use WPCCrawler\Objects\Views\CheckboxWithLabel;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\InputWithLabel;

class AddTags extends AbstractActionCommand {

    public function getKey(): string {
        return CommandKey::ADD_TAGS;
    }

    public function getName(): string {
        return _wpcc('Add tags');
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
            ->add((new ViewDefinition(InputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Tags'))
                ->setVariable(ViewVariableName::INFO,  sprintf(_wpcc('Define the tags that will be added to the 
                    tags of the post. To enter multiple tags, separate them with commas. For example, %1$s defines only 
                    one tag, while %2$s defines two tags.'),
                    '<span class="highlight tag">my tag</span>',
                    '<span class="highlight tag">my, tag</span>'
                ))
                ->setVariable(ViewVariableName::NAME,  InputName::TEXT)
                ->setVariable(ViewVariableName::TYPE,  'text'))

            ->add((new ViewDefinition(CheckboxWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Delete other tags?'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('Check this if you want to delete other existing 
                    tags, if any exists.'))
                ->setVariable(ViewVariableName::NAME,  InputName::DELETE_EXISTING));
    }

    protected function onExecute($key, $subjectValue) {
        // The data source must be a PostData
        $dataSource = $this->getDataSource();
        if (!($dataSource instanceof PostData)) {
            return;
        }

        $deleteExisting = $this->getCheckboxOption(InputName::DELETE_EXISTING);

        // Get the current tags of the post. If it is null, or the user wants to delete the current tags, assign it as
        // an empty array.
        $originalTags = array_values($dataSource->getPreparedTags() ?: []);
        $prevTags = $originalTags;
        $prevTagsExist = $prevTags ? true : false;
        if ($deleteExisting) {
            $prevTags = [];
        }

        // Add the new tags to the previous tags and update the post's tags.
        $updatedTags = array_merge($prevTags, $this->getTags());
        $dataSource->setPreparedTags($updatedTags);

        // If there is a logger, add some information to it.
        $logger = $this->getLogger();
        if ($logger) {
            // If the previous tags were deleted, add a message about that.
            if ($deleteExisting && $prevTagsExist) $logger
                ->addMessage(_wpcc('Previous tags are deleted, because the command is configured that way.'));

            // Add a message showing the current tags of the post
            $logger->addMessage(sprintf(
                _wpcc('The post now has these tags: %1$s'),
                implode(', ', $updatedTags)
            ));

            // If nothing has changed in the post's tags, add a message about it.
            if ($originalTags === $dataSource->getPreparedTags() || (!$prevTagsExist && !$dataSource->getPreparedTags())) {
                $logger->addMessage(_wpcc('No change in the tags.'));
            }
        }

    }

    /*
     *
     */

    /**
     * @return string[] The tags assigned via the "tags" option of the command, prepared.
     * @since 1.11.0
     */
    protected function getTags(): array {
        $tagsRaw = $this->getOption(InputName::TEXT);
        if ($tagsRaw === null) {
            return [];
        }

        // Explode from the commas
        $tags = explode(',', $tagsRaw);
        if ($tags === false) { // @phpstan-ignore-line
            return [];
        }

        // After exploding, there might be spaces at the beginning and at the end of the tags. Remove them.
        $trimmed = array_map(function($tag) {
            return trim($tag);
        }, $tags);

        // After trimming, there might be empty strings. Return the tags that are not empty strings.
        return array_filter($trimmed, function($tag) {
            return $tag !== '';
        });
    }

}