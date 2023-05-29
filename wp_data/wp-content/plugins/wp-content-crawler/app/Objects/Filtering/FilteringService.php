<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/03/2020
 * Time: 21:49
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering;


use Illuminate\Support\Arr;
use WPCCrawler\Environment;
use WPCCrawler\Objects\Crawling\Data\CategoryData;
use WPCCrawler\Objects\Crawling\Data\PostData;
use WPCCrawler\Objects\Events\Base\AbstractCrawlingEvent;
use WPCCrawler\Objects\Events\EventService;
use WPCCrawler\Objects\Filtering\Commands\CommandService;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandType;
use WPCCrawler\Objects\Filtering\Interfaces\HasTestViewDefinitions;
use WPCCrawler\Objects\Filtering\Interfaces\HasViewDefinitions;
use WPCCrawler\Objects\Filtering\Objects\FieldConfig;
use WPCCrawler\Objects\Filtering\Property\PropertyService;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;
use WPCCrawler\Objects\Transformation\Objects\TransformableField;
use WPCCrawler\Objects\Transformation\Objects\TransformableFieldList;
use WPCCrawler\Objects\Views\TestResultContainer;
use WPCCrawler\Objects\Views\ViewService;
use WPCCrawler\PostDetail\PostDetailsService;

class FilteringService {

    const AJAX_CMD_VIEW_DEFINITIONS = 'viewDefinitions';

    /** @var SettingsImpl */
    private $settings;

    /**
     * @var null|array Key-value pairs where keys are view keys and the values are JS templates of the views. This is
     *                 used to collect the view definitions from all items that {@link HasViewDefinitions}
     */
    private $viewTemplates = null;

    /**
     * @var null|array Array of arrays. Each inner array has these:
     *                 string 'identifier'  Identifier of the subject group, such as 'post' or 'woocommerce'
     *                 string 'name'        Human-readable name of the subject group, such as 'Post' or 'WooCommerce'
     *                 array  'subjects'    Array of arrays. Each inner array has these:
     *                      string 'title'          Title of the subject, such as 'Post URL' or 'Product Attribute Values'
     *                      string 'key'            Dot key of this subject, i.e. transformable field
     *                      array  'dataTypes'      An array of integers each corresponding to a data type
     *                      array  'fieldConfigs'   Array representations of the field configs
     *                      array  'filterViews'    Array representation of view definition list
     */
    private $subjectGroupsArray = null;

    /**
     * @var null|array Key-value pairs where keys are dot keys of the subjects (with identifiers) and the values are the
     *                 titles of the subjects.
     */
    private $subjectTitleMap = null;

    /**
     * @param SettingsImpl $settings Site settings for which the filtering service is needed.
     * @since 1.11.0
     */
    public function __construct(SettingsImpl $settings) {
        $this->settings = $settings;
    }

    /**
     * Handle an AJAX request
     *
     * @param array $data Request data
     * @return array The result
     * @since 1.11.0
     */
    public function handleAjax($data): array {
        if (!is_array($data)) return [];

        $cmd = Arr::get($data, 'filteringCmd');
        if ($cmd === static::AJAX_CMD_VIEW_DEFINITIONS) {
            return $this->getDefinitionsAsArray();
        }

        return [];
    }

    /**
     * Get all of the values required to implement the filtering in the front-end
     *
     * @return array Definitions of subjects, properties, commands, and views  as array. The main contains 'subjects',
     *               'properties', 'commands', and 'viewTemplates' keys
     * @since 1.11.0
     */
    public function getDefinitionsAsArray(): array {
        $this->viewTemplates = [];

        // Add the views of commands and properties
        $this->addViewTemplatesFromItems(array_values($this->propertyService()->getRegistry()));
        $this->addViewTemplatesFromItems(array_values($this->commandService()->getRegistry()));
        $this->addViewDefinitionList($this->createCustomViews());

        $result = [
            'properties'    => $this->propertyService()->getPropertiesAsArray(),
            'subjectGroups' => $this->getSubjectGroupsAsArray(),
            'events'        => $this->getEventsAsArray(),
            'commands'      => $this->commandService()->getCommandsAsArray(),
            'viewTemplates' => $this->viewTemplates,
        ];

        $this->viewTemplates = null;

        return $result;
    }

