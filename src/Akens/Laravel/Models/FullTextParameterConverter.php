<?php
namespace Akens\Laravel\Models;

/**
 * A subclass of ParameterConverter for handling FULLTEXT parameters.
 *
 * @package Akens\Laravel\Models
 */
class FullTextParameterConverter extends ParameterConverter {
    /**
     * Overrides the base implementation to provide FULLTEXT index matching.
     *
     * @param string $value The value to convert.
     *
     * @return string The converted value.
     */
    public function convertValue($value) {
        // TODO: Escape the value to prevent injection attacks.
        // TODO: Parse tokens and convert to boolean mode.
        return $value;
    }

    /**
     * Overrides the base implementation to add a raw where clause with a MATCH statement.
     *
     * @param $query \Illuminate\Database\Eloquent\Builder The query builder to add the where clause to.
     * @param $value string The value to be converted for use in the where clause.
     *
     * @return \Illuminate\Database\Eloquent\Builder The query builder with the where clause added.
     */
    public function addWhereToQuery($query, $value)
    {
        $term = $this->getQueryValue($value);
        $rawQuery = $query->getQuery();
        $rawQuery->whereRaw("MATCH($this->columnName) AGAINST ('$term')");
        return $query;
    }
}