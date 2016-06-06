<?php

namespace App\Model\Queue;
 
use App\Model\Queue\Command\BuildDefenseCommand;
use App\Model\Queue\Command\BuildShipsCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\Queue\Command\ScanGalaxyCommand;
use App\Model\Queue\Command\SendFleetCommand;
use App\Model\Queue\Command\UpgradeBuildingCommand;
use App\Model\Queue\Command\UpgradeResearchCommand;
use App\Utils\Functions;
use App\Utils\ArrayCollection;
use Nette\Object;

use Nette\Utils\Json;

class QueueFileRepository extends Object
{

	/** @var string */
	private $queueFile;

	/** @var string */
	private $repetitiveCommandsFile;

	public function __construct(string $queueFile, string $repetitiveCommandsFile)
	{
		$this->queueFile = $queueFile;
		$this->repetitiveCommandsFile = $repetitiveCommandsFile;
	}

	/**
	 * @return ICommand[]|ArrayCollection
	 * @throws \Nette\Utils\JsonException
	 */
	public function loadQueue() : ArrayCollection
	{
		$queueCollection = new ArrayCollection(Json::decode(file_get_contents($this->queueFile), Json::FORCE_ARRAY));
		return $queueCollection->map($this->arrayToCommandCallback());
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
			SendFleetCommand::class
		];
	}

	public function saveQueue(ArrayCollection $queue)
	{
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
		return $queueCollection->map($this->arrayToCommandCallback());
	}

	public function saveRepetitiveCommands(ArrayCollection $queue)
	{
		$array = $queue->map(Functions::toArray())->getValues();
		file_put_contents($this->repetitiveCommandsFile, Json::encode($array, Json::PRETTY));
	}

}