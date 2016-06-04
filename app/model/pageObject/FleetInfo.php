<?php

namespace App\Model\PageObject;
 
use App\Enum\FleetMission;
use App\Enum\FlightStatus;
use App\Enum\MenuItem;
use App\Enum\Ships;
use App\Model\Game\Menu;
use App\Model\ValueObject\Fleet;
use App\Model\ValueObject\Flight;
use App\Utils\ArrayCollection;
use App\Utils\OgameParser;
use App\Utils\Random;
use Carbon\Carbon;
use Nette\Object;
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

	public function __construct(\AcceptanceTester $I, Menu $menu)
	{
		$this->I = $I;
		$this->menu = $menu;
		$this->flights = null;
		$this->flightsLoadTime = Carbon::minValue();
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
			$fleetPopup = '.htmlTooltip > .fleetinfo';
			$rows = $I->getNumberOfElements("$fleetPopup > tr");
			for ($j = 1; $j <= $rows; $j++) {
				if ($I->seeExists('Lodě:', "$fleetPopup > tr:nth-of-type($j) > th")) {
					break;
				}
			}
			$fleetFrom = $j + 1;
			for ($j = 1; $j <= $rows; $j++) {
				if ($I->seeElementExists("$fleetPopup > tr:nth-of-type($j) > td[colspan=\"2\"]")) {
					break;
				}
			}
			$fleetTo = $j - 1;

			$fleet = new Fleet();
			for ($j = $fleetFrom; $j <= $fleetTo; $j++) {
				$shipName = $I->grabTextFrom("$fleetPopup > tr:nth-of-type($j) > td:nth-of-type(1)");
				$amount = $I->grabTextFrom("$fleetPopup > tr:nth-of-type($j) > td:nth-of-type(1)");
				$fleet->addShips(Ships::getFromTranslatedName($shipName), $amount);
			}
			$flight = new Flight($fleet, OgameParser::parseOgameCoordinates($from), OgameParser::parseOgameCoordinates($to), FleetMission::fromNumber($missionNumber), Carbon::now()->add(OgameParser::parseOgameTimeInterval($timeToArrive)), $returning, FlightStatus::_($status));
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
		return $this->flights;
	}

	/**
	 * @return string[]|ArrayCollection
	 */
	public function getMyFleetsReturnTimes() : ArrayCollection
	{
		return $this->getFlights()->filter(function (Flight $f) {
			return $f->getStatus() === FlightStatus::MINE && $f->isReturning();
		})->map($this->flightToArrivalTime());
	}

	/**
	 * @return string[]|ArrayCollection
	 */
	public function getMyExpeditionsReturnTimes() : ArrayCollection
	{
		return $this->getFlights()->filter(function (Flight $f) {
			return $f->getStatus()->getValue() === FlightStatus::MINE && $f->isReturning() && $f->getMission()->getValue() === FleetMission::EXPEDITION;
		})->map($this->flightToArrivalTime());
	}

	private function flightToArrivalTime() : callable
	{
		return function (Flight $f) {
			return $f->getArrivalTime();
		};
	}

	public function isAnyAttackOnMe() : bool
	{
		return ! $this->getFlights()->filter(function (Flight $f) {
			return $f->getStatus()->getValue() === FlightStatus::ENEMY && ! $f->isReturning();
		})->isEmpty();
	}

	/**
	 * @return string[]
	 */
	public function getAttackArrivalTimes() : array
	{
		return ! $this->getFlights()->filter(function (Flight $f) {
			return $f->getStatus()->getValue() === FlightStatus::ENEMY && ! $f->isReturning();
		})->map($this->flightToArrivalTime());
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
	
}