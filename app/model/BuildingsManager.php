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

	public function build(Building $building)
	{
		$this->openBuildingMenu($building);
		$I = $this->I;
		$I->click($building->getBuildButtonSelector());
	}

	/**
	 * @param Building $building
	 * @return bool
	 */
	public function isEnoughResources(Building $building)
	{
		$this->openBuildingMenu($building);
		$I = $this->I;
		return $I->seeElementExists($building->getBuildButtonSelector());
	}

	private function openBuildingMenu(Building $building)
	{
		$I = $this->I;
		$I->click($building->getMenuLocation());
		$I->click($building->getSelector());
	}
}
