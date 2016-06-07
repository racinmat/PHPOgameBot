<?php

namespace App\Model\Game;

use App\Enum\FleetMission;
use App\Enum\PlanetProbingStatus;
use App\Enum\ProbingStatus;
use App\Enum\Ships;
use App\Model\DatabaseManager;
use App\Model\Entity\Planet;

use App\Model\Queue\Command\ICommand;

use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\Queue\Command\SendFleetCommand;
use App\Model\Queue\ICommandProcessor;


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
		return true;
	}

	private function probePlayers(ProbePlayersCommand $command)
	{
		$this->logger->addInfo("Going to probe players by command {$command->toString()}.");
		$probingStart = Carbon::now();
		//send espionage probes to all players with selected statuses

		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$planetsToProbe = $this->databaseManager->getPlanetsFromCommand($command);
		$this->logger->addInfo(count($planetsToProbe) . ' planets to probe.');
		/** @var Planet $planetToProbe */
		foreach ($planetsToProbe as $planetToProbe) {
			$this->probePlanet($planetToProbe, $planet);
		}

		//todo: add waiting until all sent probes come back so we wont miss any report during the parsing.
		$this->reportReader->readEspionageReportsFrom($probingStart);
	}

	public function isProcessingAvailable(ICommand $command) : bool
	{
		return true;
	}

	private function probePlanet(Planet $planet, Planet $from)
	{
		$player = $planet->getPlayer();
		$probesAmount = $player->getProbesToLastEspionage(); //before first probing, we have 0 probes and did not get all information. So at least one probe is sent.
		if ($probesAmount === 0) {
			$probesAmount = 1;  //for first estimate
		} else if ($player->getProbingStatus()->missingAnyInformation() && $player->getProbingStatus() !== ProbingStatus::_(ProbingStatus::CURRENTLY_PROBING)) {
			$probesAmount = $this->calculateProbesAmountToGetAllInformation($probesAmount, $player->getProbingStatus());
		}
		$this->logger->addDebug("Going to probe player with name {$player->getName()} and planet with coordinates {$planet->getCoordinates()->toString()}. $probesAmount probes will be sent. Planet probing status is.");
		$player->setProbingStatus(ProbingStatus::_(ProbingStatus::CURRENTLY_PROBING));
		$player->setProbesToLastEspionage($probesAmount);
		$this->databaseManager->flush();
		$probePlanetCommand = SendFleetCommand::fromArray([
			'coordinates' => $from->getCoordinates()->toArray(),
			'data' => [
				'to' => $planet->getCoordinates()->toArray(),
				'fleet' => [Ships::ESPIONAGE_PROBE => $probesAmount],
				'mission' => FleetMission::ESPIONAGE
			]
		]);

		while ( ! $this->fleetManager->isProcessingAvailable($probePlanetCommand)) {
			$time = $this->fleetManager->getTimeToProcessingAvailable($probePlanetCommand);
			$seconds = $time->diffInSeconds();
			$seconds = min($seconds, 60);       //Do not wait for more than 60 seconds.
			$this->logger->addInfo("Going to wait until sending probes is available, for $seconds seconds.");
			sleep($seconds);
			$this->I->reloadPage();
		}
		try {
			$this->fleetManager->processCommand($probePlanetCommand);
		} catch(NonExistingPlanetException $e) {
			$this->logger->addInfo("Removing non existing planet from coordinates {$planet->getCoordinates()->toString()}");
			$this->databaseManager->removePlanet($planet->getCoordinates());
		}
	}

	private function calculateProbesAmountToGetAllInformation(int $probes, ProbingStatus $information) : int
	{
		$me = $this->databaseManager->getMe();
		$myLevel = $me->getEspionageTechnologyLevel();
		$currentResult = $information->getMaximalResult();
		$desiredResult = ProbingStatus::_(ProbingStatus::GOT_ALL_INFORMATION)->getMinimalResult();
		$enemyLevel = $this->calculateEnemyLevel($myLevel, $probes, $currentResult);
		$probesToSend = $desiredResult - ($myLevel - $enemyLevel) * abs($myLevel - $enemyLevel);
		$this->logger->addDebug("Calculating probes amount for $probes probes and probing status $information with result $currentResult. $probesToSend probes should be send to get all information.");
		return $probesToSend;
	}

	private function calculateEnemyLevel(int $myLevel, int $probes, int $result)
	{
		return $myLevel + gmp_sign($probes - $result) + sqrt(abs($probes - $result));
	}

}
