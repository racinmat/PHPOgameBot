<?php

namespace App\Model\Game;
 
use App\Enum\MenuItem;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Coordinates;
use App\Utils\OgameParser;
use App\Utils\Random;
use Nette\Object;
use Nette\Utils\Strings;

class Menu extends Object
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

	public function goToPlanet(Planet $planet)
	{
		$I = $this->I;
		$currentCoordinates = $this->getCurrentPlanetCoordinates();
		if ($planet->isOnCoordinates($currentCoordinates)) {
			echo 'i already am on coordinates' . PHP_EOL;
			return;
		}
		echo 'going to coordinates' . PHP_EOL;
		$I->click($planet->getCoordinates()->toValueObject()->__toString());
		usleep(Random::microseconds(1, 2));
	}

	private function getCurrentPlanetCoordinates() : Coordinates
	{
		$I = $this->I;
		$text = $I->grabTextFrom('.planetlink.active span.planet-koords');
		return OgameParser::parseOgameCoordinates($text);
	}
	
}