<?php

namespace App\Model\Game;

use App\Enum\FleetMission;
use App\Enum\MenuItem;
use App\Enum\PlayerStatus;
use App\Enum\Ships;
use App\Model\DatabaseManager;

use App\Model\PageObject\FleetInfo;

use App\Model\Queue\Command\ICommand;


use App\Model\Queue\Command\SendFleetCommand;
use App\Model\Queue\ICommandProcessor;
use App\Model\ResourcesCalculator;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\Fleet;
use App\Utils\Functions;
use App\Utils\OgameParser;
use App\Utils\Random;
use App\Utils\Strings;
use Carbon\Carbon;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeOutException;
use Kdyby\Monolog\Logger;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Object;
use Ramsey\Uuid\Uuid;

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

	/** @var Cache */
	private $cache;

	/**
	 * Used to save one redundant reload during batch sending.
	 * @var bool
	 */
	private $skipReload;

	/** @var SignManager */
	private $signManager;

	public function __construct(\AcceptanceTester $I, PlanetManager $planetManager, Menu $menu, Logger $logger, DatabaseManager $databaseManager, ResourcesCalculator $resourcesCalculator, FleetInfo $fleetInfo, IStorage $storage, SignManager $signManager)
	{
		$this->I = $I;
		$this->planetManager = $planetManager;
		$this->menu = $menu;
		$this->logger = $logger;
		$this->databaseManager = $databaseManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->fleetInfo = $fleetInfo;
		$this->cache = new Cache($storage, 'processedFlights');
		$this->skipReload = false;
		$this->signManager = $signManager;
	}

	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof SendFleetCommand;
	}

	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		/** @var SendFleetCommand $command */
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$this->menu->goToPlanet($planet);
		$this->menu->goToPage(MenuItem::_(MenuItem::FLEET));

		$minimalTime = Carbon::now();
		if ($command->getMission() === FleetMission::_(FleetMission::EXPEDITION) && ! $this->areFreeExpeditions()) {
			$minimalTime = $this->fleetInfo->getMyExpeditionsReturnTimes()->sort(Functions::compareCarbonDateTimes())->first(); //when expedition is not returning yet, the getMyFleetsReturnTimes() returns empty collection
			$this->logger->addDebug("Minimal time for expedition is $minimalTime.");
		}
		
		if ( ! $this->areFreeFleets()) {
			$minimalFleetTime = $this->fleetInfo->getMyFleetsReturnTimes()->sort(Functions::compareCarbonDateTimes())->first();
			$this->logger->addDebug("Minimal time for fleet is $minimalFleetTime.");
			$minimalTime = $minimalTime->max($minimalFleetTime); //when fleet is not returning yet, the getMyFleetsReturnTimes() returns empty collection
		}

		if ( ! $this->isFleetPresent($command)) {
			$missingShips = $command->getFleet()->subtract($this->getPresentFleet($command->getCoordinates()));
			$timeToFleet = $this->fleetInfo->getTimeOfFleetReturn($missingShips, $planet);
			$minimalTime = $minimalTime->max($timeToFleet);
		}

		if ($command->waitForResources()) {
			$planet = $this->planetManager->getPlanet($command->getCoordinates());
			if ($this->isCapacitySufficient($command)) {
				$timeToResources = $this->resourcesCalculator->getTimeToEnoughResources($planet, $command->getResources());
			} else {        //send all the resources that fleet can carry instead of resources in command
				$this->logger->addDebug("Calculating time for not sufficient capacity, calculating only total of resources.");
				$timeToResources = $this->resourcesCalculator->getTimeToEnoughResourcesTotal($planet, $command->getFleet()->getCapacity());
			}
			$minimalTime = $minimalTime->max($timeToResources);
		}

		return $minimalTime;
	}

	public function getFreeSlotsCount() : int
	{
		$this->menu->goToPage(MenuItem::_(MenuItem::FLEET));
		$fleets = $this->I->grabTextFrom('#inhalt > div:nth-of-type(2) > #slots > div:nth-of-type(1) > span.tooltip');
		list($occupied, $total) = OgameParser::parseSlash($fleets);
		return $total - $occupied;
	}

	private function areFreeFleets() : bool
	{
		return $this->getFreeSlotsCount() > 0;
	}

	private function areFreeExpeditions() : bool
	{
		$expeditions = $this->I->grabTextFrom('#inhalt > div:nth-of-type(2) > #slots > div:nth-of-type(2) > span.tooltip');
		list($occupied, $total) = OgameParser::parseSlash($expeditions);
		return $occupied < $total;
	}

	public function isProcessingAvailable(ICommand $command) : bool
	{
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$this->menu->goToPlanet($planet);
		$this->menu->goToPage(MenuItem::_(MenuItem::FLEET));

		if ($this->skipReload) {
			$this->skipReload = false;
		} else {
			$this->I->reloadPage(); //because the free fleets update only on page reload
		}


		/** @var SendFleetCommand $command */
		$freeFleets = $this->areFreeFleets();
		$freeExpeditions = $this->areFreeExpeditions();

		if ($command->getMission() === FleetMission::_(FleetMission::EXPEDITION)) {
			$freeFleets = $freeFleets && $freeExpeditions;
		}

		if ( ! $this->isFleetPresent($command)) {
			return false;
		}

		$enoughResources = true;
		if ($command->waitForResources()) {
			$this->logger->addDebug("This fleet wants to wait for resources: {$command->getResources()}.");
			$planet = $this->planetManager->getPlanet($command->getCoordinates());
			if ($this->isCapacitySufficient($command)) {
				$this->logger->addDebug('Checking enough resources for sufficient capacity');
				$enoughResources = $this->resourcesCalculator->isEnoughResources($planet, $command->getResources());
			} else {        //send all the resources that fleet can carry instead of resources in command
				$this->logger->addDebug('Checking enough resources for insufficient capacity');
				$enoughResources = $planet->getResources()->getTotal() >= $command->getFleet()->getCapacity();
			}
			$this->logger->addDebug($enoughResources ? 'Enough resources, processing available.' : "Not enough resources, processing unavailable.");
		}
		return $freeFleets && $enoughResources;
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var SendFleetCommand $command */
		$done = false;
		while (!$done) {
			try {
				$fleetSent = $this->sendFleet($command);
				$done = true;
				return $fleetSent;
			} catch(NoSuchElementException $e) {
				$this->signManager->checkSignedIn();
			}
		}
	}

	private function sendFleet(SendFleetCommand $command) : bool
	{
		$this->logger->addInfo("Going to send fleet {$command}.");

		$I = $this->I;

		$to = $command->getTo();

		$targetPlanet = $this->planetManager->getPlanet($to);
		$myPlanet = $this->planetManager->getPlanet($command->getCoordinates());
		$this->menu->goToPlanet($myPlanet);
		$this->menu->goToPage(MenuItem::_(MenuItem::FLEET));
		$fast = $command->isFast();

		if (!$this->isProcessingAvailable($command)) {
			$this->logger->addDebug('Processing not available.');
			return false;
		}
		$this->logger->addDebug('Processing available, starting to process the command.');

		if ($fast) {
			$type = 1;  //planet
//			$type = 3   //moon
			if ($command->getMission() === FleetMission::_(FleetMission::HARVESTING)) {
				$type = 2;  //debris
			}
			//todo: add type to debris
			$parameters = [
				'galaxy' => $to->getGalaxy(),
				'system' => $to->getSystem(),
				'position' => $to->getPlanet(),
				'type' => $type,
				'mission' => $command->getMission()->getNumber(),
				'speed' => $command->getSpeed() / 10
			];
			foreach ($command->getFleet() as $shipName => $count) {
				$ship = Ships::_($shipName);
				$parameters['am' . $ship->getNumber()] = $count;
			}
			$I->amOnUrl(Strings::appendGetParametersToUrl($I->getWholeUrl(), $parameters));
		} else {
			do {
				foreach ($command->getFleet() as $ship => $count) {
					if ($I->seeElementExists(Ships::_($ship)->getFleetInputSelector() . ':disabled')) {
						return false;
					}
					$I->fillField(Ships::_($ship)->getFleetInputSelector(), $count);
				}
			} while ( ! $I->seeElementExists('#continue.on'));
		}

		$I->click('#continue.on');
		$I->waitForText('Odeslání letky II', 3, '#planet > h2');

		if ($fast) {
			usleep(Random::microseconds(0.2, 0.4));
		} else {
			if ($command->getMission() === FleetMission::_(FleetMission::HARVESTING)) {
				$I->click('a.debris');
				usleep(Random::microseconds(0.5, 1));
			}

			$I->click((string) $command->getSpeed(), '#speedLinks');
			usleep(Random::microseconds(0.5, 1));

			do {
				$I->fillField('input#galaxy', $to->getGalaxy());
				$I->fillField('input#system', $to->getSystem());
				if ($command->getMission() === FleetMission::_(FleetMission::EXPEDITION)) {
					$I->fillField('input#position', 16);
				} else {
					$I->fillField('input#position', $to->getPlanet());
				}
				$this->logger->addDebug('Filled coordinates.');
				usleep(Random::microseconds(0.5, 1));
			} while ( ! $I->seeElementExists('#continue.on'));
		}

		$I->click('#continue.on');

		$this->logger->addDebug('Going to select mission, clicked on continue button.');

		try {
			usleep(Random::microseconds(0.2, 0.4));
			$I->waitForText('Odeslání letky III', 5, '#planet > h2');
		} catch(TimeOutException $e) {
			$this->logger->addDebug('Url is still same. Can not proceed to next phase of fleet sending. Throwing exception.');
			throw new NonExistingPlanetException();
		}

		usleep(Random::microseconds(0.1, 1));

		//we do not want to probe players who are not inactive anymore
		if ($command->hasStatuses()) {
			$playerStatusClass = $I->grabAttributeFrom('#fleetStatusBar > ul > li:nth-of-type(3) > span:last-of-type', 'class');
			$status = PlayerStatus::fromClass($playerStatusClass);
			$targetPlanet->getPlayer()->setStatus($status);
			if ( ! $command->getStatuses()->contains($status)) {
				$this->menu->goToPage(MenuItem::_(MenuItem::FLEET));
				return false;
			}
		}

		if ($fast) {
			usleep(Random::microseconds(0.2, 0.4));
		} else {
			do {
				$I->click($command->getMission()->getMissionSelector());
				$this->logger->addDebug('Selected mission.');
				usleep(Random::microseconds(1, 2));
			} while ( ! $I->seeElementExists('#start.on'));
		}

		//resources can not be filled by url parameters
		if ( ! $command->getResources()->isZero()) {
			$I->fillField('input#metal', $command->getResources()->getMetal());
			$I->fillField('input#crystal', $command->getResources()->getCrystal());
			$I->fillField('input#deuterium', $command->getResources()->getDeuterium());
		}

		$I->click('#start.on');
		$this->logger->addDebug('Fleet sent.');
		usleep(Random::microseconds(1.5, 2.5));

		$this->fleetInfo->reloadFlights();
		return true;
	}

	private function isCapacitySufficient(SendFleetCommand $command) : bool
	{
		return $command->getFleet()->getCapacity() >= $command->getResources()->getTotal();
	}

	private function isFleetPresent(SendFleetCommand $command) : bool
	{
		return $this->getPresentFleet($command->getCoordinates())->contains($command->getFleet());
	}

	public function getPresentFleet(Coordinates $coordinates) : Fleet
	{
		$planet = $this->planetManager->getPlanet($coordinates);
		$this->menu->goToPlanet($planet);
		$this->menu->goToPage(MenuItem::_(MenuItem::FLEET));

		$I = $this->I;
		$fleet = new Fleet();
		if ($I->seeExists('Na této planetě nejsou žádné lodě.', '#warning')) {
			return $fleet;
		}

		foreach (Ships::getMovingShips() as $ship) {
			$currentAmount = $I->grabTextFrom($ship->getCurrentAmountSelector());
			$fleet->addShips($ship, $currentAmount);
		}

		return $fleet;
	}

	/**
	 * @param SendFleetCommand[] $commands
	 * @param Uuid $uuid
	 * @param bool $removeNonExistingPlanets
	 * @throws NonExistingPlanetException
	 */
	public function sendMultipleFleetsAtOnce(array $commands, Uuid $uuid, bool $removeNonExistingPlanets = true)
	{
		foreach ($commands as $command) {
			$cacheName = $uuid->toString() . '/' . $command->getTo()->toString();
			if ($this->cache->load($cacheName) !== null) {
				$this->logger->addInfo("Skipping flight to planet {$command->getTo()->toString()}.");
				continue;
			}

			while ( ! $this->isProcessingAvailable($command)) { //page realoding is in this function, so there is no need to reload page here
				$time = $this->getTimeToProcessingAvailable($command);
				$seconds = $time->diffInSeconds();
				$seconds = min($seconds, 60);       //Do not wait for more than 60 seconds.
				$this->logger->addInfo("Going to wait until sending probes is available, for $seconds seconds.");
				sleep($seconds);
			}
			try {
				$this->skipReload = true;
				$this->processCommand($command);
			} catch(NonExistingPlanetException $e) {
				if ($removeNonExistingPlanets) {
					$this->logger->addInfo("Removing non existing planet from coordinates {$command->getTo()->toString()}");
					$this->databaseManager->removePlanet($command->getTo());
				} else {
					throw $e;
				}
			}
			$this->cache->save($cacheName, 'done', [Cache::TAGS => [$uuid->toString()]]);
		}
		$this->cache->clean([Cache::TAGS => [$uuid->toString()]]);
	}
}

class NonExistingPlanetException extends \Exception {}