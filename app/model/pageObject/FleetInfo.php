<?php

namespace App\Model\PageObject;
 
use App\Enum\FleetMission;
use App\Enum\FlightStatus;
use App\Enum\MenuItem;
use App\Enum\Ships;
use App\Model\Entity\Planet;
use App\Model\Game\Menu;
use App\Model\ValueObject\Fleet;
use App\Model\ValueObject\Flight;
use App\Model\ValueObject\Resources;
use App\Utils\ArrayCollection;
use App\Utils\Functions;
use App\Utils\OgameParser;
use App\Utils\Random;
use Carbon\Carbon;

use Kdyby\Monolog\Logger;
use Nette\Object;
use Nette\Utils\Json;
use Nette\Utils\Strings;


class FleetInfo extends Object
{

	const
		TYPE_MINE = '.friendly',
		TYPE_FRIENDLY = '.neutral',
		TYPE_ENEMY = '.hostile'
	;

	/** @var \AcceptanceTester */
	private $I;

	/** @var Menu */
	private $menu;

	/** @var string */
	private $fleetRow = '#eventContent > tbody > tr';

	/** @var ArrayCollection */
	private $flights;

	/** @var Carbon */
	private $flightsLoadTime;

	/** @var Logger */
	private $logger;

	public function __construct(\AcceptanceTester $I, Menu $menu, Logger $logger)
	{
		$this->I = $I;
		$this->menu = $menu;
		$this->logger = $logger;
		$this->flights = null;
		$this->flightsLoadTime = Carbon::minValue();
	}

	public function reloadFlights()
	{
		$this->flights = null;
	} 
	
	private function openFleetInfo()
	{
		$I = $this->I;
		$this->menu->goToPage(MenuItem::_(MenuItem::FLEET));
		usleep(Random::microseconds(1.5, 2.5));
		if ($I->seeElementExists('#js_eventDetailsClosed')) {   //element can be seen only when nobody clicked on it, then it disappears
			$I->click('#js_eventDetailsClosed');
		}
		$I->waitForText('Události', null, '#eventHeader h2');
	}

	private function initialize()
	{
		$this->flights = new ArrayCollection();
		if ($this->isNoFleetCurrentlyActive()) {
			$this->logger->addDebug("No fleet is currently active. Flights are empty.");
			return;
		}

		$I = $this->I;
		$this->openFleetInfo();
		$fleetRows = $this->getNumberOfFlights();
		for ($i = 1; $i <= $fleetRows; $i++) {
			$row = $this->getRowSelector($i);

			$timeToArrive = $I->grabTextFrom("$row > td.countDown");
			$returningString = $I->grabAttributeFrom($row, 'data-return-flight');
			$status = $I->grabAttributeFrom("$row > td.countDown", 'class');
			$from = $I->grabTextFrom("$row > td.coordsOrigin");
			$to = $I->grabTextFrom("$row > td.destCoords");
			$missionNumber = $I->grabAttributeFrom($row, 'data-mission-type');

			$returning = $returningString === 'true' ? true : false;
			$status = Strings::replace($status, '~countDown|textBeefy|\s+~', '');

			$I->moveMouseOver("$row > td[class^=\"icon_movement\"] > .tooltip");
			$fleetPopup = "//body//div[@class=\"t_Tooltip t_Tooltip_cloud\" and contains(@style, \"z-index: 1000000\")]//div[@class=\"htmlTooltip\"]//table[@class=\"fleetinfo\"]/tbody";
			$I->waitForElementVisible($fleetPopup);
			$rows = $I->getNumberOfElements("$fleetPopup/tr");
			for ($j = 1; $j <= $rows; $j++) {
				if ($I->seeExists('Lodě:', "$fleetPopup/tr[$j]/th")) {
					break;
				}
			}
			$fleetFrom = $j + 1;
			for ($j = 1; $j <= $rows; $j++) {
				if ($I->seeExists('Dodávka:', "$fleetPopup/tr[$j]/th")) {
					break;
				}
			}
			$fleetTo = $j - 2;
			$resourcesRow = $j + 1;

			$fleet = new Fleet();
			for ($j = $fleetFrom; $j <= $fleetTo; $j++) {
				$shipName = $I->grabTextFrom("$fleetPopup/tr[$j]/td[1]");
				$amount = $I->grabTextFrom("$fleetPopup/tr[$j]/td[2]");

				$shipName = Strings::replace($shipName, '~:~', '');
				$fleet->addShips(Ships::_(Ships::getFromTranslatedName($shipName)), $amount);
			}

			$metal = $I->grabTextFrom("$fleetPopup/tr[$resourcesRow]/td[2]");
			$resourcesRow++;
			$crystal = $I->grabTextFrom("$fleetPopup/tr[$resourcesRow]/td[2]");
			$resourcesRow++;
			$deuterium = $I->grabTextFrom("$fleetPopup/tr[$resourcesRow]/td[2]");

			$resources = new Resources(OgameParser::parseResources($metal), OgameParser::parseResources($crystal), OgameParser::parseResources($deuterium));

			/** @var Carbon $arrivalTime */
			$arrivalTime = Carbon::now()->add(OgameParser::parseOgameTimeInterval($timeToArrive));
			$flight = new Flight($fleet, OgameParser::parseOgameCoordinates($from), OgameParser::parseOgameCoordinates($to), FleetMission::fromNumber($missionNumber), $arrivalTime, $returning, FlightStatus::fromClass($status), $resources);
			$this->logger->addDebug('Done parsing flight: ' . Json::encode($flight->toArray()));
			$this->flights->add($flight);
		}
		$this->flightsLoadTime = Carbon::now();
	}

