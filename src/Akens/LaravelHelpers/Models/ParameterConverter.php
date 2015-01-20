<?php
namespace Akens\LaravelHelpers\Models;

/**
 * Exception thrown when a URL parameter can't be converted to the appropriate type.
 *
 * @package Akens\LaravelHelpers\Models
 */
class InvalidParameterValueException extends \Exception {};

/**
 * Class for converting a URL parameter to the appropriate type and format for a query where clause.
 *
 * @package Akens\LaravelHelpers\Models
 */
class ParameterConverter {
    /**
     * @var string The name of the column that this ParameterConverter is converting.
     */
    protected $columnName;

    /**
     * @var callable The callback that will be used to convert parameter values to database values.
     */
    protected $columnValueConverter;

    /**
     * Constructs a new ParameterConverter.
     *
     * @param $columnName string The name of the column that this ParameterConverter will handle.
     * @param $columnValueConverter callable A callback that can be used to convert parameter values to database values.
     */
    public function __construct($columnName, $columnValueConverter)
    {
        $this->columnName = $columnName;
        $this->columnValueConverter = $columnValueConverter;
    }

    /**
     * Gets the appropriate query operator for the query.
     *
     * @return string The query operator used when adding a where clause to a query.
     */
    protected function getQueryOperator()
    {
        return '=';
    }

    /**
     * Converts a string parameter value to the appropriate type or format required for the query.
     *
     * @param $value string The parameter value to convert.
     *
     * @return mixed The converted value.
     */
    protected function convertValue($value)
    {
        return $value;
    }

    /**
     * Converts a parameter value to the database value required for the query.
     *
     * @param $value string The value to be converted.
     *
     * @return mixed The converted value.
     */
    protected function getQueryValue($value)
    {
        return $this->columnValueConverter->__invoke($this->convertValue($value));
    }

    /**
     * Adds a where clause to the specified query with the given value converted to the appropriate type for the column.
     *
     * @param $query \Illuminate\Database\Eloquent\Builder The query builder to add the where clause to.
     * @param $value string The value to be converted for use in the where clause.
     *
     * @return \Illuminate\Database\Eloquent\Builder The query builder with the where clause added.
     */
    public function addWhereToQuery($query, $value)
    {
        return $query->where($this->columnName, $this->getQueryOperator(), $this->getQueryValue($value));
    }
}