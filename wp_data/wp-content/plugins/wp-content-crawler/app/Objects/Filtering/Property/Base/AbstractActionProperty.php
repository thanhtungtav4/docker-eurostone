<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 07/07/2020
 * Time: 20:58
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Base;


use Illuminate\Support\Str;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractActionCommand;

/**
 * Properties that should be displayed for action commands should extend this class. When a property extends this class,
 * it means the property is suitable for both condition and action commands.
 *
 * @since 1.11.0
 */
abstract class AbstractActionProperty extends AbstractProperty {

    /** @var string Used as a separator when creating a unique key for a calculated subject value. */
    const KEY_SEPARATOR = '<>p>';

    /**
     * A property extracts values from a subject and passes it to a command. Sometimes, a subject is not changed
     * directly. Instead, its property needs to be changed. For example, if there is an object whose one specific
     * property needs to be changed, the object does not need to be changed. The property can directly update the value
     * of the object's property. This method declares if this property is capable of doing this, or it wants to do
     * this.
     *
     * @return bool `true` if this property can assign the new value that is retrieved from an
     *              {@link AbstractActionCommand}. When this is `true`, an {@link AbstractActionCommand} calls this
     *              property's {@link assignNewValue()} method after the values are changed.
     * @since 1.12.0
     */
    public function canAssignNewValue(): bool {
        return false;
    }

    /**
     * Assigns the new value to the property of the subject. See {@link canAssignNewValue()}.
     *
     * @param string                $key      The key of the new value
     * @param mixed                 $newValue The new value
     * @param AbstractActionCommand $cmd      The command that called this method
     * @since 1.12.0
     */
    public function assignNewValue(string $key, $newValue, AbstractActionCommand $cmd): void {
        $this->onAssignNewValue($key, $newValue, $cmd);
    }

    /**
     * Revert the structure of an array that was previously restructured by this property. See {@link revertStructure()}
     * for more information.
     *
     * @param array $newSubjectValues See {@link revertStructure()}
     * @return array|null See {@link revertStructure()}
     * @since 1.11.0
     */
    protected function onRevertStructure(array $newSubjectValues): ?array {
        // Return null, meaning that the restructuring operation was not successful such that the new values should not
        // be reassigned to the data source. This method must be implemented by the child class that changed the
        // structure in the first place.
        return null;
    }

    /**
     * Assigns the new value to the property of the subject. See {@link assignNewValue()}.
     *
     * @param string                $key      The key of the new value
     * @param mixed                 $newValue The new value
     * @param AbstractActionCommand $cmd      The command that called this method
     * @since 1.12.0
     */
    protected function onAssignNewValue(string $key, $newValue, AbstractActionCommand $cmd): void {
        // Do nothing. This is for children to implement.
    }

    /*
     *
     */

    /**
     * Undo the restructuring operation made on the subject values earlier by this property when calculating the new
     * values of the subjects. For example, if this property produced multiple outputs from a single subject value,
     * then it is not assignable to the data source. The property needs to revert the structure into its original such
     * that the resultant array's keys and values can be directly used to reassign a value to the data source.
     *
     * @param array|null $newSubjectValues Key-value pairs where the keys are the ones created by the property, while
     *                                     the values are modified values.
     * @return array|null
     * @since 1.11.0
     */
    public function revertStructure(?array $newSubjectValues): ?array {
        // If the given array is null, return null.
        if ($newSubjectValues === null) return null;

        // If the array is empty, return it back. We do not need to perform any operation on it.
        if (!$newSubjectValues) return $newSubjectValues;

        // If the subject values were not restructured, return them without modifying anything. The modified keys
        // contain a specific separator. When the keys are modified, they are modified all. So, checking if the first
        // key has the separator or not is sufficient to understand if the subject keys were modified previously.
        if (!Str::contains(array_keys($newSubjectValues)[0], static::KEY_SEPARATOR)) return $newSubjectValues;

        // Revert the structure
        return $this->onRevertStructure($newSubjectValues);
    }

    public function toArray(): array {
        return array_merge(parent::toArray(), [
            'actionProperty' => true,
        ]);
    }

}