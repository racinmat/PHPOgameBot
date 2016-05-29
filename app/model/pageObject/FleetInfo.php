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

	/** @var string */
	private $fleetRow = '#eventContent > tbody > tr';

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
		return $this->getArrivalTimes(self::TYPE_MINE, true);
	}

	public function isAnyAttackOnMe() : bool
	{
		$I = $this->I;
		$this->openFleetInfo();
		$fleetRows = $this->getNumberOfFleets();
		for ($i = 1; $i <= $fleetRows; $i++) {
			if ($I->seeElementExists($this->nthFleet($i, self::TYPE_ENEMY, false))) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @return string[]
	 */
	public function getAttackArrivalTimes() : array
	{
		return $this->getArrivalTimes(self::TYPE_ENEMY, false);
	}

	private function getArrivalTimes(string $type, bool $returning) : array
	{
		$I = $this->I;
		$this->openFleetInfo();
		$fleetRows = $this->getNumberOfFleets();
		$timeStrings = [];
		for ($i = 1; $i <= $fleetRows; $i++) {
			if ( ! $I->seeElementExists($this->nthFleet($i, $type, $returning))) {
				continue;
			}

			$timeStrings[] = $this->getNthFleetArrivalTime($i, $type);
		}
		return $timeStrings;
	}

	private function getNumberOfFleets() : int
	{
		return $this->I->getNumberOfElements($this->fleetRow);
	}

	private function nthFleet(int $nth, string $type, bool $returning = null) : string
	{
		$returnSelector = '';
		if ($returning !== null) {
			$return = $returning ? 'true' : 'false';
			$returnSelector = "[data-return-flight=$return]";
		}
		return "$this->fleetRow:nth-of-type($nth)$returnSelector > td$type";
	}

	private function getNthFleetArrivalTime(int $nth, string $type, bool $returning = null) : string
	{
		return $this->I->grabTextFrom("{$this->nthFleet($nth, $type, $returning)}.countDown");
	}

}