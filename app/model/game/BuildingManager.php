<?php

namespace App\Model\Game;
 
use App\Enum\Building;
use App\Enum\MenuItem;
use App\Enum\Upgradable;
use App\Model\Entity\Planet;
use App\Model\ResourcesCalculator;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Nette\Object;
use Nette\Utils\Strings;

class BuildingManager extends Object
{

	/** @var \AcceptanceTester */
	private $I;

	/** @var ResourcesCalculator */
	private $resourcesCalculator;

	/** @var PlanetManager */
	private $planetManager;

	public function __construct(\AcceptanceTester $I, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator)
	{
		$this->I = $I;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
	}

	/**
	 * @param Upgradable $upgradable
	 * @return bool returns true when building was built, otherwise returns false
	 */
	public function upgrade(Upgradable $upgradable) : bool
	{
		//možná refreshnout všechna data hned po zalogování
		$this->planetManager->refreshResourceData();
		$planet = $this->planetManager->getMyHomePlanet();
		if (!$this->canBuild($planet, $upgradable)) {
			return false;
		}
		$this->openMenu($upgradable);
		$I = $this->I;
		$I->click($upgradable->getBuildButtonSelector());
		$I->wait(1);
		return true;
	}

	private function openMenu(Upgradable $upgradable)
	{
		$I = $this->I;
		$I->click($upgradable->getMenuLocation()->getSelector());
		$I->click($upgradable->getSelector());
		$I->wait(1);
	}

	private function canBuild(Planet $planet, Building $building) : bool 
	{
		return $this->resourcesCalculator->isEnoughResourcesForBuilding($planet, $building) && ! $this->currentlyUpgradingBuilding();
	}

	private function currentlyUpgradingBuilding() : bool
	{
		return ! $this->I->seeExists('Nestaví se žádné budovy.', 'table.construction.active');
	}

	public function getTimeToUpgradeAvailable(Planet $planet, Building $building) : Carbon
	{
		$datetime1 = $this->resourcesCalculator->getTimeToEnoughResourcesForBuilding($planet, $building);
		$datetime2 = $this->getTimeToFinishUpgrade();
		return $datetime1->max($datetime2);
	}

	private function getTimeToFinishUpgrade() : Carbon
	{
		$I = $this->I;
		$I->click(MenuItem::_(MenuItem::OVERVIEW));
		$I->wait(1);
		if ($I->seeElementExists('table.construction.active #Countdown')) {
			$interval = $I->grabTextFrom('table.construction.active #Countdown');
			return Carbon::now()->add($this->parseOgameTimeInterval($interval));
		}
		return Carbon::now();
	}

	private function parseOgameTimeInterval(string $interval) : CarbonInterval
	{
		$params = Strings::match($interval, '~((?<weeks>\d{1,2})t)? ?((?<days>\d{1,2})d)? ?((?<hours>\d{1,2})hod)? ?((?<minutes>\d{1,2})min)? ?((?<seconds>\d{1,2})s)?~');
		return new CarbonInterval(0, 0, $params['weeks'], $params['days'], $params['hours'], $params['minutes'], $params['seconds']);
	}
}
