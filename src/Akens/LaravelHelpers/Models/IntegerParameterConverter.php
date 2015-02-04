<?php namespace Akens\LaravelHelpers\Models;

/**
 * A subclass of ParameterConverter for handling integer parameters.
 *
 * @package Akens\LaravelHelpers\Models
 */
class IntegerParameterConverter extends ParameterConverter {
    /**
     * Overrides the base implementation to convert a string parameter to an integer.
     *
     * @param string $value The value to convert.
     *
     * @return int The converted value.
     *
     * @throws InvalidParameterValueException When the value can't be converted to an integer.
     */
    public function convertValue($value) {
        $intValue = intval($value);
        if ($intValue === 0 && $value !== '0') {
            throw new InvalidParameterValueException();
        }
        return $intValue;
    }
}