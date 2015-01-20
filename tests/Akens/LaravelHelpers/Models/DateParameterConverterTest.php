<?php
namespace Akens\LaravelHelpers\Models;

use Mockery;
use DateTime;

class DateParameterConverterTest extends \PHPUnit_Framework_TestCase {
    private $columnName = 'column_name';
    private $converter;

    public function tearDown()
    {
        Mockery::close();
    }

    public function testAddsWhereClauseForParameterWithYmdValue()
    {
        $dateString = '2014-12-23';

        $this->converter = new DateParameterConverter($this->columnName, function($value) use($dateString) {
            $this->assertInstanceOf('DateTime', $value);
            $convertedValue = $value->format('Y-m-d');
            $this->assertEquals($dateString, $convertedValue);
            return $convertedValue;
        });

        $this->mockHelper($dateString);
    }

    /**
     * Helper function for creating a mock and adding a where clause.
     *
     * @param $paramValue string The parameter value to use in the where clause.
     */
    public function mockHelper($paramValue)
    {
        $mockQueryBuilder = Mockery::mock('\Illuminate\Database\Eloquent\Builder');
        $mockQueryBuilder->shouldReceive('where')->andReturn($mockQueryBuilder);
        $this->converter->addWhereToQuery($mockQueryBuilder, $paramValue);
    }
}