<?php
namespace Akens\LaravelHelpers\Models;

/**
 * A subclass of ParameterConverter for handling boolean parameters.
 *
 * @package Akens\LaravelHelpers\Models
 */
class BooleanParameterConverter extends ParameterConverter {
    /**
     * Overrides the base implementation to convert a string parameter to a boolean.
     *
     * @param string $value The value to convert.  Valid parameter values include:
     *
     * '0', '1', 'true', 'false', 'yes', 'no'
     *
     * @return boolean The converted value.
     *
     * @throws InvalidParameterValueException When the value can't be converted to a boolean.
     */
    public function convertValue($value) {
        $lowercaseParameterValue = strtolower($value);
        switch($lowercaseParameterValue)
        {
            case 'true':
            case 'yes':
            case '1':
                return true;
            case 'false':
            case 'no':
            case '0':
                return false;
            default:
                throw new InvalidParameterValueException(sprintf("Unable to convert '%s' to boolean.  Valid values include 'true', 'false', 'yes', 'no', '1', or '0'.", $value));
        }
    }
}