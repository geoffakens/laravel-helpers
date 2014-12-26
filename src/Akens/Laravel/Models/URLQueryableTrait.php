<?php
namespace Akens\Laravel\Models;

/**
 * Trait to make a model queryable via URL parameters.  The trait will inspect the model's table and make each column
 * queryable.  For example, a users table might have first_name, last_name and email columns.  With this trait added
 * to the User model, it can be queried like so:
 *
 * <pre>http://www.myapp.com/api/v1/users?email=geoff@geoffakens.com</pre>
 *
 * To make use of the trait, add it to your model:
 *
 * <code>
 * <?php
 * class User extends Eloquent {
 *     use Akens\Laravel\Models\URLQueryableTrait;
 *
 *     protected $table = 'users';
 * }
 * </code>
 *
 * Then, modify your controller to make use of the trait:
 *
 * <code>
 * public function index()
 * {
 *     $perPage = Input::get('per_page', 25);
 *     $orderBy = Input::get('order_by', 'last_name');
 *     $orderByDir = Input::get('order_by_dir', 'asc');
 *     $queryParameters = Input::except('per_page', 'order_by', 'order_by_dir');
 *
 *     // Build, execute and return a query using the URL parameters from the request.
 *     return $this->user->buildQueryFromParameters($queryParameters)
 *         ->orderBy($orderBy, $orderByDir)
 *         ->paginate($perPage);
 * }
 * </code>
 *
 * @package Akens\Laravel\Models
 */
trait URLQueryableTrait {
    /**
     * Builds a new query with the given parameters.
     *
     * @param array $parameters The parameters to use when building the query.  Parameters that do not match a column in
     * the model's table will be ignored.
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