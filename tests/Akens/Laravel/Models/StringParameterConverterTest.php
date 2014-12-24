<?php
namespace Akens\Laravel\Models;

use Mockery;

class StringParameterConverterTest extends \PHPUnit_Framework_TestCase {
    private $columnName = 'column_name';
    private $converter;

    public function setUp()
    {
        $this->converter = new StringParameterConverter($this->columnName, function($value) {
            return $value;
        });
        parent::setUp();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testAddsWhereClauseLikeMatchingBeginningOfString()
    {
        $this->expectConversion('query', 'query%');
    }

    /**
     * Helper function for parameter conversion tests.
     *
     * @param $paramValue string The parameter value to convert for the query.
     * @param $expectedQueryValue string The value expected in the query.
     */
    public function expectConversion($paramValue, $expectedQueryValue)
    {
        $mockQueryBuilder = Mockery::mock('\Illuminate\Database\Eloquent\Builder');
        $mockQueryBuilder->shouldReceive('where')
            ->with($this->columnName, 'LIKE', $expectedQueryValue)
            ->once()
            ->andReturn($mockQueryBuilder);

        $this->converter->addWhereToQuery($mockQueryBuilder, $paramValue);
    }
}