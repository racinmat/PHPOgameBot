<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Model\ValueObject\Coordinates;
use App\Utils\OgameParser;
use Carbon\CarbonInterval;

class OgameParserTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    // tests
    public function testCoordinates()
    {
	    $this->assertEquals(new Coordinates(1,2,3), OgameParser::parseOgameCoordinates('[1:2:3]'));
	    $this->assertEquals(new Coordinates(1,222,3), OgameParser::parseOgameCoordinates('[1:222:3]'));
	    $this->assertEquals(new Coordinates(5,22,13), OgameParser::parseOgameCoordinates('[5:22:13]'));
	    $this->assertEquals(new Coordinates(5,22,1), OgameParser::parseOgameCoordinates('[5:22:1]'));
    }

	public function testTimeInterval()
	{
		$this->assertEquals(new CarbonInterval(0, 0, 0, 0, 23, 4, 3), OgameParser::parseOgameTimeInterval('23hod 4min 3s'));
		$this->assertEquals(new CarbonInterval(0, 0, 0, 0, 23, 4, 0), OgameParser::parseOgameTimeInterval('23hod 4min'));
		$this->assertEquals(new CarbonInterval(0, 0, 0, 0, 23, 0, 3), OgameParser::parseOgameTimeInterval('23hod 3s'));
		$this->assertEquals(new CarbonInterval(0, 0, 0, 0, 2, 4, 3), OgameParser::parseOgameTimeInterval('2hod 4min 3s'));
		$this->assertEquals(new CarbonInterval(0, 0, 0, 0, 0, 40, 3), OgameParser::parseOgameTimeInterval('40min 3s'));
		$this->assertEquals(new CarbonInterval(0, 0, 0, 1, 16, 0, 0), OgameParser::parseOgameTimeInterval('1d 16hod'));
		$this->assertEquals(new CarbonInterval(0, 0, 1, 1, 8, 0, 0), OgameParser::parseOgameTimeInterval('1t 1d 8hod'));
		$this->assertEquals(new CarbonInterval(0, 0, 1, 0, 3, 0, 0), OgameParser::parseOgameTimeInterval('1t 3hod'));
		$this->assertEquals(new CarbonInterval(0, 0, 0, 0, 2, 0, 0), OgameParser::parseOgameTimeInterval('2hod'));
		$this->assertEquals(new CarbonInterval(0, 0, 0, 0, 22, 0, 0), OgameParser::parseOgameTimeInterval('22hod'));
	}

	public function testSlash()
	{
		$this->assertEquals([5, 6], OgameParser::parseSlash('5/6'));
		$this->assertEquals([11, 0], OgameParser::parseSlash('11/0'));
		$this->assertEquals([1, 999], OgameParser::parseSlash('1/999'));
	}

	public function testTemperature()
	{
		$this->assertEquals([42, 82], OgameParser::parseTemperature('42°C až 82°C'));
		$this->assertEquals([220, 260], OgameParser::parseTemperature('220°C až 260°C'));
		$this->assertEquals([3, 43], OgameParser::parseTemperature('3°C až 43°C'));
		$this->assertEquals([-3, 37], OgameParser::parseTemperature('-3°C až 37°C'));
	}

	public function testResources()
	{
		$this->assertEquals(3, OgameParser::parseResources('3'));
		$this->assertEquals(250000, OgameParser::parseResources('250.000'));
		$this->assertEquals(1590000, OgameParser::parseResources('1,59M'));
		$this->assertEquals(360525, OgameParser::parseResources('360.525,287'));   //this weird value appeared once. Is is same as 360.525

	}
}
