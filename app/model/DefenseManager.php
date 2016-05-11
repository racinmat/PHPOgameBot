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
		if (!$this->resourcesCalculator->isEnoughResourcesForDefense($planet, $defense, $amount)) {
			return false;
		}
		$this->openDefenseMenu($defense);
		$I = $this->I;
		$I->fillField('#number', $amount);
		$I->wait(1);
		$I->click($defense->getBuildButtonSelector());
		$I->wait(1);
		return true;
	}

	private function openDefenseMenu(Defense $defense)
	{
		$I = $this->I;
		$I->click($defense->getMenuLocation()->getSelector());
		$I->click($defense->getSelector());
		$I->wait(1);
	}
	
}
