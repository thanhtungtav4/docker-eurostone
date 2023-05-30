<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 14/03/2020
 * Time: 14:21
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base;


use Exception;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Enums\FilterOptionKey;
use WPCCrawler\Objects\Filtering\Explaining\Loggers\ActionCommandLogger;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractActionProperty;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;
use WPCCrawler\Objects\Value\ValueSetter;
use WPCCrawler\Objects\ValueType\Interfaces\Outputs;
use WPCCrawler\Objects\ValueType\ValueTypeService;

abstract class AbstractActionCommand extends AbstractBaseCommand {

    /**
     * @var array|null A sequential array of dot keys. This command will be executed only for these keys if
     *      {@link shouldLimitSubjects()} returns true. If the subjects should be limited but this is null, the
     *      execution will still take place. For the execution to be constrained, this should be an array. So, if this
     *      is an empty array when {@link shouldLimitSubjects()} returns true, no execution will be done.
     */
    private $allowedSubjects = null;

    /** @var bool|null True if only the allowed subjects should be used. Otherwise, false. */
    private $onlyAllowed = false;

    /**
     * @var null|ActionCommandLogger If this {@link isVerbose()}, then this logger will log details about this
     *      command's execution.
     */
    private $logger = null;

    /** @var int[]|false|null */
    private $outputTypesCache = false;

    /**
     * Execute this action by retrieving the subject key from {@link AbstractBaseCommand::getFieldKey()} and the
     * options from {@link AbstractBaseCommand::getOptions()}
     *
     * @param string|int|null $key          Dot key for the $subjectValue. The dot key is the address of the
     *                                      $subjectValue in the data source (see {@link getDataSource()}). If subject
     *                                      value does not exist and {@link doesNeedSubjectValue()} returns true, this
     *                                      key will be null. If this command uses custom subject items, this key will
     *                                      be an integer. If the subject values are calculated by a property, this key
     *                                      might not be a valid dot key.
     * @param mixed|null      $subjectValue Value of the subject. If subject value does not exist and
     *                                      {@link doesNeedSubjectValue()} returns true, this will be null.
     * @return mixed New subject value. If this returns void, then it is considered as null is returned. In that case,
     *                                      if {@link shouldReassignNewValues()} returns true, the values will be set
     *                                      as null in the data source. If what this method returns should not be
     *                                      assigned in the data source, then {@link shouldReassignNewValues()} should
     *                                      return false to prevent unwanted nullification.
     * @since 1.11.0
     */
    abstract protected function onExecute($key, $subjectValue);

    /**
     * Get the data types of the values that this command can output
     *
     * @return int[] Constants defined in {@link ValueType}
     * @since 1.11.0
     */
    public function getOutputDataTypes(): array {
        if ($this->outputTypesCache === false) {
            $this->outputTypesCache = $this->isOutputTypeSameAsInputType()
                ? $this->getInputDataTypes()
                : ValueTypeService::getInstance()->getOutputTypes($this);
        }

        return $this->outputTypesCache ?: [];
    }

    /**
     * @return bool True if {@link getOutputDataTypes()} should return the same types as
     *              {@link AbstractBaseCommand::getInputDataTypes()} returns. If this return false, then the output
     *              types will be determined from the {@link Outputs} interfaces implemented by this class.
     * @since 1.11.0
     */
    protected function isOutputTypeSameAsInputType(): bool {
        return false;
    }

    /**
     * @return bool True if the results collected from {@link onExecute()} method should be reassigned to the data
     *              source when {@link doesNeedSubjectValue()} returns true. Otherwise, false.
     * @since 1.11.0
     */
    protected function shouldReassignNewValues(): bool {
        return true;
    }

    /**
     * @return bool True if "only matched items" checkbox should be shown in the front-end. This defaults to the return
     *              value of {@link doesNeedSubjectValue()}.
     * @since 1.11.0
     */
    protected function showUseMatchedItemsCheckbox(): bool {
        return $this->doesNeedSubjectValue();
    }

    /**
     * @return bool True if this action command should be constrained by subjects matched by a condition command
     * @since 1.11.0
     */
    public function shouldLimitSubjects(): bool {
        return (bool) $this->getOption(FilterOptionKey::CMD_OPTION_ONLY_MATCHED_ITEMS, false);
    }

