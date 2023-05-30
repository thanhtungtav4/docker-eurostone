<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 01/04/2020
 * Time: 11:23
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering;


use WPCCrawler\Environment;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Events\Enums\EventGroupKey;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionFactory;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandType;
use WPCCrawler\Objects\Filtering\Enums\SpecialFieldKey;
use WPCCrawler\Objects\Filtering\Objects\FieldConfig;
use WPCCrawler\Objects\Transformation\Objects\ElementTransformableField;
use WPCCrawler\Objects\Transformation\Objects\Special\RequestTransformableField;
use WPCCrawler\Objects\Transformation\Objects\Special\SpecialTransformableField;
use WPCCrawler\Objects\Transformation\Objects\TransformableField;
use WPCCrawler\Objects\Transformation\Objects\TransformableFieldList;

class SpecialFieldService {

    /** @var SpecialFieldService|null */
    private static $instance = null;

    const SPECIAL_FIELD_IDENTIFIER = 'special';

    /** @var TransformableFieldList */
    private $specialFields = null;

    /** @var array Key-value pairs where the key is special field key and the value is its the data source identifier */
    private $fieldKeyDataSourceIdentifierMap = null;

    /**
     * @return SpecialFieldService
     * @since 1.11.0
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new SpecialFieldService();
        }

        return self::$instance;
    }

    /**
     * This is a singleton
     * @since 1.11.0
     */
    protected function __construct() { }

    /**
     * Get data source identifier of a special field
     *
     * @param string|null $specialFieldKey The special field's dot key
     * @return string|null If there is a data source identifier for the special field, it will be returned. Otherwise,
     *                     null.
     * @since 1.11.0
     */
    public function getDataSourceIdentifier(?string $specialFieldKey): ?string {
        if (!$specialFieldKey) return null;

        // Cache the map so that we can search it faster the next time.
        if ($this->fieldKeyDataSourceIdentifierMap === null) {
            $map = [];

            // Populate the map by assigning the data source identifier of each special field key
            foreach($this->getSpecialFields()->getItems() as $field) {
                /** @var SpecialTransformableField $field */
                $map[$field->getDotKey()] = is_a($field, SpecialTransformableField::class)
                    ? $field->getDataSourceIdentifier()
                    : null;
            }

            $this->fieldKeyDataSourceIdentifierMap = $map;
        }

        return $this->fieldKeyDataSourceIdentifierMap[$specialFieldKey] ?? null;
    }

    /**
     * Get the special fields
     *
     * @return TransformableFieldList
     * @since 1.11.0
     */
    public function getSpecialFields(): TransformableFieldList {
        if ($this->specialFields === null) {
            $this->specialFields = new TransformableFieldList([
                $this->createPostRelatedSpecialField(),
                $this->createElementSpecialField(),
                $this->createCrawlingSpecialField(),
                $this->createNotificationSpecialField(),
                $this->createRequestSpecialField(),
            ]);
        }

        return $this->specialFields;
    }

    /**
     * @param string $specialFieldKey One of the constants defined in {@link SpecialFieldKey}
     * @return string The given key prepended the identifier, i.e. {@link SPECIAL_FIELD_IDENTIFIER} and a ".", e.g.
     *                "special.{$specialFieldKey}"
     * @since 1.11.0
     */
    public function createFullDotKey(string $specialFieldKey): string {
        return static::SPECIAL_FIELD_IDENTIFIER . '.' . $specialFieldKey;
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * Create the "postRelated" special field
     *
     * @return TransformableField
     * @since 1.11.0
     */
    protected function createPostRelatedSpecialField(): TransformableField {
        return (new SpecialTransformableField(SpecialFieldKey::POST_RELATED, _wpcc('Post'), ValueType::T_POST_PAGE))
            ->setDataSourceIdentifier(Environment::defaultPostIdentifier())
            ->addFieldConfig(new FieldConfig(EventGroupKey::POST_DATA, CommandType::ACTION));
    }

    /**
     * Create the "element" special field
     *
     * @return TransformableField
     * @since 1.11.0
     */
    protected function createElementSpecialField(): TransformableField {
        $viewDefinitionFactory = ViewDefinitionFactory::getInstance();

        return (new ElementTransformableField(SpecialFieldKey::ELEMENT, _wpcc('Element'), [ValueType::T_ELEMENT, ValueType::T_COUNTABLE]))
            ->addFieldConfig(new FieldConfig(EventGroupKey::CATEGORY_PAGE))
            ->addFieldConfig(new FieldConfig(EventGroupKey::CATEGORY_DATA, CommandType::CONDITION))
            ->addFieldConfig(new FieldConfig(EventGroupKey::POST_PAGE))
            ->addFieldConfig(new FieldConfig(EventGroupKey::POST_DATA, CommandType::CONDITION))
            ->setViewDefinitions((new ViewDefinitionList())
                ->add($viewDefinitionFactory->createMultipleCssSelectorInput()));
    }

    /**
     * Create the "crawling" special field
     *
     * @return TransformableField
     * @since 1.11.0
     */
    protected function createCrawlingSpecialField(): TransformableField {
        return (new SpecialTransformableField(SpecialFieldKey::CRAWLING, _wpcc('Crawling'), ValueType::T_CRAWLING))
            ->setDataSourceIdentifier(Environment::defaultPostIdentifier())
            ->addFieldConfig(new FieldConfig(EventGroupKey::POST_REQUEST))
            ->addFieldConfig(new FieldConfig(EventGroupKey::POST_PAGE))
            ->addFieldConfig(new FieldConfig(EventGroupKey::POST_DATA));
    }

    /**
     * Create the "notification" special field
     *
     * @return TransformableField
     * @since 1.11.0
     */
    protected function createNotificationSpecialField(): TransformableField {
        return (new SpecialTransformableField(SpecialFieldKey::NOTIFICATION, _wpcc('Notification'), ValueType::T_NOTIFICATION))
            ->addFieldConfig(new FieldConfig(null, CommandType::ACTION));
    }

    /**
     * Create the "request" special field
     *
     * @return TransformableField
     * @since 1.11.0
     */
    protected function createRequestSpecialField(): TransformableField {
        return (new RequestTransformableField(SpecialFieldKey::REQUEST, _wpcc('Request'), ValueType::T_REQUEST))
            ->addFieldConfig(new FieldConfig(null, CommandType::CONDITION));
    }
}