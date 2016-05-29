<?php

namespace App\Model\PageObject;
 
use App\Enum\MenuItem;
use App\Model\Game\Menu;
use App\Utils\OgameParser;
use App\Utils\Random;
use Carbon\Carbon;
use Nette\Object;

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

	public function __construct(\AcceptanceTester $I, Menu $menu)
	{
		$this->I = $I;
		$this->menu = $menu;
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

	/**
	 * @return string[]
	 */
	public function getMyFleetsReturnTimes() : array
	{
		$I = $this->I;
		$this->openFleetInfo();
		$fleetRows = $this->getNumberOfFleets();
		$timeStrings = [];
		for ($i = 1; $i <= $fleetRows; $i++) {
			//I want only returning flights
			if ( ! $I->seeElementExists($this->nthFleet($i, self::TYPE_MINE), ['data-return-flight' => 'true'])) {
				continue;
			}

			$timeStrings[] = $this->getNthFleetArrivalTime($i, self::TYPE_MINE);
		}
		return $timeStrings;
	}

	public function isAnyAttackOnMe() : bool
	{
		$I = $this->I;
		$this->openFleetInfo();
		$fleetRows = $this->getNumberOfFleets();
		for ($i = 1; $i <= $fleetRows; $i++) {
			if ($I->seeElementExists($this->nthFleet($i, self::TYPE_ENEMY), ['data-return-flight' => 'false'])) {
				return true;
			}
		}
		return false;
	}

	public function getAttackArrivalTimes() : string
	{
		$I = $this->I;
		$this->openFleetInfo();
		$fleetRows = $this->getNumberOfFleets();
		for ($i = 1; $i <= $fleetRows; $i++) {
			if ($I->seeElementExists($this->nthFleet($i, self::TYPE_ENEMY), ['data-return-flight' => 'false'])) {
				return true;
			}
		}
		return false;
	}

	private function getNumberOfFleets() : int
	{
		return $this->I->getNumberOfElements('#eventContent > tbody > tr');
	}

	private function nthFleet(int $nth, string $type) : string
	{
		return "#eventContent > tbody > tr:nth-of-type($nth) > td$type";
	}

	private function getNthFleetArrivalTime(int $nth, string $type) : string
	{
		return $this->I->grabTextFrom($this->nthFleet($nth, $type) . ".countDown");
	}

}