    /**
     * @param array|null $allowedSubjects See {@link $allowedSubjects}
     * @return $this
     * @since 1.11.0
     */
    public function setAllowedSubjects(?array $allowedSubjects): self {
        $this->allowedSubjects = $allowedSubjects;
        return $this;
    }

    /**
     * @return array|null See {@link $allowedSubjects}
     * @since 1.11.0
     */
    public function getAllowedSubjects(): ?array {
        return $this->allowedSubjects;
    }

    /**
     * Replace an allowed subject. If the old subject does not exist, the replacement will not be done.
     *
     * @param mixed $oldSubject The old subject
     * @param mixed $newSubject The new subject that will be put in place of the old subject
     * @return self
     * @since 1.12.0
     */
    public function replaceAllowedSubject($oldSubject, $newSubject): self {
        if (!is_array($this->allowedSubjects) || !$this->allowedSubjects || $oldSubject === null || $newSubject === null) {
            return $this;
        }

        $existingKey = array_search($oldSubject, $this->allowedSubjects, true);
        if ($existingKey === false) return $this;

        $this->allowedSubjects[$existingKey] = $newSubject;
        return $this;
    }

    /**
     * @return ActionCommandLogger|null See {@link logger}
     * @since 1.11.0
     */
    public function getLogger(): ?ActionCommandLogger {
        return $this->logger;
    }

    /**
     * Execute this action
     *
     * @since 1.11.0
     */
    public function execute(): void {
        $this->setExecuted(true);
        if ($this->isVerbose()) {
            $this->logger = new ActionCommandLogger();
            $this->logger->tick();
        }

        $this->doExecute();

        $logger = $this->getLogger();
        if ($logger) $logger->tock();

        $this->executionFinished();
    }

    /**
     * Execute this action
     *
     * @since 1.11.0
     */
    protected function doExecute(): void {
        $logger = $this->getLogger();

        if (!$this->doesNeedSubjectValue()) {
            $this->onExecute(null, null);
            return;
        }

        // Check if the subjects should be considered. If the subjects must be considered but there are no subjects,
        // stop.
        if ($this->shouldConsiderAllowedSubjects() === null) {
            if ($logger) $logger
                ->addMessage(_wpcc('Command is not executed because there are no subjects that can be used.'));
            $this->setExecuted(false);
            return;
        }

        if ($logger) $logger->setOnlyAllowed($this->onlyAllowed ?: false);

        // Execute for all of the values
        $subjectValues = $this->getSubjectValues();
        if (!$subjectValues) {
            if ($logger) $logger
                ->addMessage(_wpcc('Command is not executed because there are no subjects that can be used.'));
            $this->setExecuted(false);
            return;
        }

        $field = $this->getField();
        $newSubjectValues = [];
        foreach($subjectValues as $k => $v) {
            // If there is a field, let it decide what should be the subject item. Otherwise, use the key, which is
            // probably a dot key.
            $subjectItem = $field ? $field->getSubjectItem($k, $v) : $k;
            if ($subjectItem === null) continue;

            if (!$this->isSubjectAllowed($subjectItem)) {
                if ($logger) $logger->addDeniedSubjectItem($field
                    ? $field->getSubjectItemForHumans($k, $v, $subjectItem)
                    : $v);
                continue;
            }

            if ($logger) $logger->addSubjectItem($field ? $field->getSubjectItemForHumans($k, $v, $subjectItem) : $v);

            // Collect the output. If the onExecute method returns void, then it is considered as null by PHP. So, in
            // that case, the new value will be null.
            $newValue = $this->onExecute($k, $v);
            $newSubjectValues[$k] = $newValue;
        }

        // If the new values should be reassigned, reassign them in the data source.
        $this->reassignFieldValues($newSubjectValues, $subjectValues);
    }

    protected function onTest($subject): ?array {
        // If this command does not need a subject value, it means it does not change the data source. Instead, it does
        // something different such that we cannot generalize that and retrieve the test result here. So, stop. The
        // command should override this method and provide its own test logic.
        if (!$this->doesNeedSubjectValue()) return null;

        // Execute the command for some key, which is not important here, and get the result
        $result = $this->onExecute('some-key', $subject);

        $this->setExecuted(true)->executionFinished();
        return [$result];
    }

