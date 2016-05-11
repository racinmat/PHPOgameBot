<?php

namespace App\Model;
 
use App\Enum\Building;
use Nette;

class BuildingManager extends Nette\Object
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
		if (!$this->resourcesCalculator->isEnoughResourcesForBuilding($planet, $building)) {
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
	}

}
