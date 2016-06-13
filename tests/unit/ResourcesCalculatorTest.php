<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Model\ValueObject\Coordinates;
use App\Utils\OgameParser;
use Carbon\CarbonInterval;
use App\Model\Entity\Planet;
use App\Model\Entity\Player;
use App\Model\Queue\Command\BuildDefenseCommand;
use App\Enum\Defense;
use App\Model\ResourcesCalculator;
use Kdyby\Monolog\Logger;
use Carbon\Carbon;
use App\Utils\ArrayCollection;
use App\Model\ValueObject\Resources;
use App\Model\ValueObject\Fleet;
use App\Model\ValueObject\Flight;
use App\Enum\FleetMission;
use App\Enum\FlightStatus;

class ResourcesCalculatorTest extends \PHPUnit_Framework_TestCase
{
	/** @var ResourcesCalculator */
	private $resourcesCalculator;

    protected function setUp()
    {
	    $acceleration = 3;
	    $this->resourcesCalculator = new ResourcesCalculator($acceleration, new Logger(''));
    }

    protected function tearDown()
    {
    }

	public function testTimeProcess()
	{
		Carbon::setTestNow(Carbon::create(2016,1,1,0,0,0));

		$player = new Player('tester', true);
		$planet = new Planet('homeland', new Coordinates(1,1,1), $player);


		$command = BuildDefenseCommand::fromArray([
			'coordinates' => $planet->getCoordinates()->toArray(),
			'data' => [
				'defense' => Defense::ROCKET_LAUNCHER,
			    'amount' => 9
			]
		]);
		$flights = new ArrayCollection();
		//without any flights and mines, the default speed is 90 metal per hour
		$time = $this->resourcesCalculator->getTimeToEnoughResourcesToEnhance($planet, $command, $flights);

		$this->assertEquals(new Resources(18000, 0, 0), $command->getPrice($planet));
		$this->assertEquals(Carbon::now()->addHours(200), $time);
	}

	public function testTimeProcessWithResourcesTransport()
    {
	    Carbon::setTestNow(Carbon::create(2016,1,1,0,0,0));

	    $player = new Player('tester', true);
	    $planet = new Planet('homeland', new Coordinates(1,1,1), $player);


	    $command = BuildDefenseCommand::fromArray([
		    'coordinates' => $planet->getCoordinates()->toArray(),
		    'data' => [
			    'defense' => Defense::ROCKET_LAUNCHER,
			    'amount' => 9
		    ]
	    ]);
	    $flights = new ArrayCollection();
	    $transport = FleetMission::_(FleetMission::TRANSPORT);
	    $status = FlightStatus::_(FlightStatus::MINE);
	    $arrivalTime = Carbon::now()->addHours(1);
	    $flight = new Flight(new Fleet(), new Coordinates(1,1,2), $planet->getCoordinates(), $transport, $arrivalTime, false, $status, new Resources(9000, 0, 0));
	    $flights->add($flight);
	    $arrivalTime2 = Carbon::now()->addHours(101);
	    $flight2 = new Flight(new Fleet(), new Coordinates(1,1,2), $planet->getCoordinates(), $transport, $arrivalTime2, false, $status, new Resources(9000, 0, 0));
	    $flights->add($flight2);

	    $time = $this->resourcesCalculator->getTimeToEnoughResourcesToEnhance($planet, $command, $flights);

	    $this->assertEquals(new Resources(18000, 0, 0), $command->getPrice($planet));
	    $this->assertEquals(Carbon::now()->addHours(100), $time);
    }

}
