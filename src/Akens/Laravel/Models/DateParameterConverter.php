<?php
namespace Akens\Laravel\Models;

use DateTime;

/**
 * A subclass of ParameterConverter for handling date parameters.
 *
 * @package Akens\Laravel\Models
 */
class DateParameterConverter extends ParameterConverter {
    /**
     * Overrides the base implementation to convert a string parameter to a DateTime.
     *
     * @param string $value The value to convert.
     *
     * @return DateTime The converted value.
     *
     * @throws InvalidParameterValueException When the value can't be converted to a DateTime.
     */
    public function convertValue($value)
    {
        $convertedValue =  DateTime::createFromFormat('Y-m-d', $value);

        if($convertedValue === false)
        {
            throw new InvalidParameterValueException(sprintf("Unable to convert '%s' to DateTime.  Valid values should use the Y-m-d format.", $value));
        }

        return $convertedValue;
    }
}