<?php
namespace Akens\LaravelHelpers\Models;

use Mockery;

class BooleanParameterConverterTest extends \PHPUnit_Framework_TestCase {
    private $columnName = 'column_name';
    private $converter;

    public function setUp()
    {
        $this->converter = new BooleanParameterConverter($this->columnName, function($value) {
            return $value;
        });
        parent::setUp();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testAddsWhereClauseForParameterWithValueOfTrue()
    {
        $this->expectConversion('true', true);
    }

    public function testAddsWhereClauseForParameterWithValueOfYes()
    {
        $this->expectConversion('yes', true);
    }

    public function testAddsWhereClauseForParameterWithValueOfOne()
    {
        $this->expectConversion('1', true);
    }

    public function testAddsWhereClauseForParameterWithValueOfFalse()
    {
        $this->expectConversion('no', false);
    }

    public function testAddsWhereClauseForParameterWithValueOfNo()
    {
        $this->expectConversion('false', false);
    }

    public function testAddsWhereClauseForParameterWithValueOfZero()
    {
        $this->expectConversion('0', false);
    }

    public function testAddsWhereClauseForParameterWithMixedCaseValues()
    {
        $this->expectConversion('True', true);
        $this->expectConversion('False', false);
        $this->expectConversion('YES', true);
        $this->expectConversion('NO', false);
    }

    /**
     * @expectedException \Akens\LaravelHelpers\Models\InvalidParameterValueException
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
     * @param $expectedQueryValue boolean The value expected in the query.
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