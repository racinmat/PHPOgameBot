<?php

namespace App\Model\PageObject;
 
use App\Enum\MenuItem;
use Nette;
 
class Menu extends Nette\Object
{

	/** @var \AcceptanceTester */
	private $I;

	public function __construct(\AcceptanceTester $I)
	{
		$this->I = $I;
	}

	public function goTo(MenuItem $menuItem)
	{
		$I = $this->I;
		$I->click($menuItem->getSelector());

	}

}