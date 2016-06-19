<?php

namespace App\Model\Game;
 
use App\Enum\MenuItem;
use App\Enum\PlayerStatus;
use App\Model\DatabaseManager;
use App\Model\Entity\Planet;

use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\ScanGalaxyCommand;
use App\Model\Queue\ICommandProcessor;
use App\Model\ValueObject\Coordinates;
use App\Utils\ArrayCollection;
use App\Utils\Functions;
use App\Utils\OgameParser;
use App\Utils\Random;
use Carbon\Carbon;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\WebDriverKeys;
use Kdyby\Monolog\Logger;
use Nette\Object;
use Nette\Utils\Strings;

class GalaxyBrowser extends Object implements ICommandProcessor
{

	/** @var \AcceptanceTester */
	private $I;

	/** @var Menu */
	private $menu;

	/** @var PlanetManager */
	private $planetManager;

	/** @var DatabaseManager */
	private $databaseManager;

	/** @var Logger */
	private $logger;

	/** @var SignManager */
	private $signManager;

	public function __construct(\AcceptanceTester $I, Menu $menu, PlanetManager $planetManager, DatabaseManager $databaseManager, Logger $logger, SignManager $signManager)
	{
		$this->I = $I;
		$this->menu = $menu;
		$this->planetManager = $planetManager;
		$this->databaseManager = $databaseManager;
		$this->logger = $logger;
		$this->signManager = $signManager;
	}

	protected function scanGalaxy(ScanGalaxyCommand $command)
	{
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$this->menu->goToPlanet($planet);
		$this->menu->goToPage(MenuItem::_(MenuItem::GALAXY));
		$from = $command->getFrom();
		$to = $command->getTo();

		$this->logger->addInfo("Going to scan galaxy from system {$from->toString()} to system {$to->toString()}.");
		for ($i = $from; $i->isLesserThanOrEquals($to); $i = $i->nextSystem()) {
			$this->scanSystem($i, $planet);
		}

	}

	protected function scanSystem(Coordinates $coordinates, Planet $planet)
	{
		$I = $this->I;

		$currentGalaxy = (int) $I->grabValueFrom('#galaxy_input', $coordinates->getGalaxy());
		$currentSystem = (int) $I->grabValueFrom('#system_input', $coordinates->getSystem());
		$isNextSystem = $currentGalaxy === $coordinates->getGalaxy() && ($currentSystem + 1) === $coordinates->getSystem();

		//sometimes it just logs me out after ~100 systems scanned
		try {
			if ($isNextSystem) {
				$this->goToNextSystem();
			} else {
				$this->goToSystem($coordinates);
			}
		} catch(TimeOutException $e) {
			$this->logger->addDebug('Stucked on loading. Forcing page refresh.');
			$I->reloadPage();
			if ( ! $I->seeInCurrentUrlExists(MenuItem::_(MenuItem::GALAXY)->getUrlIdentifier())) {
				$this->logger->addDebug('Signed out automatically. Signing in and continuing in scanning.');
				$this->signManager->signIn();
				$this->menu->goToPlanet($planet);
				$this->menu->goToPage(MenuItem::_(MenuItem::GALAXY));
			}
			$this->goToSystem($coordinates);    //sometimes refresh after getting stuck does not log me out. In that case, I only go to desired system without login
		}
		
		$this->readPlanetsInfo($coordinates);
	}

	private function readPlanetsInfo(Coordinates $coordinates)
	{
		$I = $this->I;
		$myPlanetsCoordinates = $this->databaseManager->getAllMyPlanetsCoordinates();
		$planetCount = Coordinates::$maxPlanet;
		$planetsInSystem = new ArrayCollection();
		for ($i = 1; $i <= $planetCount; $i++) {
			$planetCoordinates = $coordinates->planet($i);
			//check to avoid parsing empty spots
			if ( ! $I->seeElementExists("tbody tr:nth-of-type($i) > td.colonized")) {
				continue;
			}

			$planetsInSystem->add($planetCoordinates->getPlanet());
			//checking my planet, do not waste time with it. Status of my planets is missing
			if ($myPlanetsCoordinates->exists(Functions::equalCoordinates($planetCoordinates))) {
				continue;
			}

			$planetName = $I->grabTextFrom("tbody tr:nth-of-type($i) .planetname");
			$playerName = $I->grabTextFrom("tbody tr:nth-of-type($i) .playername > a > span");
			$playerStatusClass = $I->grabAttributeFrom("tbody tr:nth-of-type($i) .playername > a > span", 'class');
			if ($I->seeElementExists("tbody tr:nth-of-type($i) .allytagwrapper")) {
				$alliance = $I->grabTextFrom("tbody tr:nth-of-type($i) .allytagwrapper");
			} else {
				$alliance = null;
			}
			$hasDebris = ! $I->seeElementExists("tbody tr:nth-of-type($i) .debris.js_no_action");
			$hasMoon = ! $I->seeElementExists("tbody tr:nth-of-type($i) .moon.js_no_action");

			$playerStatus = PlayerStatus::fromClass($playerStatusClass);
			if ($hasDebris) {
				$I->moveMouseOver("tbody tr:nth-of-type($i) .debris");
				usleep(Random::microseconds(0.5, 1));
				$debrisMetalString = $I->grabTextFrom("#debris$i .ListLinks li:nth-of-type(1)");
				$debrisCrystalString = $I->grabTextFrom("#debris$i .ListLinks li:nth-of-type(2)");
				$debrisMetal = OgameParser::parseResources(Strings::split($debrisMetalString, '~:~')[1]);
				$debrisCrystal = OgameParser::parseResources(Strings::split($debrisCrystalString, '~:~')[1]);
			} else {
				$debrisMetal = 0;
				$debrisCrystal = 0;
			}

			$player = $this->databaseManager->getPlayer($playerName);
			if ($player === null) {
				$player = $this->databaseManager->addPlayer($playerName);
			}
			$planet = $this->databaseManager->getPlanet($planetCoordinates);
			if ($planet === null) {
				$planet = $this->databaseManager->addPlanet($planetCoordinates, $player);
			}

			$player->setStatus($playerStatus);
			$player->setAlliance($alliance);
			$player->setLastVisited(Carbon::now());

			$planet->setName($planetName);
			$planet->setMoon($hasMoon);
			$planet->setDebrisMetal($debrisMetal);
			$planet->setDebrisCrystal($debrisCrystal);
			$planet->setLastVisited(Carbon::now());

		}
		$this->databaseManager->flush();
		$this->databaseManager->removePlanetsInSystemExceptOf($coordinates, $planetsInSystem);
	}

	protected function goToNextSystem()
	{
		$I = $this->I;
		$I->pressKey('body', WebDriverKeys::ARROW_RIGHT);
		$I->waitForElementNotVisible('#galaxyLoading', 15);
	}

	protected function goToSystem(Coordinates $coordinates)
	{
		$I = $this->I;

		$I->fillField('#galaxy_input', $coordinates->getGalaxy());
		$I->fillField('#system_input', $coordinates->getSystem());
		$I->click('#galaxyHeader > form > div:nth-child(9)');
		$I->waitForElementNotVisible('#galaxyLoading', 15);
	}

	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof ScanGalaxyCommand;
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var ScanGalaxyCommand $command */
		$this->scanGalaxy($command);
		return true;
	}

	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		return Carbon::now();
	}

	public function isProcessingAvailable(ICommand $command) : bool
	{
		return true;
	}
	
}