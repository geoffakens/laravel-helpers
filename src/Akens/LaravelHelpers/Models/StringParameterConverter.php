<?php namespace Akens\LaravelHelpers\Models;

/**
 * A subclass of ParameterConverter for handling string parameters.
 *
 * @package Akens\LaravelHelpers\Models
 */
class StringParameterConverter extends ParameterConverter {
    /**
     * Adds a where clause to the specified query with the given value converted to the appropriate type for the column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder to add the where clause to.
     * @param string $value The value to be converted for use in the where clause.
     *
     * @return \Illuminate\Database\Eloquent\Builder The query builder with the where clause added.
     */
    public function addWhereToQuery($query, $value) {
        if(is_array($value)) {
            return $query->whereIn($this->columnName, $this->getQueryValueForInClause($value));
        }

        return parent::addWhereToQuery($query, $value);
    }

    /**
     * Converts an array of parameter values to the database values required for the query.
     *
     * @param array $values The values to be converted.
     *
     * @return array The converted values.
     */
    protected function getQueryValueForInClause($values) {
        $inValues = [];
        foreach($values as $value) {
            $inValues[] = $this->columnValueConverter->__invoke(parent::convertValue($value));
        }
        return $inValues;
    }

    /**
     * Overrides the base implementation to provide simple LIKE querying that matches at the beginning of the string.
     *
     * @param string $value The value to convert.
     * @param bool $forInClause Indicates whether the value will be used in an IN(...) clause.
     *
     * @return string The converted value.
     */
    public function convertValue($value, $forInClause = false) {
        // Don't add a wildcard if this is for an IN(...) clause.
        if($forInClause) {
            return $value;
        }

        return $value . '%';
    }

    /**
     * Overrides the base implementation to provide a LIKE operator.
     *
     * @param string $value The parameter value to parse the operator from.
     *
     * @return string The query operator used when adding a where clause to a query.
     */
    protected function getQueryOperator($value) {
        return 'LIKE';
    }
}