<?php

namespace App\Model\Game;

use App\Enum\FleetMission;
use App\Enum\MenuItem;
use App\Enum\Ships;
use App\Model\DatabaseManager;
use App\Model\Entity\Planet;
use App\Model\PageObject\FleetInfo;
use App\Model\Queue\Command\IBuildCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\Queue\Command\SendFleetCommand;
use app\model\queue\ICommandProcessor;
use App\Model\ResourcesCalculator;
use App\Utils\OgameParser;
use App\Utils\Random;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Facebook\WebDriver\Exception\TimeOutException;
use Kdyby\Monolog\Logger;
use Nette\Object;

class FleetManager extends Object implements ICommandProcessor
{

	/** @var \AcceptanceTester */
	private $I;

	/** @var PlanetManager */
	private $planetManager;

	/** @var Menu */
	private $menu;

	/** @var Logger */
	private $logger;

	/** @var DatabaseManager */
	private $databaseManager;

	/** @var ResourcesCalculator */
	private $resourcesCalculator;

	/** @var FleetInfo */
	private $fleetInfo;

	public function __construct(\AcceptanceTester $I, PlanetManager $planetManager, Menu $menu, Logger $logger, DatabaseManager $databaseManager, ResourcesCalculator $resourcesCalculator, FleetInfo $fleetInfo)
	{
		$this->I = $I;
		$this->planetManager = $planetManager;
		$this->menu = $menu;
		$this->logger = $logger;
		$this->databaseManager = $databaseManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->fleetInfo = $fleetInfo;
	}

	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof SendFleetCommand;
	}

	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		/** @var SendFleetCommand $command */
		//todo: later add checking for amount of ships in planet from command

		if ($command->getMission() === FleetMission::_(FleetMission::EXPEDITION) && ! $this->areFreeExpeditions()) {
			$minimalTime = OgameParser::getNearestTime($this->fleetInfo->getMyExpeditionsReturnTimes());
		} else {
			$minimalTime = OgameParser::getNearestTime($this->fleetInfo->getMyFleetsReturnTimes());
		}

		if ($command->waitForResources()) {
			$planet = $this->planetManager->getPlanet($command->getCoordinates());
			$timeToResources = $this->resourcesCalculator->getTimeToEnoughResources($planet, $command->getResources());
			$minimalTime = $minimalTime->max($timeToResources);
		}

		return $minimalTime;
	}

	private function areFreeFleets() : bool
	{
		$this->menu->goToPage(MenuItem::_(MenuItem::FLEET));
		$fleets = $this->I->grabTextFrom('#inhalt > div:nth-of-type(2) > #slots > div:nth-of-type(1) > span.tooltip');
		list($occupied, $total) = OgameParser::parseSlash($fleets);
		return $occupied < $total;
	}

	private function areFreeExpeditions() : bool
	{
		$expeditions = $this->I->grabTextFrom('#inhalt > div:nth-of-type(2) > #slots > div:nth-of-type(2) > span.tooltip');
		list($occupied, $total) = OgameParser::parseSlash($expeditions);
		return $occupied < $total;

	}

	public function isProcessingAvailable(SendFleetCommand $command) : bool
	{
		//todo: later add checking for amount of ships in planet from command
		$freeFleets = $this->areFreeFleets();
		$freeExpeditions = $this->areFreeExpeditions();

		if ($command->getMission() === FleetMission::_(FleetMission::EXPEDITION)) {
			$freeFleets = $freeFleets && $freeExpeditions;
		}

		$enoughResources = true;
		if ($command->waitForResources()) {
			$this->logger->addDebug("This fleet wants to wait for resources: {$command->getResources()}.");
			$planet = $this->planetManager->getPlanet($command->getCoordinates());
			$enoughResources = $this->resourcesCalculator->isEnoughResources($planet, $command->getResources());
			$this->logger->addDebug($enoughResources ? 'Enough resources, processing available.' : "Not enough resources, processing unavailable.");
		}
		return $freeFleets && $enoughResources;
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var SendFleetCommand $command */
		return $this->sendFleet($command);
	}

	private function sendFleet(SendFleetCommand $command) : bool
	{
		$this->logger->addInfo("Going to send fleet {$command}.");

		$I = $this->I;

		$to = $command->getTo();

		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$this->menu->goToPlanet($planet);

		if (!$this->isProcessingAvailable($command)) {
			$this->logger->addDebug('Processing not available.');
			return false;
		}
		$this->logger->addDebug('Processing available, starting to process the command.');
		$this->menu->goToPage(MenuItem::_(MenuItem::FLEET));
		foreach ($command->getFleet()->getNonZeroShips() as $ship => $count) {
			if ($I->seeElementExists(Ships::_($ship)->getFleetInputSelector() . ':disabled')) {
				return false;
			}
			$I->fillField(Ships::_($ship)->getFleetInputSelector(), $count);
		}
		$I->click('#continue.on');
		usleep(Random::microseconds(1.5, 2.5));

		$I->fillField('input#galaxy', $to->getGalaxy());
		$I->fillField('input#system', $to->getSystem());
		if ($command->getMission() === FleetMission::_(FleetMission::EXPEDITION)) {
			$I->fillField('input#position', 16);
		} else {
			$I->fillField('input#position', $to->getPlanet());
		}
		$this->logger->addDebug('Filled coordinates.');
		usleep(Random::microseconds(0.5, 1));

		$currentUrl = $I->grabFromCurrentUrl();
		$I->click('#continue.on');

		$this->logger->addDebug('Going to select mission, clicked on continue button.');

		try {
			$I->waitForText('Odeslání letky III', 4, '#planet > h2');
		} catch(TimeOutException $e) {
			$this->logger->addDebug('Url is still same. Can not proceed to next phase of fleet sending. Throwing exception.');
			throw new NonExistingPlanetException();
		}

		usleep(Random::microseconds(1.5, 2.5));

		do {
			$I->click($command->getMission()->getMissionSelector());
			$this->logger->addDebug('Selected mission.');
			usleep(Random::microseconds(1, 2));
		} while ( ! $I->seeElementExists('#start.on'));

		if ( ! $command->getResources()->isZero()) {
			$I->fillField('input#metal', $command->getResources()->getMetal());
			$I->fillField('input#crystal', $command->getResources()->getCrystal());
			$I->fillField('input#deuterium', $command->getResources()->getDeuterium());
		}

		$I->click('#start.on');
		$this->logger->addDebug('Fleet sent.');
		usleep(Random::microseconds(1.5, 2.5));

		return true;
	}
}

class NonExistingPlanetException extends \Exception {}