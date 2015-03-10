<?php namespace Akens\LaravelHelpers\Models;


/**
 * A subclass of ParameterConverter for handling numeric parameters.
 *
 * @package Akens\LaravelHelpers\Models
 */
class NumericParameterConverter extends ParameterConverter {
    /**
     * Parse the operator and operand from the given parameter value.
     *
     * @param string $value The parameter value to parse.
     *
     * @return array An empty array if the value could not be parsed, or an array containing the keys 'operator' and
     * 'operand'.
     */
    protected function parseValues($value) {
        $result = [];
        $values = [];
        if(preg_match('/^([<>]{1})(.+)/i', $value, $result)) {
            $values['operator'] = $result[1];
            $values['operand'] = $result[2];
        }
        return $values;
    }

    /**
     * Gets the appropriate query operator for the query.
     *
     * @param string $value The parameter value to parse the operator from.
     *
     * @return string The query operator used when adding a where clause to a query.  Defaults to '='.
     */
    protected function getQueryOperator($value) {
        $values = $this->parseValues($value);
        if(isset($values['operator'])) {
            return $values['operator'];
        }
        return '=';
    }

    /**
     * Converts a parameter value to the database value required for the query.
     *
     * @param string $value The value to be converted.
     *
     * @return mixed The converted value.
     */
    protected function getQueryValue($value) {
        $values = $this->parseValues($value);
        if(isset($values['operand'])) {
            return parent::getQueryValue($values['operand']);
        }
        return parent::getQueryValue($value);
    }
}