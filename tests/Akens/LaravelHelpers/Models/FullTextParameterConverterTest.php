<?php
namespace Akens\LaravelHelpers\Models;

use Mockery;

class FullTextParameterConverterTest extends \PHPUnit_Framework_TestCase {
    private $columnName = 'column_name1, column_name2';
    private $converter;

    public function setUp()
    {
        $this->converter = new FullTextParameterConverter($this->columnName, function($value) {
            return $value;
        });
        parent::setUp();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testAddsWhereClauseForParameterWithSingleTerm()
    {
        $this->expectConversion('term', 'term');
    }

    public function testAddsWhereClauseForParameterWithMultipleTerms()
    {
        $this->expectConversion('term1 term2', 'term1 term2');
    }

    /**
     * Helper function for parameter conversion tests.
     *
     * @param $paramValue string The parameter value to convert for the query.
     * @param $expectedQueryValue boolean The value expected in the query.
     */
    public function expectConversion($paramValue, $expectedQueryValue)
    {
        $mockRawQueryBuilder = Mockery::mock('\Illuminate\Database\Query\Builder');
        $mockQueryBuilder = Mockery::mock('\Illuminate\Database\Eloquent\Builder');
        $mockQueryBuilder->shouldReceive('getQuery')
            ->once()
            ->andReturn($mockRawQueryBuilder);

        $mockRawQueryBuilder->shouldReceive('whereRaw')
            ->once()
            ->with("MATCH($this->columnName) AGAINST ('$expectedQueryValue')");

        $this->converter->addWhereToQuery($mockQueryBuilder, $paramValue);
    }
}