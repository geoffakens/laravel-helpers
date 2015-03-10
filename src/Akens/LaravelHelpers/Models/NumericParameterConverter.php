<?php namespace Akens\LaravelHelpers\Models;

define('UNARY_PATTERN', '/^([<>]{1})(\d+[.]?\d+)/i');
define('RANGE_PATTERN', '/^(\d+[.]?\d+)-(\d+[.]?\d+)/i');

/**
 * A subclass of ParameterConverter for handling numeric parameters.
 *
 * @package Akens\LaravelHelpers\Models
 */
class NumericParameterConverter extends ParameterConverter {
    /**
     * Adds a where clause to the specified query with the given value converted to the appropriate type for the column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder to add the where clause to.
     * @param string $value The value to be converted for use in the where clause.
     *
     * @return \Illuminate\Database\Eloquent\Builder The query builder with the where clause added.
     */
    public function addWhereToQuery($query, $value) {
        $components = [];
        if($this->parseRangeComponents($value, $components)) {
            return $query->whereBetween($this->columnName, $components);
        }
        else if($this->parseUnaryComponents($value, $components)) {
            return $query->where($this->columnName, $components['operator'], $components['operand']);
        }

        return parent::addWhereToQuery($query, $value);
    }

    /**
     * Parse the range from the given parameter value.
     *
     * @param string $value The parameter value to parse.
     * @param array $components An array that will be populated with the start and end of the range if the value matches
     * the range pattern.
     *
     * @return bool True if the value matched the range pattern, false if not.
     */
    protected function parseRangeComponents($value, &$components) {
        $result = [];
        if(preg_match(RANGE_PATTERN, $value, $result)) {
            $components[] = $this->getQueryValue($result[1]);
            $components[] = $this->getQueryValue($result[2]);
            return true;
        }
        return false;
    }

    /**
     * Parse the operator and operand from the given parameter value.
     *
     * @param string $value The parameter value to parse.
     * @param array $components An array that will be populated with the operator and operand if the value matches the
     * unary pattern.
     *
     * @return bool True if the value matched the unary pattern, false if not.
     */
    protected function parseUnaryComponents($value, &$components) {
        $result = [];
        if(preg_match(UNARY_PATTERN, $value, $result)) {
            $components['operator'] = $result[1];
            $components['operand'] = $this->getQueryValue($result[2]);
            return true;
        }
        return false;
    }
}