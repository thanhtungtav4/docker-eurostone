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
use WPCCrawler\Objects\Views\MultipleInputWithLabel;

class AddCategories extends AbstractActionCommand {

    const SEPARATOR = '>';

    public function getKey(): string {
        return CommandKey::ADD_CATEGORIES;
    }

    public function getName(): string {
        return _wpcc('Add categories');
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
            ->add((new ViewDefinition(MultipleInputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE,       _wpcc('Categories'))
                ->setVariable(ViewVariableName::INFO, sprintf(_wpcc('Enter the categories. Add a new item for each 
                    top-level category. You can add child categories to the top-level category by separating them with
                    %1$s character. For example, %2$s puts the post under that category, while %3$s puts it under the 
                    last category after creating its parent categories.'),
                    '<span class="highlight separator">' . static::SEPARATOR . '</span>',
                    '<span class="highlight category">Cities</span>',
                    sprintf('<span class="highlight category">Cities %1$s Metropolitan %1$s Chicago</span>', static::SEPARATOR)
                ))
                ->setVariable(ViewVariableName::NAME,        InputName::CATEGORIES)
                ->setVariable(ViewVariableName::PLACEHOLDER, _wpcc('Category names...'))
                ->setVariable(ViewVariableName::TYPE,        'text'))

            ->add((new ViewDefinition(CheckboxWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Delete other categories?'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('Check this if you want to delete other existing 
                    categories, if any exists.'))
                ->setVariable(ViewVariableName::NAME,  InputName::DELETE_EXISTING));
    }

    protected function onExecute($key, $subjectValue) {
        // The data source must be a PostData
        $dataSource = $this->getDataSource();
        if (!($dataSource instanceof PostData)) {
            return;
        }

        $deleteExisting = $this->getCheckboxOption(InputName::DELETE_EXISTING);

        // Get the current categories of the post. If it is null, or the user wants to delete the current tags, assign
        // it as an empty array.
        $originalCats = array_values($dataSource->getCategoryNames() ?: []);
        $prevCats = $originalCats;
        $prevCatsExist = $prevCats ? true : false;
        if ($deleteExisting) {
            $prevCats = [];
        }

        // Add the new categories to the previous categories and update the post's categories.
        $updatedCats = array_merge($prevCats, $this->getCategories());
        $dataSource->setCategoryNames($updatedCats);

        // If there is a logger, add some information to it.
        $logger = $this->getLogger();
        if ($logger) {
            // If the previous categories were deleted, add a message about that.
            if ($deleteExisting && $prevCatsExist) $logger
                ->addMessage(_wpcc('Previous categories are deleted, because the command is configured that way.'));

            // Add a message showing the current categories of the post
            $logger->addMessage(sprintf(
                _wpcc('The post now has these categories: %1$s'),
                $this->getCurrentCategoriesString($dataSource)
            ));

            // If nothing has changed in the post's categories, add a message about it.
            if ($originalCats === $dataSource->getCategoryNames() || (!$prevCatsExist && !$dataSource->getCategoryNames())) {
                $logger->addMessage(_wpcc('No change in the categories.'));
            }
        }

    }

    /*
     *
     */

    /**
     * @return array<int, string|string[]> The categories assigned via the "categories" option of the command, prepared
     *                                     in the structure specified in {@link PostData::$categoryNames}.
     * @since 1.11.0
     */
    protected function getCategories(): array {
        $categoriesRaw = $this->getOption(InputName::CATEGORIES);
        if ($categoriesRaw === null || !is_array($categoriesRaw)) {
            return [];
        }

        $result = [];
        foreach($categoriesRaw as $item) {
            $prepared = $this->prepareSingleCategoryItem($item);
            if ($prepared === null) continue;

            $result[] = $prepared;
        }

        return $result;
    }

    /**
     * @param string $item A single category item of the "categories" option
     * @return string|string[]|null
     * @since 1.11.0
     */
    protected function prepareSingleCategoryItem(string $item) {
        // Explode the item from the separator
        $exploded = explode(static::SEPARATOR, trim($item));
        if ($exploded === false) {
            return null;
        }

        $result = [];
        foreach($exploded as $cat) {
            // After explosion, each category can have spaces at the beginning or end of it. Remove those spaces and
            // make sure the value is not an empty string.
            $trimmed = trim($cat);
            if ($trimmed === '') continue;

            $result[] = $trimmed;
        }

        // If there is no category item, return null.
        if (!$result) return null;

        // If there is only one category, return it as a string. Otherwise, return a string array.
        return count($result) === 1 ? $result[0] : $result;
    }

    /**
     * @param PostData $postData The post data from which the categories will be retrieved
     * @return string A string that represents the post's current categories
     * @since 1.11.0
     */
    protected function getCurrentCategoriesString(PostData $postData): string {
        $cats = $postData->getCategoryNames();
        if (!$cats) {
            return '';
        }

        $result = [];
        foreach($cats as $item) {
            $val = is_array($item)
                ? implode(' ' . static::SEPARATOR . ' ', $item)
                : $item;

            $result[] = '"' . $val . '"';
        }

        return implode(', ', $result);
    }

}