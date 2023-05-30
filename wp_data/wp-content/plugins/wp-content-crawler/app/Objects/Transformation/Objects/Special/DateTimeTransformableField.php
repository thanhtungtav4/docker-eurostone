<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 27/05/2020
 * Time: 15:30
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Transformation\Objects\Special;


use DateTime;
use WPCCrawler\Environment;

class DateTimeTransformableField extends SpecialTransformableField {

    protected function getSubjectItemAsString($subject): ?string {
        // It is not possible to get string representation of a DateTime by casting it to string. It results in a fatal
        // error. So, let's create a string representation of the date.
        return is_a($subject, DateTime::class)
            ? $subject->format(Environment::mysqlDateFormat())
            : null;
    }

}