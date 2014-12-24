<?php
namespace Akens\Laravel\Models;

use Illuminate\Support\Facades\App;
use Doctrine\DBAL\Types\Type;

/**
 * Factory class for providing ParameterConverter instances.
 *
 * @package Akens\Laravel\Models
 */
class ParameterConverterProvider {
    /**
     * @var array Cached ParameterConverters used by the factory method.
     */
    protected static $converters = array();

    /**
     * @var array Mapping of column type to ParameterConverter type.
     */
    protected static $parameterConverterMap = array(
        Type::BOOLEAN => 'Akens\Laravel\Models\BooleanParameterConverter',
        Type::INTEGER => 'Akens\Laravel\Models\IntegerParameterConverter',
        Type::SMALLINT => 'Akens\Laravel\Models\IntegerParameterConverter',
        Type::BIGINT => 'Akens\Laravel\Models\IntegerParameterConverter',
        Type::STRING => 'Akens\Laravel\Models\StringParameterConverter',
        Type::DATE => 'Akens\Laravel\Models\DateParameterConverter',
    );

    /**
     * Initializes and caches a set of ParameterConverters for the given table's columns.
     *
     * @param $tableName string The name of the table to initialize a set of ParameterConverters for.
     */
    protected static function initConvertersForTable($tableName)
    {
        // ParameterConverters are cached to avoid querying the table schema repeatedly.
        if (isset(static::$converters[$tableName]))
        {
            return;
        }

        // Query the table schema.
        $db = App::make('db');
        $schemaManager = $db->getDoctrineSchemaManager($tableName);
        $columns = $schemaManager->listTableColumns($tableName);

        // Create ParameterConverters for each column.
        foreach($columns as $columnName => $column)
        {
            // Create a closure to handle converting parameter values to database values.
            $columnValueConverter = function($value) use($schemaManager, $column)
            {
                $columnType = $column->getType();
                return $columnType->convertToDatabaseValue($value, $schemaManager->getDatabasePlatform());
            };

            if (array_key_exists($column->getType()->getName(), static::$parameterConverterMap))
            {
                static::$converters[$tableName][$columnName] = new static::$parameterConverterMap[$column->getType()->getName()]($columnName, $columnValueConverter);
            }
            else
            {
                static::$converters[$tableName][$columnName] = new ParameterConverter($columnName, $columnValueConverter);
            }

        }
    }

    /**
     * Factory method for getting a ParameterConverter instance.
     *
     * @param $tableName string The name of the table containing the column.
     * @param $columnName string The name of the column in the table.
     *
     * @return ParameterConverter  A ParameterConverter instance for the table column, or null if the column does not exist in the table.
     */
    public static function getParameterConverter($tableName, $columnName)
    {
        static::initConvertersForTable($tableName);
        if(array_key_exists($columnName, static::$converters[$tableName]))
        {
            return static::$converters[$tableName][$columnName];
        }
        return null;
    }
}