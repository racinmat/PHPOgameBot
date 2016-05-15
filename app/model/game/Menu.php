<?php

namespace App\Model\Game;
 
use App\Enum\MenuItem;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Coordinates;
use App\Utils\OgameParser;
use App\Utils\Random;
use Kdyby\Monolog\Logger;
use Nette\Object;
use Nette\Utils\Strings;

class Menu extends Object
{

	/** @var \AcceptanceTester */
	protected $I;

	/** @var Logger */
	protected $logger;

	public function __construct(\AcceptanceTester $I, Logger $logger)
	{
		$this->I = $I;
		$this->logger = $logger;
	}

	public function goToPage(MenuItem $menuItem)
	{
		$I = $this->I;
		$this->logger->addDebug("Going to page {$menuItem->getValue()}.");
		if ($I->seeInCurrentUrlExists($menuItem->getUrlIdentifier())) {
			$this->logger->addDebug("I already am on requested page, current URL is {$I->grabFromCurrentUrl()}.");
			return;
		}
		$this->logger->addDebug("Clicking to go to another page.");
		$I->click($menuItem->getSelector());
		usleep(Random::microseconds(1, 2));
	}

	public function goToPlanet(Planet $planet)
	{
		$I = $this->I;
		$currentCoordinates = $this->getCurrentPlanetCoordinates();
		$this->logger->addDebug("Going to planet {$planet->getCoordinates()->toValueObject()->toString()}.");
		if ($planet->isOnCoordinates($currentCoordinates)) {
			$this->logger->addDebug("I already am on requested coordinates.");
			return;
		}
		$this->logger->addDebug("Clicking to go to another planet.");
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