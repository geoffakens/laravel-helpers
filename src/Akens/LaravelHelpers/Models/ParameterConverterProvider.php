<?php namespace Akens\LaravelHelpers\Models;

use Illuminate\Support\Facades\App;
use Doctrine\DBAL\Types\Type;

/**
 * Factory class for providing ParameterConverter instances.
 *
 * @package Akens\LaravelHelpers\Models
 *
 * @see \Akens\LaravelHelpers\Models\ParameterConverter
 */
class ParameterConverterProvider {

    /**
     * @var array Cached ParameterConverters used by the factory method.
     */
    protected static $converters = array();

    /**
     * @var array Mapping of Doctrine column type to ParameterConverter type.
     *
     * @link http://www.doctrine-project.org/api/dbal/2.5/class-Doctrine.DBAL.Types.Type.html
     */
    protected static $parameterConverterMap = array(
        Type::BOOLEAN => 'Akens\LaravelHelpers\Models\BooleanParameterConverter',
        Type::INTEGER => 'Akens\LaravelHelpers\Models\IntegerParameterConverter',
        Type::SMALLINT => 'Akens\LaravelHelpers\Models\IntegerParameterConverter',
        Type::BIGINT => 'Akens\LaravelHelpers\Models\IntegerParameterConverter',
        Type::FLOAT => 'Akens\LaravelHelpers\Models\FloatParameterConverter',
        Type::DECIMAL => 'Akens\LaravelHelpers\Models\FloatParameterConverter',
        Type::STRING => 'Akens\LaravelHelpers\Models\StringParameterConverter',
        Type::TEXT => 'Akens\LaravelHelpers\Models\StringParameterConverter',
        Type::DATE => 'Akens\LaravelHelpers\Models\DateParameterConverter'
    );

    /**
     * Factory method for getting a ParameterConverter instance.
     *
     * @param \Illuminate\Database\Connection $connection The connection to use when getting table schema information.
     * @param string $tableName The name of the table containing the column.
     * @param string $columnName The name of the column in the table.
     *
     * @return ParameterConverter A ParameterConverter instance for the table column, or null if the column does not exist in the table.
     *
     * @see \Akens\LaravelHelpers\Models\ParameterConverter
     */
    public static function getParameterConverter($connection, $tableName, $columnName) {
        // Make sure the column name includes the table name.
        if (!preg_match('|(?mi-Us)' . $tableName . '\\.|', $columnName)) {
            $columnName = "{$tableName}.{$columnName}";
        }

        static::initConvertersForTable($connection, $tableName);
        if (array_key_exists($columnName, static::$converters[$tableName])) {
            return static::$converters[$tableName][$columnName];
        }
        return null;
    }

    /**
     * Initializes and caches a set of ParameterConverters for the given table's columns.
     *
     * Makes use of the Doctrine database abstraction layer to query a table's schema.
     * @link http://www.doctrine-project.org/
     *
     * @param $tableName string The name of the table to initialize a set of ParameterConverters for.
     */
    protected static function initConvertersForTable($connection, $tableName) {
        // ParameterConverters are cached to avoid querying the table schema repeatedly.
        if (isset(static::$converters[$tableName])) {
            return;
        }

        // Query the table schema.
        $schemaManager = $connection->getDoctrineSchemaManager($tableName);

        // Prevent errors from enum columns, which Doctrine doesn't currently support.
        $platform = $schemaManager->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        // Create ParameterConverters for each column.
        $columns = $schemaManager->listTableColumns($tableName);
        foreach ($columns as $columnName => $column) {
            // Create a closure to handle converting parameter values to database values.
            $columnValueConverter = function ($value) use ($schemaManager, $column) {
                $columnType = $column->getType();
                return $columnType->convertToDatabaseValue($value, $schemaManager->getDatabasePlatform());
            };

            if (array_key_exists($column->getType()->getName(), static::$parameterConverterMap)) {
                static::$converters[$tableName]["{$tableName}.{$columnName}"] = new static::$parameterConverterMap[$column->getType()->getName()]("{$tableName}.{$columnName}",
                    $columnValueConverter);
            }
            else {
                static::$converters[$tableName]["{$tableName}.{$columnName}"] = new ParameterConverter("{$tableName}.{$columnName}",
                    $columnValueConverter);
            }
        }

        // Create ParameterConverters for each FULLTEXT index.
        $indexes = $schemaManager->listTableIndexes($tableName);
        $fulltextValueConverter = function ($value) use($connection) {
            // Escape the parameter value to prevent injection attacks.
            return $connection->getDoctrineConnection()->quote($value);
        };
        foreach ($indexes as $index) {
            if ($index->hasFlag('FULLTEXT')) {
                $columnNames = join(',', array_map(function ($column) use ($tableName) {
                    return "{$tableName}.{$column}";
                }, $index->getColumns()));
                static::$converters[$tableName]["{$tableName}.{$index->getName()}"] = new FullTextParameterConverter($columnNames,
                    $fulltextValueConverter);
            }
        }
    }
}