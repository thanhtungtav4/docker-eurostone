<?php /** @noinspection PhpUndefinedClassInspection */

/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 14/03/2020
 * Time: 14:18
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\Base;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use WPCCrawler\Exceptions\PropertyNotExistException;
use WPCCrawler\Interfaces\Arrayable;
use WPCCrawler\Objects\Crawling\Data\PostData;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionFactory;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\FilterOptionKey;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Explaining\Loggers\Logger;
use WPCCrawler\Objects\Filtering\FilterDependencyProvider\FilterDependencyProvider;
use WPCCrawler\Objects\Filtering\FilteringUtils;
use WPCCrawler\Objects\Filtering\Interfaces\HasTestViewDefinitions;
use WPCCrawler\Objects\Filtering\Interfaces\HasViewDefinitions;
use WPCCrawler\Objects\Filtering\Interfaces\NeedsProvider;
use WPCCrawler\Objects\Filtering\Interfaces\Verbosable;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractProperty;
use WPCCrawler\Objects\Filtering\Property\Objects\CalculationResult;
use WPCCrawler\Objects\Filtering\Property\PropertyService;
use WPCCrawler\Objects\Filtering\SpecialFieldService;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;
use WPCCrawler\Objects\Transformation\Objects\TransformableField;
use WPCCrawler\Objects\Transformation\Objects\TransformableFieldList;
use WPCCrawler\Objects\Value\ValueExtractor;
use WPCCrawler\PostDetail\Base\BasePostDetailFactory;
use WPCCrawler\Utils;

