<?php
namespace Akens\Laravel\Models;

/**
 * Trait to make a model queryable via URL parameters.
 *
 * @package Akens\Laravel\Models
 */
trait URLQueryableTrait {
    /**
     * Builds a new query for the given parameters.
     *
     * @param array $parameters The parameters to use when building the query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildQueryFromParameters(array $parameters)
    {
        $query = $this->newQuery();
        foreach($parameters as $paramName => $paramValue)
        {
            $query = $this->addWhereToQuery($query, $paramName, $paramValue);
        }
        return $query;
    }

    /**
     * Adds a where clause to the specified query for the given parameter and value.
     *
     * @param $query \Illuminate\Database\Eloquent\Builder The query builder to add the where clause to.
     * @param $paramName string The name of the parameter being added to the query.
     * @param $paramValue string The value of the parameter being added to the query.
     *
     * @return \Illuminate\Database\Eloquent\Builder The query builder with the where clause added.
     */
    protected function addWhereToQuery($query, $paramName, $paramValue)
    {
        $converter = ParameterConverterProvider::getParameterConverter($this->table, $paramName);
        if(isset($converter))
        {
            return $converter->addWhereToQuery($query, $paramValue);
        }
        return $query;
    }
}