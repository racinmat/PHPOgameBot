<?php

namespace App\Model;
 
use App\Enum\Building;
use Nette;

class BuildingsManager extends Nette\Object
{

	/** @var \AcceptanceTester */
	private $I;

	public function __construct(\AcceptanceTester $I)
	{
		$this->I = $I;
	}

	/**
	 * @param Building $building
	 * @return bool returns true when building was built, otherwise returns false
	 */
	public function build(Building $building) : bool
	{
		if (!$this->isEnoughResources($building)) {
			return false;
		}
		$this->openBuildingMenu($building);
		$I = $this->I;
		$I->click($building->getBuildButtonSelector());
		$I->wait(1);
		return true;
	}

	/**
	 * @param Building $building
	 * @return bool
	 */
	public function isEnoughResources(Building $building)
	{
		$this->openBuildingMenu($building);
		$this->I->waitForElementVisible('#detail');
		return $this->I->seeElementExists($building->getBuildButtonSelector());
	}

	private function openBuildingMenu(Building $building)
	{
		$I = $this->I;
		$I->click($building->getMenuLocation()->getSelector());
		$I->click($building->getSelector());
	}
}
