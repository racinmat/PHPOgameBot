<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Model\ValueObject\Coordinates;
use App\Utils\OgameParser;
use Carbon\CarbonInterval;
use App\Utils\Strings;

class StringsTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testExtractParametersFromUrl()
    {
	    $this->assertEquals(['q' => 'bar'], Strings::extractParametersFromUrl('http://regexr.com/foo.html?q=bar'));
	    $this->assertEquals([
		    'page' => 'fleet1',
		    'galaxy' => '1',
		    'system' => '24',
		    'position' => '8',
		    'type' => '1',
		    'mission' => '1',
		    'am203' => '2'
	    ], Strings::extractParametersFromUrl('https://s124-cz.ogame.gameforge.com/game/index.php?page=fleet1&galaxy=1&system=24&position=8&type=1&mission=1&am203=2'));
	    $this->assertEquals(new Coordinates(5,22,13), OgameParser::parseOgameCoordinates('[5:22:13]'));
	    $this->assertEquals(new Coordinates(5,22,1), OgameParser::parseOgameCoordinates('[5:22:1]'));
    }

}
