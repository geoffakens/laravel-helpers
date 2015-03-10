<?php
namespace Akens\LaravelHelpers\Models;

use Mockery;

class FloatParameterConverterTest extends \PHPUnit_Framework_TestCase {
    private $columnName = 'column_name';
    private $converter;

    public function setUp()
    {
        $this->converter = new FloatParameterConverter($this->columnName, function($value) {
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

    public function testAddsWhereClauseForParameterWithValueOfZeroWithDecimal()
    {
        $this->expectConversion('0.0', 0);
    }

    public function testAddsWhereClauseForParameterWithPositiveValue()
    {
        $this->expectConversion('100.34', 100.34);
    }

    public function testAddsWhereClauseForParameterWithNegativeValue()
    {
        $this->expectConversion('-100.34', -100.34);
    }

    public function testAddsWhereClauseForParameterWithGreaterThanOperator()
    {
        $this->expectConversion('>100.34', 100.34, '>');
    }

    public function testAddsWhereClauseForParameterWithLessThanOperator()
    {
        $this->expectConversion('<100.34', 100.34, '<');
    }

    public function testAddsWhereClauseForParameterWithRange() {
        $expectedValues = [10.5, 20.5];
        $mockQueryBuilder = Mockery::mock('\Illuminate\Database\Eloquent\Builder');
        $mockQueryBuilder->shouldReceive('whereBetween')
            ->with($this->columnName, $expectedValues)
            ->once()
            ->andReturn($mockQueryBuilder);

        $this->converter->addWhereToQuery($mockQueryBuilder, "10.5-20.5");
    }

    /**
     * @expectedException \Akens\LaravelHelpers\Models\InvalidParameterValueException
     */
    public function testInvalidParameterValueExceptionIsThrownForInvalidValue()
    {
        $mockQueryBuilder = Mockery::mock('\Illuminate\Database\Eloquent\Builder');

        $this->converter->addWhereToQuery($mockQueryBuilder, 'invalid');
    }

    /**
     * @expectedException \Akens\LaravelHelpers\Models\InvalidParameterValueException
     */
    public function testInvalidParameterValueExceptionIsThrownForInvalidOperator()
    {
        $mockQueryBuilder = Mockery::mock('\Illuminate\Database\Eloquent\Builder');

        $this->converter->addWhereToQuery($mockQueryBuilder, '<>100.34');
    }

    /**
     * Helper function for parameter conversion tests.
     *
     * @param string $paramValue The parameter value to convert for the query.
     * @param integer $expectedQueryValue The value expected in the query.
     * @param string $expectedOperator The operator that should be used in the query.
     */
    public function expectConversion($paramValue, $expectedQueryValue, $expectedOperator = '=')
    {
        $mockQueryBuilder = Mockery::mock('\Illuminate\Database\Eloquent\Builder');
        $mockQueryBuilder->shouldReceive('where')
            ->with($this->columnName, $expectedOperator, $expectedQueryValue)
            ->once()
            ->andReturn($mockQueryBuilder);

        $this->converter->addWhereToQuery($mockQueryBuilder, $paramValue);
    }
}