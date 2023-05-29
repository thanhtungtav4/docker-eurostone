<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/03/2020
 * Time: 07:59
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Transformation\Objects;


use WPCCrawler\Exceptions\MethodNotExistException;
use WPCCrawler\Interfaces\Arrayable;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Commands\ConditionCommands\Base\AbstractConditionCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Explaining\Loggers\Logger;
use WPCCrawler\Objects\Filtering\FilteringUtils;
use WPCCrawler\Objects\Filtering\Interfaces\HasViewDefinitions;
use WPCCrawler\Objects\Filtering\Interfaces\NeedsCommand;
use WPCCrawler\Objects\Filtering\Objects\FieldConfig;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;
use WPCCrawler\Objects\Value\ValueExtractor;
use WPCCrawler\Objects\Value\ValueExtractorOptions;

class TransformableField implements Arrayable, HasViewDefinitions, NeedsCommand {

    /**
     * @var string The dot key that will be used to extract values from the subjects. E.g. if the value of
     * "attachmentData" field has an array value, whose each item has transformable values in "title" and "alt" keys,
     * then two {@link TransformableField}s can be created with the following dot keys:
     *
     * <ul>
     *   <li>attachmentData.title</li>
     *   <li>attachmentData.alt</li>
     * </ul>
     *
     * If a key points to an associative array and no key is defined for the array, all keys of the array are
     * transformable. For example, if <i>["data" => "Post Data"]</i> is given and <i>getData()</i> method returns
     * <i>[ ['name' => 'A', 'value' => 'B'] ]</i>, then the values of both <i>'name'</i> and <i>'value'</i> keys are
     * transformable. If <i>["data.name" => "Post Data"]</i> is given, then only the value of <i>'name'</i> key is
     * transformable. Objects are transformable as well. In case of objects, set the field names that have setter and
     * getter methods. E.g. if <i>"attachmentData"</i> stores an array of MediaFile instances, and each media file has a
     * mediaTitle field and <i>setMediaTitle</i> and <i>getMediaTitle</i> methods, then
     * <i>"attachmentData.mediaTitle"</i> key indicates this.
     *
     * NOTE: Transform more wisely. For example, instead of transforming listNumbers, listTitles, etc., just transform
     * the final post template. By this way, the number of chars to be transformed will be less, hence, less money will
     * be spent for the transformation service.
     *
     * NOTE: The fields must have mutator and accessor methods. In other words, if there is "title", then there must be
     * <i>setTitle($title)</i> and <i>getTitle()</i> methods so that "title" can be transformed. The methods must start
     * with "set" and "get", respectively, and they must be named in camelCase.
     */
    private $dotKey;

    /** @var string Human-readable name of this transformable field */
    private $title;

    /**
     * @var string|null Human-friendly description of this transformable field. This will be shown in the UI for the
     *      users to understand what this field is for.
     */
    private $description;

    /**
     * @var int[] Types of this field. One or many of the constants defined in {@link ValueType}
     */
    private $dataTypes;

    /**
     * @var FieldConfig[]|null Defines event group and command type combinations this field is available for. If this
     *      is null, this field is available for any combination.
     */
    private $fieldConfigs = null;

    /** @var null|string A string that will be added after the dot key */
    private $dotKeySuffix = null;

    /*
     *
     */

    /** @var ViewDefinitionList|null Views that should be rendered for this field for filter settings */
    private $filterViews = null;

    /** @var AbstractBaseCommand|null */
    private $command = null;

    /**
     * @var Callable|null A callback that modifies extractor options returned by {@link createExtractorOptions()}.
     *      Structure: <b>func({@link ValueExtractorOptions} $options) {}</b>
     */
    private $modifyExtractorOptionsCallback = null;

    /**
     * @param string    $dotKey    See {@link $dotKey}
     * @param string    $title     See {@link $title}
     * @param int|int[] $dataTypes See {@link $dataTypes}
     * @since 1.11.0
     */
    public function __construct(string $dotKey, string $title, $dataTypes) {
        $this->dotKey   = $dotKey;
        $this->title    = $title;
        $this->dataTypes = is_array($dataTypes) ? $dataTypes : [$dataTypes];
    }

