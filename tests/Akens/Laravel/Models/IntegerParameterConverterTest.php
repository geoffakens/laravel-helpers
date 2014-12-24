<?php
namespace Akens\Laravel\Models;

use Mockery;

class IntegerParameterConverterTest extends \PHPUnit_Framework_TestCase {
    private $columnName = 'column_name';
    private $converter;

    public function setUp()
    {
        $this->converter = new IntegerParameterConverter($this->columnName, function($value) {
            return $value;
        });
        parent::setUp();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testAddsWhereClauseForParameterWithValueOfZero()
    {
        $this->expectConversion('0', 0);
    }

    public function testAddsWhereClauseForParameterWithPositiveValue()
    {
        $this->expectConversion('100', 100);
    }

    public function testAddsWhereClauseForParameterWithNegativeValue()
    {
        $this->expectConversion('-100', -100);
    }

    /**
     * @expectedException \Akens\Laravel\Models\InvalidParameterValueException
     */
    public function testInvalidParameterValueExceptionIsThrow()
    {
        $mockQueryBuilder = Mockery::mock('\Illuminate\Database\Eloquent\Builder');

        $this->converter->addWhereToQuery($mockQueryBuilder, 'invalid');
    }

    /**
     * Helper function for parameter conversion tests.
     *
     * @param $paramValue string The parameter value to convert for the query.
     * @param $expectedQueryValue integer The value expected in the query.
     */
    public function expectConversion($paramValue, $expectedQueryValue)
    {
        $mockQueryBuilder = Mockery::mock('\Illuminate\Database\Eloquent\Builder');
        $mockQueryBuilder->shouldReceive('where')
            ->with($this->columnName, '=', $expectedQueryValue)
            ->once()
            ->andReturn($mockQueryBuilder);

        $this->converter->addWhereToQuery($mockQueryBuilder, $paramValue);
    }
}