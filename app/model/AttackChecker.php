<?php

namespace App\Model;

use App\Enum\FleetMission;
use App\Model\Game\FleetManager;
use App\Model\Game\PlanetManager;
use App\Model\PageObject\FleetInfo;
use App\Model\Queue\Command\SendFleetCommand;
use App\Model\Queue\CommandDispatcher;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\Fleet;
use App\Model\ValueObject\Resources;
use App\Utils\Functions;
use App\Utils\OgameParser;
use Kdyby\Monolog\Logger;
use Nette\Object;

class AttackChecker extends Object
{

	/** @var Logger */
	private $logger;

	/** @var FleetInfo */
	private $fleetInfo;

	/** @var CommandDispatcher */
	private $commandDispatcher;

	/** @var FleetManager */
	private $fleetManager;

	/** @var DatabaseManager */
	private $databaseManager;

	public function __construct(FleetInfo $fleetInfo, Logger $logger, CommandDispatcher $commandDispatcher, FleetManager $fleetManager, DatabaseManager $databaseManager)
	{
		$this->fleetInfo = $fleetInfo;
		$this->logger = $logger;
		$this->commandDispatcher = $commandDispatcher;
		$this->fleetManager = $fleetManager;
		$this->databaseManager = $databaseManager;
	}

	public function checkIncomingAttacks()
	{
		$this->logger->addDebug('checking attacks');
		if ($this->fleetInfo->isAnyAttackOnMe()) {
			$this->attackDetected();
		} else {
			$this->logger->addDebug('attack not detected');
		}
	}

	private function attackDetected()
	{
		$nearestAttack = $this->fleetInfo->getNearestAttackFlight();
		$currentFleet = $this->fleetManager->getPresentFleet($nearestAttack->getTo());
		$to = $nearestAttack->getTo();

		$this->logger->addAlert("Attack on some of my planets! Nearest attack in {$nearestAttack->getArrivalTime()}.");
		$this->logger->addDebug('attack detected and logged. Preparing fleetsave.');

		/** @var Coordinates $otherPlanet */
		$otherPlanet = $this->databaseManager->getAllMyPlanetsCoordinates()->filter(function (Coordinates $c) use ($to) {return ! $c->equals($to);})->first();
		$data = [
			'to' => $otherPlanet->toArray(),
			'fleet' => $currentFleet->toArray(),
			'mission' => FleetMission::DEPLOYMENT,
			'resources' => (new Resources(100000000, 100000000, 100000000))->toArray()
		];
		$fleetSaveCommand = new SendFleetCommand($to, $data);
		$this->commandDispatcher->processCommand($fleetSaveCommand);
		$this->logger->addDebug('Fleetsave done.');
	}

}