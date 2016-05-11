<?php

namespace App\Model;
 
use App\Enum\Building;
use App\Model\Entity\Planet;
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
	 * @param Building $building
	 * @return bool returns true when building was built, otherwise returns false
	 */
	public function upgrade(Building $building) : bool
	{
		//možná refreshnout všechna data hned po zalogování
		$this->planetManager->refreshResourceData();
		$planet = $this->planetManager->getMyHomePlanet();
		if (!$this->canBuild($planet, $building)) {
			return false;
		}
		$this->openBuildingMenu($building);
		$I = $this->I;
		$I->click($building->getUpgradeButtonSelector());
		$I->wait(1);
		return true;
	}

	private function openBuildingMenu(Building $building)
	{
		$I = $this->I;
		$I->click($building->getMenuLocation()->getSelector());
		$I->click($building->getSelector());
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
		if ($I->seeElementExists('table.construction.active #Countdown')) {
			$interval = $I->grabTextFrom('table.construction.active #Countdown');
			return Carbon::now()->add($this->parseOgameTimeInterval($interval));
		}
		return Carbon::now();
	}

	private function parseOgameTimeInterval(string $interval) : CarbonInterval
	{
		$params = Strings::match($interval, '~((?<minutes>\d{1,2})min)? ?((?<seconds>\d{1,2})s)?~');//todo: dodat hodiny až zjistím formát
		return new CarbonInterval(0, 0, 0, 0, 0, $params['minutes'], $params['seconds']);
	}
}