	private function getFlights() : ArrayCollection
	{
		if (Carbon::now()->subMinutes(3)->gt($this->flightsLoadTime)) { //after 3 minutes the flights will be reloaded
			$this->flights = null;
		}
		if ($this->flights === null) {
			$this->initialize();
		}
		$this->logger->addDebug("Flights: " . Json::encode($this->flights->map(function (Flight $f) {return $f->toArray();})->toArray()));
		return $this->flights;
	}

	/**
	 * @return Carbon[]|ArrayCollection
	 */
	public function getMyFleetsReturnTimes() : ArrayCollection
	{
		return $this->getFlights()->filter(Flight::myReturning())->map(Flight::toArrivalTime());
	}

	/**
	 * @return Carbon[]|ArrayCollection
	 */
	public function getMyExpeditionsReturnTimes() : ArrayCollection
	{
		$arrivalTimes = $this->getFlights()->filter(Flight::myReturning())->filter(Flight::withMission(FleetMission::_(FleetMission::EXPEDITION)))->map(Flight::toArrivalTime());
		$this->logger->addDebug('Return times of my expeditions are ' . Json::encode($arrivalTimes->toArray()));
		return $arrivalTimes;
	}

	public function isAnyAttackOnMe() : bool
	{
		return ! $this->getFlights()->filter(Flight::incomingAttacks())->isEmpty();
	}

	public function getNearestAttackTime() : Carbon
	{
		return $this->getFlights()->filter(Flight::incomingAttacks())->map(Flight::toArrivalTime())->sort(Functions::compareCarbonDateTimes())->first() ?: Carbon::maxValue();
	}

	public function getNearestAttackFlight() : Flight
	{
		$compare = Functions::compareCarbonDateTimes();
		return $this->getFlights()->filter(Flight::incomingAttacks())->sort(function(Flight $a, Flight $b) use ($compare) {
			return $compare($a->getArrivalTime(), $b->getArrivalTime());
		})->first();
	}

	private function getRowSelector(int $nth) : string
	{
		return "$this->fleetRow:nth-of-type($nth)";
	}

	private function getNumberOfFlights() : int
	{
		return $this->I->getNumberOfElements($this->fleetRow);
	}

	private function isNoFleetCurrentlyActive() : bool
	{
		return $this->I->seeElementExists('#eventboxBlank');
	}

	public function getTimeOfFleetReturn(Fleet $fleet, Planet $planet) : Carbon
	{
		$myReturning = $this->getFlights()->filter(Flight::myReturning());
		$this->logger->addDebug('My returning flights are ' . Json::encode($myReturning->map(function (Flight $f) {return $f->toArray();})->toArray()));
		$myFleetReturning = $myReturning->filter(Flight::withFleet($fleet));
		$this->logger->addDebug('Flights with fleet: ' . Json::encode($fleet->toArray()) . ' are ' . Json::encode($myFleetReturning->map(function (Flight $f) {return $f->toArray();})->toArray()));
		$arrivalTimes = $myFleetReturning->filter(Flight::fromPlanet($planet))->map(Flight::toArrivalTime())->sort(Functions::compareCarbonDateTimes());
		$this->logger->addDebug('Return times of ' . Json::encode($fleet->toArray()) . ' are ' . Json::encode($arrivalTimes->map(function (Carbon $c) {return $c->__toString();})->toArray()));
		return $arrivalTimes->first() ?: Carbon::maxValue();//when fleet is not returning yet, the $arrivalTimes are empty collection
	}

}