    /**
     * @return array See {@link subjectGroupsArray}
     * @since 1.11.0
     */
    public function getSubjectGroupsAsArray(): array {
        if ($this->subjectGroupsArray === null) {
            $this->subjectGroupsArray = $this->createSubjectGroupsArray();
        }

        return $this->subjectGroupsArray;
    }

    /**
     * @return array See {@link subjectTitleMap}
     * @since 1.11.0
     */
    public function getSubjectTitleMap(): array {
        if ($this->subjectTitleMap === null) {
            $this->subjectTitleMap = $this->createSubjectTitleMap();
        }

        return $this->subjectTitleMap;
    }

    /**
     * @return SettingsImpl
     * @since 1.11.0
     */
    public function getSettings(): SettingsImpl {
        return $this->settings;
    }

    /**
     * Get the command service
     *
     * @return CommandService
     * @since 1.11.0
     */
    public function commandService(): CommandService {
        return CommandService::getInstance();
    }

    /**
     * Get the property service
     *
     * @return PropertyService
     * @since 1.11.0
     */
    public function propertyService(): PropertyService {
        return PropertyService::getInstance();
    }

    /*
     *
     */

    /**
     * Calls {@link addViewDefinitionList()} for each view definition list in the given array
     *
     * @param HasViewDefinitions[]|HasTestViewDefinitions[] $itemsWithViews
     * @since 1.11.0
     */
    private function addViewTemplatesFromItems(?array $itemsWithViews): void {
        if (!$itemsWithViews) return;

        foreach($itemsWithViews as $itemWithView) {
            if (is_a($itemWithView, HasViewDefinitions::class)) {
                $this->addViewDefinitionList($itemWithView->getViewDefinitions());
            }

            if (is_a($itemWithView, HasTestViewDefinitions::class)) {
                $this->addViewDefinitionList($itemWithView->getTestViewDefinitions());
            }
        }
    }

    /**
     * Add the templates of the view definitions into {@link viewTemplates}
     *
     * @param ViewDefinitionList|null $viewList
     * @since 1.11.0
     */
    private function addViewDefinitionList(?ViewDefinitionList $viewList): void {
        if (!$viewList) return;

        $viewService = ViewService::getInstance();

        foreach($viewList->getItems() as $item) {
            $viewInstance = $viewService->getViewInstance($item->getViewClass());
            if ($viewInstance === null) continue;

            $viewKey = $viewInstance->getKey();

            // If the template already exists, continue with the next one.
            if (isset($this->viewTemplates[$viewKey])) continue;

            $this->viewTemplates[$viewKey] = $viewInstance->renderAsJsTemplate();
        }
    }

    /**
     * @return array Creates an array structured the same as {@link subjectGroupsArray}
     * @since 1.11.0
     */
    private function createSubjectGroupsArray(): array {
        // Add the view templates of the special fields
        $specialFields = SpecialFieldService::getInstance()->getSpecialFields();
        $this->addViewTemplatesFromItems($specialFields->getItems());

        $result = [
            // Create the transformable fields for category data
            $this->createTransformableFieldGroup(
                Environment::defaultCategoryIdentifier(),
                _wpcc('Category'),
                $this->getCommandFields(new CategoryData())
            ),

            // Create special transformable fields
            $this->createTransformableFieldGroup(
                SpecialFieldService::SPECIAL_FIELD_IDENTIFIER,
                _wpcc('Special'),
                $specialFields->toArray()
            ),

            // Create the transformable fields for a default WordPress post
            $this->createTransformableFieldGroup(
                Environment::defaultPostIdentifier(),
                _wpcc('Post'),
                $this->getCommandFields(new PostData())
            ),
        ];

        // Create registered post details' transformable fields
        $factories = PostDetailsService::getInstance()->getTransformableFactories($this->getSettings());
        foreach($factories as $factory) {
            /** @var Transformable $data */
            $data = $factory->getDetailData();
            $result[] = $this->createTransformableFieldGroup(
                $factory->getIdentifier(),
                $factory->getName(),
                $this->getCommandFields($data)
            );
        }

        return $result;
    }

