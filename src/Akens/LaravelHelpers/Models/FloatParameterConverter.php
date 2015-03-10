<?php namespace Akens\LaravelHelpers\Models;

/**
 * A subclass of ParameterConverter for handling float parameters.
 *
 * @package Akens\LaravelHelpers\Models
 */
class FloatParameterConverter extends NumericParameterConverter {
    /**
     * Overrides the base implementation to convert a string parameter to a float.
     *
     * @param string $value The value to convert.
     *
     * @return float The converted value.
     *
     * @throws InvalidParameterValueException When the value can't be converted to a float.
     */
    public function convertValue($value) {
        $floatValue = floatval($value);
        if ($floatValue === 0.0 && $value !== '0' && $value !== '0.0') {
            throw new InvalidParameterValueException();
        }
        return $floatValue;
    }
}