abstract class AbstractBaseCommand implements Arrayable, HasViewDefinitions, HasTestViewDefinitions, NeedsProvider,
    Verbosable {

    /**
     * @var Transformable|null Data source from which the subject's value will be retrieved by using {@link $fieldKey} and
     *      {@link $propertyKey}
     */
    private $dataSource;

    /**
     * @var string|null If the data source is a {@link BasePostDetailFactory} instead of a {@link PostData}, then this
     *                  identifier must be passed by retrieving it via {@link BasePostDetailFactory::getIdentifier()}
     */
    private $dataSourceIdentifier;

    /**
     * @var string|null Dot key that will be used to extract the value of the subject from {@link $dataSource}. It must
     *      be the dot key of one of the {@link TransformableField}s returned by
     *      {@link Transformable::getInteractableFields()} (or by another method of {@link Transformable}) of the
     *      {@link $dataSource}
     */
    private $fieldKey;

    /** @var array|null Options that will be used by this command. Each command can have different options specific to it. */
    private $options;

    /** @var string|null One of the constants defined in {@link PropertyKey} */
    private $propertyKey;

    /** @var null|false|TransformableField The field having the {@link $fieldKey} */
    private $field = false;

    /** @var null|false|AbstractProperty The property having the {@link $propertyKey} */
    private $property = false;

    /** @var FilterDependencyProvider|null */
    private $provider;

    /**
     * @var bool True if extra logging should be done while executing the command. This is intended to be used when
     *      testing the settings such that extra information about the command is needed to debug it.
     */
    private $verbose = false;

    /*
     *
     */

    /** @var ViewDefinitionList|false|null Views that should be rendered for this command */
    private $views = false;

    /** @var ViewDefinitionList|false|null Test views that should be rendered for this command */
    private $testViews = false;

    /** @var array|null Stores the result of the last called {@link extractSubjectValues()} */
    private $lastExtractedSubjectValues = null;

    /** @var array|null Stores the result of the last called {@link getPropertyValueFromExtractedSubjectValue()} */
    private $lastExtractedPropertyValues = null;

    /** @var bool If this command was executed at least once before, then this is true. Otherwise, this is false. */
    private $executed = false;

    /** @var bool True if a property calculation has been done. Otherwise, false. */
    private $propertyCalculated = false;

    /**
     * @param Transformable|null $dataSource           See {@link $dataSource}
     * @param string|null        $fieldKey             See {@link $fieldKey}
     * @param array|null         $options              See {@link $options}
     * @param string|null        $propertyKey          See {@link $propertyKey}
     * @param string|null        $dataSourceIdentifier See {@link $dataSourceIdentifier}
     * @since 1.11.0
     */
    final public function __construct(?Transformable $dataSource, ?string $fieldKey, ?array $options = null,
                                      ?string $propertyKey = null, ?string $dataSourceIdentifier = null) {
        $this
            ->setDataSource($dataSource)
            ->setDataSourceIdentifier($dataSourceIdentifier)
            ->setFieldKey($fieldKey)
            ->setOptions($options)
            ->setPropertyKey($propertyKey);
    }

    /**
     * @return string Unique identifier for this command
     * @since 1.11.0
     */
    abstract public function getKey(): string;

    /**
     * @return string Human-friendly name of this command. This value will be shown to the user where necessary.
     * @since 1.11.0
     */
    abstract public function getName(): string;

    /**
     * @return string|null Human-friendly description of this command. This will be shown in the UI for the users to
     *                     understand what this command does.
     * @since 1.11.0
     */
    public function getDescription(): ?string {
        return null;
    }

    /**
     * Get the data types of the values that can be input to this command
     *
     * @return int[] Constants defined in {@link ValueType}
     * @since 1.11.0
     */
    abstract public function getInputDataTypes(): array;

    /**
     * @return TransformableFieldList[]|TransformableFieldList|null Field lists that can contain the fields of this
     *                                                              command
     * @since 1.11.0
     */
    abstract protected function getFieldLists();

    /**
     * @return Logger|null If this command {@link isVerbose()}, then this will return a logger that
     *                                    stores details about this command's execution. Otherwise, returns null.
     * @since 1.11.0
     * @noinspection PhpMissingReturnTypeInspection
     */
    abstract public function getLogger();

    /**
     * @return bool True if this command needs a subject value to be executed. Otherwise, false.
     * @since 1.11.0
     */
    public function doesNeedSubjectValue(): bool {
        return true;
    }

    /**
     * @return bool True if this command is testable in the UI. Otherwise, false.
     * @since 1.11.0
     */
    protected function isTestable(): bool {
        return true;
    }

    /**
     * Perform a test for the subject, and return an array of results.
     *
     * @param mixed|null $subject Test subject
     * @return string[]|null Test results
     * @since 1.11.0
     */
    abstract protected function onTest($subject): ?array;

    /**
     * Test this command by using the test subject(s) that is already provided as an option to this command
     *
     * @return string[]|null
     * @since 1.11.0
     */
    public function test(): ?array {
        return $this->isTestable()
            ? $this->onTest($this->getTestSubject())
            : null;
    }

    /**
     * @return Transformable|null See {@link $dataSource}
     * @since 1.11.0
     */
    public function getDataSource(): ?Transformable {
        return $this->dataSource;
    }

    /**
     * @param Transformable|null $dataSource See {@link $dataSource}
     * @return $this
     * @since 1.11.0
     */
    public function setDataSource(?Transformable $dataSource): self {
        $this->dataSource = $dataSource;
        return $this;
    }

    /**
     * @param string|null $dataSourceIdentifier See {@link $dataSourceIdentifier}
     * @return $this
     * @since 1.11.0
     */
    public function setDataSourceIdentifier(?string $dataSourceIdentifier): self {
        $this->dataSourceIdentifier = $dataSourceIdentifier;
        return $this;
    }

    /**
     * @return string|null See {@link $fieldKey}
     * @since 1.11.0
     */
    public function getFieldKey(): ?string {
        return $this->fieldKey;
    }

    /**
     * @param string|null $fieldKey See {@link $fieldKey}
     * @return $this
     * @since 1.11.0
     */
    public function setFieldKey(?string $fieldKey): self {
        $this->fieldKey = $fieldKey;
        $this->field = false;
        return $this;
    }

    /**
     * @return array|null
     * @since 1.11.0
     */
    protected function getOptions(): ?array {
        return $this->options;
    }

    /**
     * @param array|null $options See {@link $options}
     * @return $this
     * @since 1.11.0
     */
    public function setOptions(?array $options): self {
        $this->options = $options;
        return $this;
    }

    /**
     * Get value from {@link options} of this command.
     *
     * @param string|null $key     The option key. It can be dot notation.
     * @param mixed       $default Default value that will be returned if the value does not exist
     * @return mixed|null If the key exists in {@link options}, its value. Otherwise, null.
     * @since 1.11.0
     */
    public function getOption(?string $key, $default = null) {
        return $key === null ? $default : Utils::array_get($this->getOptions(), $key, $default);
    }

    /**
     * Get value of a checkbox option from {@link options}
     *
     * @param string $key The option's key. It can be dot notation.
     * @return bool True if the checkbox option is checked. Otherwise, false.
     * @since 1.11.0
     */
    public function getCheckboxOption(string $key): bool {
        $value = $this->getOption($key);

        // If the value is "0", 0, "", false, null, etc., i.e. evaluates to false, we return false. For other values, we
        // return true. This is because sometimes the value of the unchecked option is provided as false, sometimes as
        // '0', sometimes as '', and so on. We expect the provided value of a checked checkbox to evaluate to true.
        return (bool) $value;
    }

    /**
     * Get value of an option as a string
     *
     * @param string $key The key of the option
     * @return string|null If the value exists, and it can be cast to a string, the value is returned as a string.
     *                     Otherwise, null is returned.
     * @since 1.12.0
     */
    public function getStringOption(string $key): ?string {
        $value = $this->getOption($key);
        return is_scalar($value)
            ? (string) $value
            : null;
    }

    /**
     * Get value of an option as an array
     *
     * @param string $key The key of the option
     * @return array|null If the value exists, and it is an array, the value is returned. Otherwise, null is returned.
     * @since 1.12.0
     */
    public function getArrayOption(string $key): ?array {
        $value = $this->getOption($key);
        return is_array($value)
            ? $value
            : null;
    }

    /**
     * @param string|null $propertyKey See {@link $propertyKey}
     * @return $this
     * @since 1.11.0
     */
    public function setPropertyKey(?string $propertyKey): self {
        $this->propertyKey = $propertyKey;
        return $this;
    }

    /**
     * @return string|null See {@link propertyKey}
     * @since 1.11.0
     */
    public function getPropertyKey(): ?string {
        return $this->propertyKey;
    }

    /**
     * @return AbstractProperty|null See {@link property}
     * @since 1.11.0
     */
    public function getProperty(): ?AbstractProperty {
        if ($this->property === false) {
            try {
                $this->property = PropertyService::getInstance()->getProperty($this->propertyKey, true);

            } catch (PropertyNotExistException $e) {
                $this->property = null;
                Informer::addInfo($e->getMessage())->setException($e)->addAsLog();
            }
        }

        return $this->property;
    }

    /**
     * @return ViewDefinitionList|null See {@link views}
     * @since 1.11.0
     */
    public function getViewDefinitions(): ?ViewDefinitionList {
        if ($this->views === false) {
            $this->views = $this->createViews();
        }

        return $this->views;
    }

    /**
     * @return ViewDefinitionList|null See {@link testViews}
     * @since 1.11.0
     */
    public function getTestViewDefinitions(): ?ViewDefinitionList {
        if (!$this->isTestable()) return null;

        if ($this->testViews === false) {
            $this->testViews = $this->createTestViews();
        }

        return $this->testViews;
    }

    /**
     * @return mixed|null The test subject retrieved from the options
     * @since 1.11.0
     */
    public function getTestSubject() {
        $testOptionNames = [InputName::TEST_TEXT, InputName::TEST_NUMBER, InputName::TEST_DATE];

        $options = $this->getOptions();
        foreach($testOptionNames as $optionName) {
            if (isset($options[$optionName])) {
                return $options[$optionName];
            }
        }

        return null;
    }

    /**
     * @return string|null Extra information that will be shown in the message part of the command's test results
     * @since 1.11.0
     */
    public function getTestMessage(): ?string {
        return null;
    }

    /**
     * @return array|null See {@link lastExtractedSubjectValues}
     * @since 1.11.0
     */
    public function getLastExtractedSubjectValues(): ?array {
        return $this->lastExtractedSubjectValues;
    }

    /**
     * @return array|null See {@link lastExtractedPropertyValues}
     * @since 1.11.0
     */
    public function getLastExtractedPropertyValues(): ?array {
        return $this->lastExtractedPropertyValues;
    }

    /**
     * @return bool See {@link propertyCalculated}
     * @since 1.11.0
     */
    public function isPropertyCalculated(): bool {
        return $this->propertyCalculated;
    }

    /**
     * @return bool See {@link executed}
     * @since 1.11.0
     */
    public function isExecuted(): bool {
        return $this->executed;
    }

    /**
     * Invalidate things to free up some memory
     *
     * @since 1.11.0
     */
    public function executionFinished(): void {
        // Invalidate the stored subject values.
        $this->invalidateExtractedSubjectValues();

        // Invalidate the dependencies of the things since this command has finished its job
        $provider = $this->getProvider();
        if ($provider) {
            if (is_object($this->field)) $provider->invalidateDependencies($this->field);
        }
    }

    /*
     *
     */

    public function toArray(): array {
        $viewList = $this->getViewDefinitions();
        $testViewList = $this->getTestViewDefinitions();

        return [
            'key'        => $this->getKey(),
            'name'       => $this->getName(),
            'description'=> $this->getDescription(),
            'inputTypes' => $this->getInputDataTypes(),
            'views'      => $viewList !== null     ? $viewList->toArray()     : null,
            'testViews'  => $testViewList !== null ? $testViewList->toArray() : null,
            'testable'   => $this->isTestable(),
        ];
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * @param bool $executed See {@link executed}
     * @return $this
     * @since 1.11.0
     */
    protected function setExecuted(bool $executed): self {
        $this->executed = $executed;
        return $this;
    }

    /**
     * @return ViewDefinitionList|null See {@link views}
     * @since 1.11.0
     */
    protected function createViews(): ?ViewDefinitionList {
        return null;
    }

    /**
     * @return ViewDefinitionList|null See {@link testViews}
     * @since 1.11.0
     */
    protected function createTestViews(): ?ViewDefinitionList {
        $inputTypes = $this->getInputDataTypes();
        if (!$inputTypes) return null;

        $viewDefinitionFactory = ViewDefinitionFactory::getInstance();

        $list = new ViewDefinitionList();
        if (in_array(ValueType::T_STRING, $inputTypes)) {
            $list->add($viewDefinitionFactory->createTestTextInput());

        } else if (Utils::arrayIntersect([ValueType::T_NUMERIC, ValueType::T_FLOAT, ValueType::T_INTEGER], $inputTypes)) {
            $list->add($viewDefinitionFactory->createTestNumberInput());

        } else if (Utils::arrayIntersect([ValueType::T_DATE, ValueType::T_DATE_STR], $inputTypes)) {
            $list->add($viewDefinitionFactory->createTestDateInput());
        }

        return $list;
    }

    /**
     * Get the value that should be used by this command. The value is extracted from {@link $dataSource}. If there is
     * a {@link $propertyKey}, then the defined property will be calculated using the extracted value and it will be
     * returned instead of the value.
     *
     * @return array|null An associative array where keys are the dot keys for the values. If the values could not be
     *                    retrieved, returns null.
     * @since 1.11.0
     */
    protected function getSubjectValues(): ?array {
        $this->invalidateExtractedSubjectValues();

        // Extract the subject value if this command needs it.
        $extracted = $this->doesNeedSubjectValue() ? $this->extractSubjectValues() : null;
        if ($extracted === null) return null;

        // Get the property values from the extracted subject value by calculating their property values if there is a
        // property.
        $this->lastExtractedPropertyValues = $this->getPropertyValueFromExtractedSubjectValue($extracted);
        return $this->lastExtractedPropertyValues;
    }

    /**
     * @return array|null If the subject value could be extracted, the subject value as an array. Otherwise, null. The
     *                    array value is returned by {@link ValueExtractor::fillAndFlatten()} method. The extraction is
     *                    done by using the field's (see {@link getField()}) dot key (see
     *                    {@link TransformableField::getDotKey()}). The result can also be reached later via
     *                    {@link getLastExtractedSubjectValues()}.
     * @since 1.11.0
     */
    protected function extractSubjectValues(): ?array {
        $this->invalidateExtractedSubjectValues();

        // Get the field. We need the dot key to be able to extract the subject value.
        $field = $this->getField();
        if ($field === null) return null;

        $provider = $this->getProvider();
        if ($provider) $provider->injectDependencies($field);

        $this->lastExtractedSubjectValues = $field->extractSubjectValues($this->dataSource);
        return $this->lastExtractedSubjectValues;
    }

    /**
     * Get the field that corresponds to the return value of {@link getFieldKey()}.
     *
     * @return null|TransformableField If a field is found, it will be returned. Otherwise, null will be returned.
     * @since 1.11.0
     */
    protected function getField() {
        if ($this->field === false) {
            // Prepare the subject key
            $fieldKey = $this->getFieldKey();
            if (!$fieldKey) return null;

            // If this is a special field, directly find it.
            $specialFieldKey = $this->prepareFieldKey($fieldKey, SpecialFieldService::SPECIAL_FIELD_IDENTIFIER);
            if ($specialFieldKey !== null) {
                $this->field = SpecialFieldService::getInstance()->getSpecialFields()->getByKey($specialFieldKey);
                return $this->field;
            }

            // If there is a data source identifier existing in the subject key, remove it.
            if ($this->dataSourceIdentifier) {
                $fieldKey = $this->prepareFieldKey($fieldKey, $this->dataSourceIdentifier);

                if ($fieldKey === null) {
                    // If the given key does not start with the data source identifier, return null. It must start with
                    // the given identifier.
                    $this->field = null;
                    return $this->field;
                }
            }

            // Get the field. We need it to understand if its data type can be handled by the property.
            $this->field = $this->findField($fieldKey);
        }

        return $this->field;
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * Retrieve the property value by using the extracted subject value. This method calculates the property values if
     * there is a {@link $propertyKey}.
     *
     * @param array $extracted Subject value extracted by {@link extractSubjectValues()}
     * @return null|array Calculated property values as an associative array. If the values could not be retrieved,
     *                    returns null.
     * @since 1.11.0
     */
    private function getPropertyValueFromExtractedSubjectValue($extracted): ?array {
        if (!is_array($extracted)) return null;

        // If there is no property, return the extracted value directly.
        if (!$this->propertyKey) return $extracted;

        // Get the field. We need it to understand if its data type can be handled by the property.
        $field = $this->getField();
        if ($field === null) return null;

        // There is a property key. Get the property.
        $property = $this->getProperty();
        if ($property === null) return null;

        // Make sure the data type of the field can be handled by the property
        if (!$field->containsDataType($property->getInputDataTypes())) return $extracted;

        // Calculate the new values
        $newExtracted = [];
        if (!$property->doesRequireRawExtractedValues()) {
            // If there is no item in the extracted value, return the extracted value directly.
            if (empty($extracted)) return $extracted;

            // Calculate the new value for all of the values
            foreach($extracted as $k => $v) {
                $calculationResults = $property->calculate($k, $v, $this);
                if ($calculationResults === null) continue;

                foreach($calculationResults as $calculationResult) {
                    if (!($calculationResult instanceof CalculationResult)) continue;
                    $newExtracted[$calculationResult->getKey()] = $calculationResult->getValue();
                }
            }

        } else {
            // Calculate the new value for the array of extracted values
            $calculationResults = $property->calculate('extracted', $extracted, $this);
            if ($calculationResults !== null) {
                foreach($calculationResults as $calculationResult) {
                    if (!($calculationResult instanceof CalculationResult)) continue;
                    $newExtracted[$calculationResult->getKey()] = $calculationResult->getValue();
                }
            }
        }

        $this->propertyCalculated = true;
        return $newExtracted;
    }

    /**
     * @param string|null $fieldKey Key of the field that does NOT contain an identifier prefix
     * @return TransformableField|null If found, a {@link TransformableField} that has the same field key. Otherwise,
     *                                 null.
     * @since 1.11.0
     */
    private function findField(?string $fieldKey): ?TransformableField {
        if ($fieldKey === null) return null;

        // Try to find the field among interactable fields
        $field = $this->dataSource
            ? $this->dataSource->getInteractableFields()->getByKey($fieldKey)
            : null;

        // If the field is not found, search in other field lists
        if ($field === null) {
            // Get the field lists
            $fieldLists = $this->getFieldLists() ?: [];
            if (!is_array($fieldLists)) $fieldLists = [$fieldLists];

            // Search the field until it is found in one of the field lists
            foreach($fieldLists as $fieldList) {
                if ($fieldList === null) continue; // @phpstan-ignore-line

                /** @var TransformableFieldList $fieldList */
                $field = $fieldList->getByKey($fieldKey);
                if ($field !== null) break;
            }
        }

        return $field;
    }

    /**
     * Remove an identifier from a field key.
     *
     * @param string|null $fieldKey   Field key having an identifier prefix
     * @param string|null $identifier Identifier
     * @return string|null If the field key starts with the identifier, field key without the identifier is returned.
     *                     Otherwise, null.
     * @since 1.11.0
     */
    private function prepareFieldKey(?string $fieldKey, ?string $identifier): ?string {
        if ($fieldKey === null || $identifier === null) return null;

        // If the field key does not start with the identifier, return null.
        if(!Str::startsWith($fieldKey, $identifier)) return null;

        $prepared = substr($fieldKey, strlen($identifier) + 1); // +1 is for "." char
        return $prepared === false ? null : $prepared;
    }

    /**
     * Invalidates the references to {@link lastExtractedSubjectValues}
     *
     * @since 1.11.0
     */
    private function invalidateExtractedSubjectValues(): void {
        $this->lastExtractedSubjectValues  = null;
        $this->lastExtractedPropertyValues = null;
        $this->propertyCalculated = false;

        // If there is a property, it might be storing references to some objects. To free some memory, invalidate the
        // property as well. When the property is needed, a new property instance will be created.
        $this->property = false;
    }

    /*
     * INTERFACE METHODS
     */

    /**
     * @param FilterDependencyProvider|null $provider See {@link provider}
     * @since 1.11.0
     */
    public function setProvider(?FilterDependencyProvider $provider): void {
        $this->provider = $provider;
    }

    /**
     * @return FilterDependencyProvider|null See {@link provider}
     * @since 1.11.0
     */
    public function getProvider(): ?FilterDependencyProvider {
        return $this->provider;
    }

    /**
     * @return bool See {@link verbose}
     * @since 1.11.0
     */
    public function isVerbose(): bool {
        return $this->verbose;
    }

    /**
     * @param bool $verbose See {@link verbose}
     * @return AbstractBaseCommand
     * @since 1.11.0
     */
    public function setVerbose(bool $verbose): self {
        $this->verbose = $verbose;
        return $this;
    }

    /*
     * STATIC METHODS
     */

    /**
     * @return static
     * @since 1.11.0
     */
    public static function newInstance(): self {
        return new static(null, null);
    }

    /**
     * @param array|null $options Options that will be used to create an instance of this class. This must have
     *                            'subject' and 'property' keys. The remaining items in the array will be defined as
     *                            the value of {@link AbstractBaseCommand::$options} value after removing 'command'
     *                            and 'type' keys.
     * @param bool|null  $verbose See {@link FilteringUtils::getVerbose()}
     * @return AbstractBaseCommand|null
     * @since 1.11.0
     */
    public static function fromOptions(?array $options, ?bool $verbose = null): ?self {
        if ($options === null) return null;

        $fieldKey    = Arr::pull($options, FilterOptionKey::SUBJECT);
        $propertyKey = Arr::pull($options, FilterOptionKey::PROPERTY);
        if (!is_string($fieldKey))                             $fieldKey    = null;
        if (!is_string($propertyKey) || $propertyKey === "-1") $propertyKey = null;

        unset($options[FilterOptionKey::COMMAND], $options[FilterOptionKey::TYPE]);

        $cmd = new static(null, $fieldKey, $options, $propertyKey);
        $cmd->setVerbose(FilteringUtils::getVerbose($verbose));

        return $cmd;
    }
}