    /**
     * Reassigns the modified field values in their data source.
     *
     * @param array $newSubjectValues This has the same structure as the return value of {@link getSubjectValues()}
     *                                method. The only difference is that the values are the return values retrieved
     *                                from {@link onExecute()}.
     * @param array $oldSubjectValues The old subject values. This has the same structure as the return value of
     *                                {@link getSubjectValues()} method.
     * @since 1.11.0
     */
    protected function reassignFieldValues(array $newSubjectValues, array $oldSubjectValues): void {
        $property = $this->getProperty();

        // If the new values should not be reassigned, stop.
        $shouldReassignNewValues   = $this->shouldReassignNewValues();
        $canPropertyAssignNewValue = $property instanceof AbstractActionProperty && $property->canAssignNewValue();
        if (!$shouldReassignNewValues && !$canPropertyAssignNewValue) return;

        // If a property calculation was performed for an action command, revert the structure of the array into a
        // structure we can use to reassign the subject values.
        if ($this->isPropertyCalculated() && $property instanceof AbstractActionProperty) {
            $newSubjectValues = $property->revertStructure($newSubjectValues) ?: [];
        }

        $logger = $this->getLogger();

        foreach($newSubjectValues as $dotKey => $newSubjectValue) {
            if ($logger && $newSubjectValue !== ($oldSubjectValues[$dotKey] ?? null)) {
                $logger->addModifiedSubjectItem($newSubjectValue);
            }

            // If the assignment should be done by the property, let it. Otherwise, go on with the regular operation.
            if ($property instanceof AbstractActionProperty && $property->canAssignNewValue()) {
                $property->assignNewValue($dotKey, $newSubjectValue, $this);

            } else {
                $this->setFieldValue($dotKey, $newSubjectValue, null);
            }
        }
    }

    /**
     * Check if this command is allowed to be executed for the given subject
     *
     * @param mixed $subject The same type as the types of the items in {@link allowedSubjects}. The subjects can be
     *                       dot keys or objects.
     * @return bool False if this command should be executed only for the allowed subjects and the key is not allowed.
     *              Otherwise, true.
     * @since 1.11.0
     */
    protected function isSubjectAllowed($subject): bool {
        return !$this->onlyAllowed || in_array($subject, $this->getAllowedSubjects() ?: [], true);
    }

