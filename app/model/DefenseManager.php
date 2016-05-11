<?php

namespace App\Model;
 
use App\Enum\Building;
use App\Enum\Defense;
use Nette;

class DefenseManager extends Nette\Object
{

	/** @var \AcceptanceTester */
	private $I;

	/** @var PlanetManager */
	private $planetManager;
	
	/** @var ResourcesCalculator */
	private $resourcesCalculator;

	/**
	 * DefenseManager constructor.
	 * @param \AcceptanceTester $I
	 * @param PlanetManager $planetManager
	 * @param ResourcesCalculator $resourcesCalculator
	 */
	public function __construct(\AcceptanceTester $I, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator)
	{
		$this->I = $I;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
	}
	
	/**
	 * @param Defense $defense
	 * @param int $amount
	 * @return bool returns true when building was built, otherwise returns false
	 */
	public function build(Defense $defense, int $amount) : bool
	{
		//možná refreshnout všechna data hned po zalogování
		$this->planetManager->refreshResourceData();
		$planet = $this->planetManager->getMyHomePlanet();
		if (!$this->resourcesCalculator->isEnoughResourcesForBuilding($planet, $building, $building->getCurrentLevel($planet))) {
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
