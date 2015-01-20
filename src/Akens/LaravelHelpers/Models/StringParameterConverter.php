<?php
namespace Akens\LaravelHelpers\Models;

/**
 * A subclass of ParameterConverter for handling string parameters.
 *
 * @package Akens\LaravelHelpers\Models
 */
class StringParameterConverter extends ParameterConverter {
    /**
     * Overrides the base implementation to provide simple LIKE querying that matches at the beginning of the string.
     *
     * @param string $value The value to convert.
     *
     * @return string The converted value.
     */
    public function convertValue($value) {
        return $value . '%';
    }

    /**
     * Overrides the base implementation to provide a LIKE operator.
     *
     * @return string The query operator used when adding a where clause to a query.
     */
    protected function getQueryOperator()
    {
        return 'LIKE';
    }
}