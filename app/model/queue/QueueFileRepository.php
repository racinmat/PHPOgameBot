<?php

namespace App\Model\Queue;
 
use App\Model\Queue\Command\AttackFarmsCommand;
use App\Model\Queue\Command\BuildDefenseCommand;
use App\Model\Queue\Command\BuildShipsCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\ProbeFarmsCommand;
use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\Queue\Command\ScanGalaxyCommand;
use App\Model\Queue\Command\SendFleetCommand;
use App\Model\Queue\Command\UpgradeBuildingCommand;
use App\Model\Queue\Command\UpgradeResearchCommand;
use App\Utils\Functions;
use App\Utils\ArrayCollection;
use Kdyby\Monolog\Logger;
use Nette\Object;

use Nette\Utils\Json;

class QueueFileRepository extends Object
{

	/** @var string */
	private $queueFile;

	/** @var string */
	private $repetitiveCommandsFile;

	/** @var Logger */
	private $logger;

	public function __construct(string $queueFile, string $repetitiveCommandsFile, Logger $logger)
	{
		$this->queueFile = $queueFile;
		$this->repetitiveCommandsFile = $repetitiveCommandsFile;
		$this->logger = $logger;
	}

	/**
	 * @return ICommand[]|ArrayCollection
	 * @throws \Nette\Utils\JsonException
	 */
	public function loadQueue() : ArrayCollection
	{
		$queueCollection = new ArrayCollection(Json::decode(file_get_contents($this->queueFile), Json::FORCE_ARRAY));
		$queue = $queueCollection->map($this->arrayToCommandCallback());
		$this->logger->addDebug("Loading queue with ids: " . Json::encode($queue->map(Functions::commandToUuidString())->toArray()));
		return $queue;
	}

	private function arrayToCommand(array $data) : ICommand
	{
		foreach ($this->getCommandList() as $commandClass) {
			if ($commandClass::getAction() === $data['action']) {
				unset($data['action']);
				return $commandClass::fromArray($data);
			}
		}
		throw new \InvalidArgumentException("No action corresponds with provided data.");
	}

	private function arrayToCommandCallback() : callable
	{
		return function (array $data) {
			return $this->arrayToCommand($data);
		};
	}

	/**
	 * @return string[]
	 */
	public function getCommandList() : array
	{
		return [
			UpgradeBuildingCommand::class,
			UpgradeResearchCommand::class,
			BuildDefenseCommand::class,
			BuildShipsCommand::class,
			ScanGalaxyCommand::class,
			ProbePlayersCommand::class,
			SendFleetCommand::class,
			ProbeFarmsCommand::class,
			AttackFarmsCommand::class
		];
	}

	public function saveQueue(ArrayCollection $queue)
	{
		$this->logger->addDebug("Saving queue with ids: " . Json::encode($queue->map(Functions::commandToUuidString())->toArray()));
		$array = $queue->map(Functions::toArray())->getValues();
		file_put_contents($this->queueFile, Json::encode($array, Json::PRETTY));
	}

	/**
	 * @return ICommand[]|ArrayCollection
	 * @throws \Nette\Utils\JsonException
	 */
	public function loadRepetitiveCommands() : ArrayCollection
	{
		$queueCollection = new ArrayCollection(Json::decode(file_get_contents($this->repetitiveCommandsFile), Json::FORCE_ARRAY));
		$queue = $queueCollection->map($this->arrayToCommandCallback());
		$this->logger->addDebug("Loading repetitive commands with ids: " . Json::encode($queue->map(Functions::commandToUuidString())->toArray()));
		return $queue;
	}

	public function saveRepetitiveCommands(ArrayCollection $queue)
	{
		$this->logger->addDebug("Saving repetitive commands with ids: " . Json::encode($queue->map(Functions::commandToUuidString())->toArray()));
		$array = $queue->map(Functions::toArray())->getValues();
		file_put_contents($this->repetitiveCommandsFile, Json::encode($array, Json::PRETTY));
	}

}