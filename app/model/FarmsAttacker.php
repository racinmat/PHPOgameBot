<?php

namespace App\Model;

use App\Enum\FleetMission;
use App\Enum\PlayerStatus;
use App\Enum\ProbingStatus;
use App\Enum\Ships;
use App\Model\Entity\Planet;
use App\Model\Game\FleetManager;
use App\Model\Queue\Command\AttackFarmsCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\SendFleetCommand;
use App\Model\Queue\ICommandProcessor;
use App\Utils\ArrayCollection;
use App\Utils\Functions;
use Carbon\Carbon;
use Kdyby\Monolog\Logger;
use Nette\Object;

class FarmsAttacker extends Object implements ICommandProcessor
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
		return $command instanceof AttackFarmsCommand;
	}

	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		//just some fake command to get time to free fleet slot
		$probePlanetCommand = SendFleetCommand::fromArray([
			'coordinates' => $command->getCoordinates()->toArray(),
			'data' => [
				'to' => ['galaxy' => 1, 'system' => 1, 'planet' => 1],
				'fleet' => [Ships::LARGE_CARGO_SHIP => 1],
				'mission' => FleetMission::ATTACKING
			]
		]);
		return $this->fleetManager->getTimeToProcessingAvailable($probePlanetCommand);
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var AttackFarmsCommand $command */
		$planets = $this->planetCalculator->getFarms($command->getLimit(), Carbon::now()->sub($command->getVisitedAfter()));
		$fromPlanet = $this->databaseManager->getPlanet($command->getCoordinates());
		$attackCommands = $this->createAttackCommands($planets, $fromPlanet);
		$this->fleetManager->sendMultipleFleetsAtOnce($attackCommands, $command->getUuid());
		$this->planetCalculator->saveResourcesEstimateAfterAttackForPlanets($planets);
		return true;
	}

	/**
	 * @param Planet[] $planets
	 * @param Planet $from
	 * @return SendFleetCommand[]
	 */
	private function createAttackCommands(array $planets, Planet $from)
	{
		$largeCargo = Ships::_(Ships::LARGE_CARGO_SHIP);
		$farmedStatuses = new ArrayCollection();
		$farmedStatuses->add(PlayerStatus::_(PlayerStatus::STATUS_INACTIVE));
		$farmedStatuses->add(PlayerStatus::_(PlayerStatus::STATUS_LONG_INACTIVE));
		$commands = [];
		foreach ($planets as $planet) {
			$player = $planet->getPlayer();
			$cargoesAmount = $this->planetCalculator->countShipsNeededToFarmResources($planet, $largeCargo);
			$this->logger->addDebug("Going to attack farm. Attacking player with name {$player->getName()} and planet with coordinates {$planet->getCoordinates()->toString()}. $cargoesAmount cargoes will be sent.");
			$player->setProbingStatus(ProbingStatus::_(ProbingStatus::CURRENTLY_PROBING));
			$player->setProbesToLastEspionage($cargoesAmount);
			$this->databaseManager->flush();
			$commands[] = SendFleetCommand::fromArray([
				'coordinates' => $from->getCoordinates()->toArray(),
				'data' => [
					'to' => $planet->getCoordinates()->toArray(),
					'fleet' => [$largeCargo->getValue() => $cargoesAmount],
					'mission' => FleetMission::ATTACKING,
					'statuses' => $farmedStatuses->map(Functions::enumToValue())->toArray()
				]
			]);
		}
		return $commands;
	}

	public function isProcessingAvailable(ICommand $command) : bool
	{
		return true;
	}

}