    /**
     * This sets the return value to {@link onlyAllowed} as well. After calling this, whether a subject is allowed or
     * not can be found by calling {@link isSubjectAllowed()}.
     *
     * @return bool|null If the execution should not occur, returns null. Otherwise, true if the allowed subjects should
     *                   be considered and false if they should not be.
     * @since 1.11.0
     */
    protected function shouldConsiderAllowedSubjects(): ?bool {
        $allowedSubjects = $this->getAllowedSubjects();
        $onlyAllowed     = $this->shouldLimitSubjects();

        if ($onlyAllowed) {
            // If this should be executed only for the allowed subjects but there is no allowed subject, do not execute.
            // Here, we allow null values. Null means "only allowed" rule does not apply.
            if (is_array($allowedSubjects) && !$allowedSubjects) {
                return $this->onlyAllowed = null;
            }

            // If the "allowed subjects" is null, "only allowed" rule does not apply.
            if ($allowedSubjects === null) {
                $logger = $this->getLogger();
                if ($logger) $logger
                    ->addMessage(_wpcc('Only allowed subjects should have been used but all subjects are allowed 
                        since there were no subjects found by a condition.'));

                $onlyAllowed = false;
            }
        }

        return $this->onlyAllowed = $onlyAllowed;
    }

    /*
     *
     */

    public function toArray(): array {
        return array_merge(parent::toArray(), [
            'needSubjectValue' => $this->showUseMatchedItemsCheckbox(),
            'outputTypes'      => $this->getOutputDataTypes(),
        ]);
    }

    /*
     *
     */

    protected function getFieldLists() {
        $transformable = $this->getDataSource();
        return $transformable === null ? null : $transformable->getActionCommandFields();
    }

    /**
     * Set value of something in the data source
     *
     * @param string|null $dotKey   Dot key of the item in the data source. See {@link ValueSetter::set()} for more
     *                              information.
     * @param mixed       $newValue The item's new value. This will be tried to be casted to the old value's type if the
     *                              type is not the same as the old value's.
     * @param mixed       $oldValue Old value of the field. The new value will be set only if the new value is different
     *                              than the old value.
     * @uses  \WPCCrawler\Objects\Value\ValueSetter::set()
     * @since 1.11.0
     */
    protected function setFieldValue(?string $dotKey, $newValue, $oldValue): void {
        // If the old value is the same as the new value, stop.
        if ($oldValue !== null && $oldValue === $newValue) return;

        // Get the data source
        $dataSource = $this->getDataSource();

        // Prepare the new value by making sure its type is correct. If the new value could not be prepared, stop.
        if (!$this->prepareNewValue($dataSource, $newValue, $oldValue)) return;

        // Try to set the new value
        try {
            (new ValueSetter())->set($dataSource, [$dotKey => $newValue]);

        } catch (Exception $e) {
            // Notify the user in case of an exception
            Informer::addError(sprintf(
                _wpcc('New value could not be set in "%1$s" command.'),
                $this->getName()
            ))
                ->setException($e)
                ->addAsLog();
        }
    }

    /**
     * TODO: Make sure this method works as expected
     * Prepare the new value for {@link setFieldValue()}. This method checks if the new value's type is one of the valid
     * types, which can be safely used. If not, this method tries to cast the type of the value into a valid data type.
     *
     * @param Transformable|null $dataSource The data source of this command. It can be retrieved by
     *                                       {@link getDataSource()}.
     * @param mixed              $newValue   The new value. This will be casted to the correct type if it is not the
     *                                       correct type.
     * @param mixed              $oldValue   The old value. This will be used to retrieve the correct type of the new
     *                                       value.
     * @return bool True if the new value can be used. Otherwise, false.
     * @since 1.11.0
     */
    protected function prepareNewValue(?Transformable $dataSource, &$newValue, $oldValue): bool {
        // If there is no data source, notify the user and stop.
        if ($dataSource === null) {
            Informer::addError(sprintf(
                _wpcc('New value could not be set in "%1$s" command because there is no data source.'),
                $this->getName()
            ));
            return false;
        }

        $isOutputTypeValid = $this->isOutputTypeValid($newValue, $oldValue);
        if ($isOutputTypeValid === null) {
            Informer::addError(sprintf(
                _wpcc('New value could not be set in "%1$s" command because the command cannot output the value 
                    in a required data type.'),
                $this->getName()
            ));
            return false;
        }

        // If the output type is not valid, try to cast it to a correct type. If the new value is null, do not try to
        // cast it, use it directly.
        if ($isOutputTypeValid === false && $newValue !== null) {
            // Try to cast the output to a correct type. If it cannot be casted, do not set the new value.
            $preparedNewValue = $this->castNewValue($newValue, $oldValue);
            if ($preparedNewValue === null) {
                $valueTypeService = ValueTypeService::getInstance();
                Informer::addError(sprintf(
                    _wpcc('New value could not be set in "%1$s" command because it cannot be casted to the 
                        correct type. (Type of new value: "%2$s", type of old value: "%3$s")'),
                    $this->getName(),
                    implode(', ', (array) $valueTypeService->getTypeOf($newValue)),
                    $oldValue === null ? 'null' : implode(', ', (array) $valueTypeService->getTypeOf($oldValue))
                ));
                return false;
            }

            $newValue = $preparedNewValue;
        }

        return true;
    }

    /**
     * TODO: Make sure this method works as expected
     * Check if the output type is suitable for the field. This method checks the primitive types, inheritance, and
     * the ability of this class to provide a data type suitable for the field. In other words, if this action can
     * output a value with a type suitable for the field and the primitive types, including inheritance, of the new and
     * the old value are OK, this method returns true.
     *
     * @param mixed $newValue New value of the subject
     * @param mixed $oldValue Old value of the subject
     * @return bool|null True if the output type is suitable for the field of this command. False if the type is not
     *                   suitable. If any of this command's output types does not exist in the field's data types,
     *                   returns null.
     * @since 1.11.0
     */
    protected function isOutputTypeValid($newValue, $oldValue): ?bool {
        // If the new value is null, return true, because null is always allowed.
        if ($newValue === null) return true;

        // If there is no field, we cannot check the type. If that is the case, just allow all output types.
        $field = $this->getField();
        if ($field === null) return true;

        // If the field does not have one of the valid output types, return null. If the property is calculated, then
        // the values follow these steps: Field -> Property -> Command -> Property -> Field. The property takes the
        // value from the field, converts it and passes to the command. Then, the command processes it and gives it to
        // the property so that the property can convert it back to one of its input types. So, if the property is
        // calculated, the valid output types are the input types of the property.
        if ($this->isPropertyCalculated()) {
            $property = $this->getProperty();
            if (!$property || !$field->containsDataType($property->getInputDataTypes())) return null;

        } else {
            // If the property is not calculated, then the valid output types are the output types of this command,
            // since the data follows these steps: Field -> Command -> Field
            if(!$field->containsDataType($this->getOutputDataTypes())) return null;
        }

        // If the old value is null, we cannot check the types. Return false.
        if ($oldValue === null) return false;

        // If the values are not of the same type, return false.
        if (gettype($newValue) !== gettype($oldValue)) return false;

        // The types of the values are the same.

        // If the types are object, then the values must be instances of the same class or the same base class
        if (is_object($oldValue)) {
            // If the old value is an object but the new value is not, return false.
            if (!is_object($newValue)) return false;

            $cls1 = get_class($oldValue);
            $cls2 = get_class($newValue);
            $cls1Parents = class_parents($cls1);
            $cls2Parents = class_parents($cls2);

            // If the classes are not the same and they do not have a common parent, return false.
            if ($cls1 !== $cls2 && (!$cls1Parents || !$cls2Parents || !array_intersect($cls1Parents, $cls2Parents))) {
                return false;
            }
        }

        return true;
    }

    /**
     * TODO: Make sure this method works as expected
     * Cast a value (newValue) into the type of another value (oldValue) if the data type of the new value is NOT one
     * of the suitable data types of this command's transformable field. If the old value is null, then the new value
     * will be tried to be casted to one of the suitable data types.
     *
     * @param mixed      $newValue The new value. If the new value is null, it cannot be casted to anything. If it is
     *                             null, this method should not be called. Otherwise, this will return null.
     * @param mixed|null $oldValue The old value. The new value will be casted to the old value's type if its type is
     *                             one of the suitable types of this command's transformable field.
     * @return mixed|null If casting was successful, the new value casted to a suitable data type. Otherwise, null.
     * @since 1.11.0
     */
    protected function castNewValue($newValue, $oldValue) {
        // If the new value is null, we cannot cast it to anything. In that case, return null.
        if ($newValue === null) return null;

        // Get the field. We will retrieve the data types from it. If there is no field, indicate that casting is not
        // successful by returning null.
        $field = $this->getField();
        if ($field === null) return null;

        $valueTypeService = ValueTypeService::getInstance();

        // Get the new value's type. If the type could not be retrieved, indicate the casting is not successful by
        // returning null.
        $newValueType = $valueTypeService->getTypeOf($newValue);
        if ($newValueType === null) return null;

        // If the new value's type is among the data types of the field, return the new value without casting it to
        // anything. The new value is OK to use.
        if ($field->containsDataType($newValueType)) return $newValue;

        // The new value needs to be casted. First, try to cast it to the old value's type. This is because in case
        // there are multiple types defined in the field, instead of randomly selecting one of the data types, using the
        // old value's type is more robust.
        if ($oldValue !== null) {
            $oldValueType = $valueTypeService->getTypeOf($oldValue);

            // Cast it only if the old value's type is one of the suitable data types.
            if ($oldValueType !== null) {
                foreach((array) $oldValueType as $oldValueTypeItem) {
                    if (!$field->containsDataType($oldValueTypeItem)) {
                        continue;
                    }
                    
                    return $valueTypeService->castTo($this, $newValue, $oldValueTypeItem);
                }
            }
        }

        // We could not retrieve the old value's type or the old value's type was not suitable. Try to cast the new
        // value to one of the types of the field.

        // Get the suitable data types. If there is no data type, the new value cannot be made OK. In that case,
        // indicate that the casting is not successful by returning null.
        $suitableTypes = $field->getDataTypes();
        if (!$suitableTypes) return null;

        // Now, try to cast the value to one of the suitable data types until the casting is valid.
        foreach($suitableTypes as $suitableType) {
            $candidate = $valueTypeService->castTo($this, $newValue, $suitableType);
            if ($candidate !== null) return $candidate;
        }

        // The value could not be casted to a valid data type.
        return null;
    }

}