    /**
     * @return string
     * @since 1.11.0
     */
    public function getDotKey(): string {
        return $this->dotKey . ($this->dotKeySuffix ?: '');
    }

    /**
     * @param string|null $dotKeySuffix See {@link $dotKeySuffix}
     * @since 1.11.0
     * @noinspection PhpUnused
     */
    public function setDotKeySuffix(?string $dotKeySuffix): void {
        $this->dotKeySuffix = $dotKeySuffix;
    }

    /**
     * @return string
     * @since 1.11.0
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @return string|null See {@link description}
     * @since 1.11.0
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @param string|null $description See {@link description}
     * @return TransformableField
     * @since 1.11.0
     */
    public function setDescription(?string $description): TransformableField {
        $this->description = $description;
        return $this;
    }

    /**
     * @return FieldConfig[]|null See {@link fieldConfigs}
     * @since 1.11.0
     */
    public function getFieldConfigs(): ?array {
        return $this->fieldConfigs;
    }

    /**
     * @param FieldConfig[]|null $fieldConfigs See {@link fieldConfigs}
     * @return TransformableField
     * @since 1.11.0
     */
    public function setFieldConfigs(?array $fieldConfigs): self {
        $this->fieldConfigs = $fieldConfigs;
        return $this;
    }

    /**
     * See {@link fieldConfigs}
     *
     * @param FieldConfig $config An item that will be added to {@link fieldConfigs}
     * @return TransformableField
     * @since 1.11.0
     */
    public function addFieldConfig(FieldConfig $config): self {
        if (!is_array($this->fieldConfigs)) {
            $this->fieldConfigs = [];
        }

        $this->fieldConfigs[] = $config;
        return $this;
    }

    /**
     * @return int[] See {@link dataTypes}
     * @since 1.11.0
     */
    public function getDataTypes(): array {
        return $this->dataTypes;
    }

    /**
     * Check if data type(s) exist for this field
     *
     * @param int|int[] $type See {@link FilteringUtils::hasDataType()}
     * @return bool Returns what {@link FilteringUtils::hasDataType()} returns
     * @uses FilteringUtils::hasDataType()
     * @since 1.11.0
     */
    public function hasDataType($type): bool {
        return FilteringUtils::hasDataType($type, $this->getDataTypes());
    }

    /**
     * @param int|int[] $type See {@link FilteringUtils::containsDataType()}
     * @return bool Returns what {@link FilteringUtils::containsDataType()} returns
     * @uses  FilteringUtils::containsDataType()
     * @since 1.11.0
     */
    public function containsDataType($type): bool {
        return FilteringUtils::containsDataType($type, $this->getDataTypes());
    }

    /**
     * @param ViewDefinitionList|null $views See {@link $filterViews}
     * @return TransformableField
     * @since 1.11.0
     */
    public function setViewDefinitions(?ViewDefinitionList $views): self {
        $this->filterViews = $views;
        return $this;
    }

    /**
     * @return ViewDefinitionList|null See {@link $filterViews}
     * @since 1.11.0
     */
    public function getViewDefinitions(): ?ViewDefinitionList {
        return $this->filterViews;
    }

    /**
     * Get the value that will be added to {@link AbstractConditionCommand::suitableSubjectItems} when an item meets the
     * condition
     *
     * @param string|int $key   Key of the item that meets the condition
     * @param mixed      $value Value of the item that meets the condition
     * @return mixed|null The value. If null, this will not be added as a suitable subject item.
     * @since 1.11.0
     */
    public function getSubjectItem($key, $value) {
        return $key;
    }

