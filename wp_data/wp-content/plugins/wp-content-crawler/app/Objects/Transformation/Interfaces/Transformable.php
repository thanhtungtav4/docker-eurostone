<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 17/02/2019
 * Time: 18:43
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Interfaces;

use WPCCrawler\Objects\Transformation\Objects\TransformableField;
use WPCCrawler\Objects\Transformation\Objects\TransformableFieldList;

/**
 * IMPORTANT!
 * ==========
 *
 * The transformable's field keys should not start with that transformable's identifier. For example, {@link PostData}'s
 * identifier is "post". In this case, {@link PostData}'s field keys should not start with "post". This can be easily
 * achieved by avoiding defining an instance variable named as "post". So, if {@link PostData} does not have a field
 * named as "post", everything is fine, since its field keys cannot start with "post" anymore because it does not have
 * a field named as "post".
 *
 * Another example can be {@link WooCommerceData}. Its identifier is "product". So, {@link WooCommerceData} should not
 * have a field named as "product".
 *
 * This is mainly because data sources are mapped to {@link Filter}s' commands by using their identifiers. If a field
 * key starts with the same identifier, it might cause unwanted behaviors.
 *
 * @since 1.8.1
 * @since 1.11.0 Add getConditionCommandFields(), getActionCommandFields() methods.
 */
interface Transformable {

    /**
     * Returns a list of the fields that can be transformed. See {@link TransformableField::$dotKey}. Since
     * transformations can be applied to values that are non-array and non-object, all fields are expected to return a
     * single-value data type, such as string, numeric, date, etc. Also see {@link Transformable} about certain
     * limitations.
     *
     * @return TransformableFieldList
     * @since 1.9.0
     * @since 1.11.0 Change return type to TransformableFieldList
     */
    public function getTransformableFields(): TransformableFieldList;

    /**
     * This method defines the fields that can be interacted with. For example, if certain things need to be found and
     * replaced, the values of these fields can be retrieved and changed by another method. Another example might be
     * parsing short codes in all the values of all the fields. So, these fields can be queried and changed by a method
     * that wants to do so. While the fields returned by {@link getTransformableFields()} are generally used to
     * translate and spin, the fields returned by this method can be used for anything. Since transformations can be
     * applied to values that are non-array and non-object, all fields are expected to return a single-value data type,
     * such as string, numeric, date, etc. Also see {@link Transformable} about certain limitations.
     *
     * @return TransformableFieldList
     * @since 1.9.0
     * @since 1.11.0 Change return type to TransformableFieldList
     */
    public function getInteractableFields(): TransformableFieldList;

    /**
     * Return a list of fields that can be shown as subjects in the condition command parts of the filters. Fields
     * returned by {@link getInteractableFields()} are included as command subjects by default. The fields returned by
     * this method will be suffixed. Therefore, the return value can contain the same fields returned by
     * {@link getInteractableFields()} with a different data type. The values of the fields will be retrieved from this
     * class by using their getter methods. Therefore, the commands that use these fields can have the raw value and do
     * whatever it wants with it. Also see {@link Transformable} about certain limitations.
     *
     * @return TransformableFieldList|null
     * @since 1.11.0
     */
    public function getConditionCommandFields(): ?TransformableFieldList;

    /**
     * Return a list of fields that can be shown as subjects in the action command parts of the filters. Fields
     * returned by {@link getInteractableFields()} are included as command subjects by default. The fields returned by
     * this method will be suffixed. Therefore, the return value can contain the same fields returned by
     * {@link getInteractableFields()} with a different value type. The values of the fields will be retrieved from this
     * class by using their getter methods. Therefore, the commands that use these fields can have the raw value and do
     * whatever it wants with it. Also see {@link Transformable} about certain limitations.
     *
     * @return TransformableFieldList|null
     * @since 1.11.0
     */
    public function getActionCommandFields(): ?TransformableFieldList;

}