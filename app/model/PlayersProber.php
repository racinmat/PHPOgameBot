<?php

namespace App\Model\Game;

use App\Enum\FleetMission;
use App\Enum\Ships;
use App\Model\DatabaseManager;
use App\Model\Entity\Planet;
use App\Model\Queue\Command\IBuildCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\Queue\Command\SendFleetCommand;
use app\model\queue\ICommandProcessor;
use App\Model\ResourcesCalculator;
use App\Utils\Random;
use Carbon\Carbon;
use Kdyby\Monolog\Logger;
use Nette\Object;

class PlayersProber extends Object implements ICommandProcessor
{

	/** @var PlanetManager */
	private $planetManager;

	/** @var Logger */
	private $logger;

	/** @var DatabaseManager */
	private $databaseManager;

	/** @var FleetManager */
	private $fleetManager;

	public function __construct(PlanetManager $planetManager, Logger $logger, DatabaseManager $databaseManager, FleetManager $fleetManager)
	{
		$this->planetManager = $planetManager;
		$this->logger = $logger;
		$this->databaseManager = $databaseManager;
		$this->fleetManager = $fleetManager;
	}

	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof ProbePlayersCommand;
	}

	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		//todo: použít metodu t FleetManageru
	}

	public function isProcessingAvailable(Planet $planet, IEnhanceCommand $command) : bool
	{
		//todo: použít metodu t FleetManageru
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var ProbePlayersCommand $command */
		$this->probePlayers($command);
	}

	private function probePlayers(ProbePlayersCommand $command)
	{
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$planetsToProbe = $this->databaseManager->getPlanetsOfPlayersWithStatuses($command->getStatuses());
		/** @var Planet $planetToProbe */
		foreach ($planetsToProbe as $planetToProbe) {
			$probePlanetCommand = SendFleetCommand::fromArray([
				'coordinates' => $planet->getCoordinates()->toValueObject()->toArray(),
				'data' => [
					'to' => $planetToProbe->getCoordinates()->toValueObject()->toArray(),
					'fleet' => [Ships::ESPIONAGE_PROBE => 1],    //todo: move setting probes count to command
					'mission' => FleetMission::ESPIONAGE
				]
			]);

			while ( ! $this->fleetManager->isProcessingAvailable($probePlanetCommand)) {
				$time = $this->fleetManager->getTimeToProcessingAvailable($probePlanetCommand);
				sleep($time->diffInSeconds());
			}
			$this->fleetManager->processCommand($probePlanetCommand);
		}
	}
}
