<?php

namespace App\Model\Game;
 
use App\Enum\MenuItem;
use App\Utils\Random;
use Nette;
 
class Menu extends Nette\Object
{

	/** @var \AcceptanceTester */
	protected $I;

	public function __construct(\AcceptanceTester $I)
	{
		$this->I = $I;
	}

	public function toToPage(MenuItem $menuItem)
	{
		$I = $this->I;
		if ($I->seeInCurrentUrlExists($menuItem->getUrlIdentifier())) {
			return;
		}
		
		$I->click($menuItem->getSelector());
		usleep(Random::microseconds(1, 2));
	}
}