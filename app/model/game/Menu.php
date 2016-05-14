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

	public function goToPage(MenuItem $menuItem)
	{
		$I = $this->I;
		if ($I->seeInCurrentUrlExists($menuItem->getUrlIdentifier())) {
			echo 'i already am on page ' . $menuItem->getValue() . PHP_EOL;
			echo 'current url is: ' . $I->grabFromCurrentUrl() . PHP_EOL;
			return;
		}
		echo 'going to page ' . $menuItem->getValue() . PHP_EOL;
		$I->click($menuItem->getSelector());
		usleep(Random::microseconds(1, 2));
	}
}