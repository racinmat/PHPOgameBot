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

	/** @var ReportReader */
	private $reportReader;

	/** @var \AcceptanceTester */
	private $I;

	public function __construct(PlanetManager $planetManager, Logger $logger, DatabaseManager $databaseManager, FleetManager $fleetManager, ReportReader $reportReader, \AcceptanceTester $I)
	{
		$this->planetManager = $planetManager;
		$this->logger = $logger;
		$this->databaseManager = $databaseManager;
		$this->fleetManager = $fleetManager;
		$this->reportReader = $reportReader;
		$this->I = $I;
	}

	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof ProbePlayersCommand;
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
		/** @var ProbePlayersCommand $command */
		$this->probePlayers($command);
	}

	private function probePlayers(ProbePlayersCommand $command)
	{
		$this->logger->addInfo("Going to probe players by command {$command->toString()}.");
		$probingStart = Carbon::now();
		//send espionage probes to all players with selected statuses

		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$planetsToProbe = $this->databaseManager->getPlanetsOfPlayersWithStatuses($command->getStatuses());
		$this->logger->addInfo(count($planetsToProbe) . ' planets to probe.');
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
				$this->I->reloadPage();
			}
			$this->fleetManager->processCommand($probePlanetCommand);
		}

		//todo: add waiting until all sent probes come back so we wont miss any report during the parsing.
		$this->reportReader->readEspionageReportsFrom($probingStart);
	}
}
