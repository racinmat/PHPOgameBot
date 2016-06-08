<?php

namespace App\Model;

use App\Enum\FleetMission;
use App\Enum\PlanetProbingStatus;
use App\Enum\ProbingStatus;
use App\Enum\Ships;
use App\Model\DatabaseManager;
use App\Model\Entity\Planet;

use App\Model\Game\FleetManager;
use App\Model\Game\PlanetManager;
use App\Model\Game\ReportReader;
use App\Model\Queue\Command\ICommand;

use App\Model\Queue\Command\ProbeFarmsCommand;
use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\Queue\Command\SendFleetCommand;
use App\Model\Queue\ICommandProcessor;


use Carbon\Carbon;
use Kdyby\Monolog\Logger;
use Nette\Object;

class FarmsProber extends Object implements ICommandProcessor
{

	/** @var Logger */
	private $logger;

	/** @var DatabaseManager */
	private $databaseManager;

	/** @var FleetManager */
	private $fleetManager;

	/** @var Prober */
	private $prober;

	/** @var PlanetCalculator */
	private $planetCalculator;

	public function __construct(Logger $logger, DatabaseManager $databaseManager, FleetManager $fleetManager, Prober $prober, PlanetCalculator $planetCalculator)
	{
		$this->logger = $logger;
		$this->databaseManager = $databaseManager;
		$this->fleetManager = $fleetManager;
		$this->prober = $prober;
		$this->planetCalculator = $planetCalculator;
	}

	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof ProbeFarmsCommand;
	}

	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		//just some fake command to get time to free fleet slot
		$probePlanetCommand = SendFleetCommand::fromArray([
			'coordinates' => $command->getCoordinates()->toArray(),
			'data' => [
				'to' => ['galaxy' => 1, 'system' => 1, 'planet' => 1],
				'fleet' => [Ships::ESPIONAGE_PROBE => 1],
				'mission' => FleetMission::ESPIONAGE
			]
		]);
		return $this->fleetManager->getTimeToProcessingAvailable($probePlanetCommand);
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var ProbeFarmsCommand $command */
		$planets = $this->planetCalculator->getFarms($command->getLimit());
		$fromPlanet = $this->databaseManager->getPlanet($command->getCoordinates());
		$this->prober->probePlanets($planets, $fromPlanet);
		return true;
	}

	public function isProcessingAvailable(ICommand $command) : bool
	{
		return true;
	}

}