    /**
     * @param string $identifier Identifier of the group
     * @param string $name       Human-readable name of the group
     * @param array  $subjects   An array of the array versions of a {@link Transformable}'s fields. See
     *                           {@link getCommandFields()}.
     * @return array An array that can be sent to the front-end
     * @since 1.11.0
     */
    private function createTransformableFieldGroup(string $identifier, string $name, array $subjects): array {
        return [
            'identifier' => $identifier,
            'name'       => $name,
            'subjects'   => $subjects
        ];
    }

    /**
     * Get the array versions of {@link TransformableField}s that can be used by the commands
     *
     * @param Transformable $data
     * @return array
     * @since 1.11.0
     */
    private function getCommandFields(Transformable $data): array {
        $transformableFields    = $data->getInteractableFields();
        $actionCommandFields    = $data->getActionCommandFields();
        $conditionCommandFields = $data->getConditionCommandFields();

        $fields = $transformableFields->toArray();
        $this->addCommandFields($fields, $actionCommandFields,    CommandType::ACTION);
        $this->addCommandFields($fields, $conditionCommandFields, CommandType::CONDITION);

        // Add the view templates of the fields
        $this->addViewTemplatesFromItems($transformableFields->getItems());
        if($actionCommandFields)    $this->addViewTemplatesFromItems($actionCommandFields->getItems());
        if($conditionCommandFields) $this->addViewTemplatesFromItems($conditionCommandFields->getItems());

        return $fields;
    }

    /**
     * Prepare command fields and add them into an array
     *
     * @param array                       $fields       An array in which the array versions of
     *                                                  {@link TransformableField}s will be added
     * @param TransformableFieldList|null $fieldList    A list of fields that will be prepared and added to the given
     *                                                  fields array
     * @param string|string[]             $commandTypes The command types for the fields in the given list. These types
     *                                                  will assigned to the fields in the given list
     * @since 1.11.0
     */
    private function addCommandFields(array &$fields, ?TransformableFieldList $fieldList, $commandTypes): void {
        if ($fieldList === null) return;
        if (!is_array($commandTypes)) $commandTypes = [$commandTypes];

        $items = $fieldList->getItems();
        array_walk($items, function($item) use (&$commandTypes) {
            /** @var TransformableField $item */

            // If there is no field config, assign a new field config with the given command types and null event group
            $fieldConfigs = $item->getFieldConfigs();
            if ($fieldConfigs === null) {
                $item->addFieldConfig(new FieldConfig(null, $commandTypes));

            } else {
                // Otherwise, modify each field config so that they only have the given command types
                foreach($fieldConfigs as $fieldConfig) $fieldConfig->setCommandTypes($commandTypes);
            }
        });

        $fields = array_merge($fields, $fieldList->toArray());
    }

    /**
     * @return array Array representations of the events supported by the filters
     * @since 1.11.0
     */
    private function getEventsAsArray(): array {
        $events = EventService::getInstance()->getAllEvents(AbstractCrawlingEvent::class);
        return array_map(function($event) {
            /** @var AbstractCrawlingEvent $event */
            return $event->toArray();
        }, $events);
    }

    /**
     * Get custom views that are needed in the UI. These views are the views whose definitions exist in the backend and
     * that are needed in the frontend. By sending these views from backend, we keep the code DRY.
     *
     * @return ViewDefinitionList A list that contains custom views that are needed for special purposes
     * @since 1.11.0
     */
    private function createCustomViews(): ViewDefinitionList {
        return (new ViewDefinitionList())
            ->add(new ViewDefinition(TestResultContainer::class));
    }

    /**
     * @return array Creates an array structured as {@link subjectTitleMap}
     * @since 1.11.0
     */
    private function createSubjectTitleMap(): array {
        $result = [];
        foreach($this->getSubjectGroupsAsArray() as $subjectGroup) {
            $identifier = $subjectGroup['identifier'];

            foreach($subjectGroup['subjects'] as $subject) {
                $result["$identifier.{$subject['key']}"] = $subject['title'];
            }
        }

        return $result;
    }
}