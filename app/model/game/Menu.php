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
		$this->logger->addDebug("Clicking to go to requested page.");
		if ($I->seeElementExists('#errorPageContainer > #errorTitle > #errorTitleText')) {  //firefox error page with message "Secure Connection Failed"
			$I->reloadPage();
		}
		$I->click($menuItem->getSelector());
		$I->waitForElementVisible('body');
		usleep(Random::microseconds(1.5, 2));
	}

	public function goToPlanet(Planet $planet)
	{
		$I = $this->I;
		$currentCoordinates = $this->getCurrentPlanetCoordinates();
		$this->logger->addDebug("Going to planet {$planet->getCoordinates()->toString()}.");
		if ($planet->isOnCoordinates($currentCoordinates)) {
			$this->logger->addDebug("I already am on requested coordinates.");
			return;
		}
		$this->logger->addDebug("Clicking to go to requested planet.");
		$I->click($planet->getCoordinates()->toString(), '#planetList');
		$I->waitForText($planet->getCoordinates()->toString(), null, '.planetlink.active span.planet-koords');    //waiting for the requested coordnates to be on active planet
		usleep(Random::microseconds(0.5, 1));
	}

	private function getCurrentPlanetCoordinates() : Coordinates
	{
		$I = $this->I;
		$I->waitForElementVisible('#planetList .planetlink.active span.planet-koords');
		$text = $I->grabTextFrom('#planetList .planetlink.active span.planet-koords');
		return OgameParser::parseOgameCoordinates($text);
	}
	
}
