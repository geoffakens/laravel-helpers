<?php
/**
 * Created by PhpStorm.
 * User: gakens
 * Date: 12/22/14
 * Time: 1:42 PM
 */

namespace Akens\Laravel\Models;

use Mockery;

class ParameterConverterTest extends \PHPUnit_Framework_TestCase {
    private $columnName = 'column_name';
    private $converter;

    public function setUp()
    {
        $this->converter = new ParameterConverter($this->columnName, function($value) {
            return $value;
        });
        parent::setUp();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testAddsWhereClauseWithParameterValue()
    {
        $value = 'Some Value';
        $mockQueryBuilder = Mockery::mock('\Illuminate\Database\Eloquent\Builder');
        $mockQueryBuilder->shouldReceive('where')
            ->with($this->columnName, '=', $value)
            ->once()
            ->andReturn($mockQueryBuilder);

        $this->converter->addWhereToQuery($mockQueryBuilder, $value);
    }
}