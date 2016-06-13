<?php

namespace App\Model;

use App\Enum\FleetMission;
use App\Enum\ProbingStatus;
use App\Enum\Ships;
use App\Model\Entity\Planet;
use App\Model\Game\FleetManager;
use App\Model\Game\NonExistingPlanetException;
use App\Model\Game\PlanetManager;
use App\Model\Game\ReportReader;
use App\Model\Queue\Command\SendFleetCommand;
use App\Utils\ArrayCollection;
use App\Utils\Functions;
use Carbon\Carbon;
use Kdyby\Monolog\Logger;
use Nette\Object;
use Ramsey\Uuid\Uuid;

class Prober extends Object
{

	/** @var PlanetManager */
	private $planetManager;

	/** @var Logger */
	private $logger;

	/** @var DatabaseManager */
	private $databaseManager;

	/** @var FleetManager */
	private $fleetManager;

	/** @var PlanetCalculator */
	private $planetsCalculator;

	/** @var ReportReader */
	private $reportReader;

	/** @var \AcceptanceTester */
	private $I;

	public function __construct(PlanetManager $planetManager, Logger $logger, DatabaseManager $databaseManager, FleetManager $fleetManager, ReportReader $reportReader, \AcceptanceTester $I, PlanetCalculator $planetCalculator)
	{
		$this->planetManager = $planetManager;
		$this->logger = $logger;
		$this->databaseManager = $databaseManager;
		$this->fleetManager = $fleetManager;
		$this->reportReader = $reportReader;
		$this->I = $I;
		$this->planetsCalculator = $planetCalculator;
	}

	/**
	 * @param Planet[] $planets
	 * @param Planet $fromPlanet
	 * @param ArrayCollection $statuses
	 * @param Uuid $uuid
	 * @throws NonExistingPlanetException
	 */
	public function probePlanets(array $planets, Planet $fromPlanet, ArrayCollection $statuses, Uuid $uuid)
	{
		$this->logger->addInfo("Going to probe planets.");
		$probingStart = Carbon::now();
		//send espionage probes to all players with selected statuses

		$this->logger->addInfo(count($planets) . ' planets to probe.');
		$commands = $this->createEspionageCommands($planets, $fromPlanet, $statuses);
		$this->fleetManager->sendMultipleFleetsAtOnce($commands, $uuid);

		//todo: add waiting until all sent probes come back so we wont miss any report during the parsing.
		sleep(40);  //now I just wait for some time
		$this->reportReader->readEspionageReportsFrom($probingStart);
	}

	/**
	 * @param Planet[] $planets
	 * @param Planet $from
	 * @param ArrayCollection $statuses
	 * @return SendFleetCommand[]
	 */
	private function createEspionageCommands(array $planets, Planet $from, ArrayCollection $statuses)
	{
		$commands = [];
		foreach ($planets as $planet) {
			$player = $planet->getPlayer();
			$probesAmount = $player->getProbesToLastEspionage(); //before first probing, we have 0 probes and did not get all information. So at least one probe is sent.
			if ($probesAmount === 0) {
				$probesAmount = 1;  //for first estimate
			} else if ($player->getProbingStatus()->missingAnyInformation() && $player->getProbingStatus() !== ProbingStatus::_(ProbingStatus::CURRENTLY_PROBING)) {
				$probesAmount = $this->calculateProbesAmountToGetAllInformation($probesAmount, $player->getProbingStatus());
			}
			$this->logger->addDebug("Going to probe player with name {$player->getName()} and planet with coordinates {$planet->getCoordinates()->toString()}. $probesAmount probes will be sent. Planet probing status is {$planet->getProbingStatus()}.");
			$player->setProbingStatus(ProbingStatus::_(ProbingStatus::CURRENTLY_PROBING));
			$player->setProbesToLastEspionage($probesAmount);
			$this->databaseManager->flush();
			$commands[] = SendFleetCommand::fromArray([
				'coordinates' => $from->getCoordinates()->toArray(),
				'data' => [
					'to' => $planet->getCoordinates()->toArray(),
					'fleet' => [Ships::ESPIONAGE_PROBE => $probesAmount],
					'mission' => FleetMission::ESPIONAGE,
					'statuses' => $statuses->map(Functions::enumToValue())->toArray(),
					'fast' => true
				]
			]);
		}
		return $commands;
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
