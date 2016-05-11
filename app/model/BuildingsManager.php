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
		$I = $this->I;
		$I->click($building->getMenuLocation());
		$I->click($building->getSelector());
		$I->click($building->getBuildButtonSelector());
	}

}