    /**
     * Get the subject item that will be shown to humans so that they can understand the subject. For example, this
     * value might be used by {@link Logger}.
     *
     * @param string|int $key         The same as $key required by {@link getSubjectItem()}
     * @param mixed      $value       The same as $value required by {@link getSubjectItem()}
     * @param mixed|null $subjectItem Subject item retrieved by {@link getSubjectItem()}
     * @return string|null
     * @since 1.11.0
     * @noinspection PhpUnusedParameterInspection
     */
    public function getSubjectItemForHumans($key, $value, $subjectItem): ?string {
        // If the command has a property, then the $value is the value the property returns. We do not want that. We
        // want the subject value. To make sure we get the actual subject value, we can retrieve the extracted subject
        // values from the command and retrieve the actual subject value by using the given $key.
        $cmd = $this->getCommand();
        if (!$cmd) return null;

        $lastSubjectValues = $cmd->getLastExtractedSubjectValues();
        if (!$lastSubjectValues) return null;

        // If the key exists in the last subject values array, return it.
        if (isset($lastSubjectValues[$key])) return $this->getSubjectItemAsString($lastSubjectValues[$key]);

        // The key does not exist in the subject values array. Try property values array.
        $lastPropertyValues = $cmd->getLastExtractedPropertyValues();
        if (!$lastPropertyValues) return null;

        // If the key exists in the property values array, return it. It probably exists, but we want to be on the safe
        // side.
        return isset($lastPropertyValues[$key]) ? $this->getSubjectItemAsString($lastPropertyValues[$key]) : null;
    }

    /**
     * @param Callable|null $modifyExtractorOptionsCallback See {@link $modifyExtractorOptionsCallback}
     * @return TransformableField
     * @since 1.11.0
     */
    public function setModifyExtractorOptionsCallback(?callable $modifyExtractorOptionsCallback): self {
        $this->modifyExtractorOptionsCallback = $modifyExtractorOptionsCallback;
        return $this;
    }

    /**
     * Extract values of this field from the given data source
     *
     * @param Transformable|null $dataSource The data source from which the values will be extracted
     * @return array|null If the subject value could be extracted, the subject value as an array. Otherwise, null. The
     *                    array value is returned by {@link ValueExtractor::fillAndFlatten()} method. The extraction is
     *                    done by using this field's (see {@link getDotKey()}).
     * @since 1.11.0
     */
    public function extractSubjectValues(?Transformable $dataSource): ?array {
        return $this->onExtractSubjectValues($dataSource);
    }

    /*
     *
     */

    /**
     * Parse subject value to string. This is mainly used to show the subject to the user, in the test results.
     *
     * @param mixed $subject The subject value
     * @return string|null The string representation of the subject value
     * @since 1.11.0
     */
    protected function getSubjectItemAsString($subject): ?string {
        return (string) $subject;
    }

    /**
     * See {@link extractSubjectValues()}
     *
     * @param Transformable|null $dataSource
     * @return array|null
     * @since 1.11.0
     */
    protected function onExtractSubjectValues(?Transformable $dataSource): ?array {
        if (!$dataSource) return null;

        try {
            $options = $this->createExtractorOptions();

            // If there is a modifier for extractor options, call it.
            $cb = $this->modifyExtractorOptionsCallback;
            if ($cb !== null) $cb($options);

            return (new ValueExtractor())->fillAndFlatten(
                $dataSource,
                [$this->getDotKey() => ''],
                ValueExtractor::DEFAULT_SEPARATOR,
                $options
            );

        } catch (MethodNotExistException $e) {
            Informer::addError($e->getMessage())->setException($e)->addAsLog();
            return null;
        }
    }

    /**
     * @return ValueExtractorOptions Options that will be used by {@link extractSubjectValues()}
     * @since 1.11.0
     */
    protected function createExtractorOptions(): ValueExtractorOptions {
        return (new ValueExtractorOptions())
            ->setAllowNull(true)
            ->setAllowNumeric(true)
            ->setAllowEmptyString(true);
    }

    /*
     * INTERFACE METHODS
     */

    public function toArray(): array {
        $fieldConfigs = $this->getFieldConfigs();
        if ($fieldConfigs) $fieldConfigs = array_map(function($item) { return $item->toArray(); }, $fieldConfigs);

        $viewList = $this->getViewDefinitions();
        return [
            'title'        => $this->getTitle(),
            'description'  => $this->getDescription(),
            'key'          => $this->getDotKey(),
            'dataTypes'    => $this->getDataTypes(),
            'fieldConfigs' => $fieldConfigs,
            'filterViews'  => $viewList ? $viewList->toArray() : null
        ];
    }

    public function setCommand(?AbstractBaseCommand $command): void {
        $this->command = $command;
    }

    public function getCommand(): ?AbstractBaseCommand {
        return $this->command;
    }

}