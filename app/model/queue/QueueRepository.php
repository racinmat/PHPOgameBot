<?php

namespace App\Model\Queue;
 
use App\Model\Queue\Command\BuildDefenseCommand;
use App\Model\Queue\Command\BuildShipsCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\UpgradeBuildingCommand;
use App\Model\Queue\Command\UpgradeResearchCommand;
use App\Utils\Functions;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Object;
use Nette\Utils\Json;

class QueueRepository extends Object
{

	/** @var string */
	private $queueFile;

	public function __construct(string $queueFile)
	{
		$this->queueFile = $queueFile;
	}

	/**
	 * @return ICommand[]
	 * @throws \Nette\Utils\JsonException
	 */
	public function loadQueue() : array
	{
		$queueCollection = new ArrayCollection(Json::decode(file_get_contents($this->queueFile), Json::FORCE_ARRAY));
		return $queueCollection->map($this->arrayToCommandCallback())->toArray();
	}

	private function arrayToCommand(array $data) : ICommand
	{
		foreach ($this->getCommandList() as $commandClass) {
			if ($commandClass::getAction() === $data['action']) {
				return $commandClass::fromArray($data['data']);
			}
		}
	}

	private function arrayToCommandCallback() : callable
	{
		return function (array $data) {
			return $this->arrayToCommand($data);
		};
	}

	private function getCommandList() : array
	{
		return [
			UpgradeBuildingCommand::class,
			UpgradeResearchCommand::class,
			BuildDefenseCommand::class,
			BuildShipsCommand::class
		];
	}

	public function saveQueue(array $queue)
	{
		$array = (new ArrayCollection($queue))->map(Functions::toArray())->getValues();
		file_put_contents($this->queueFile, Json::encode($array, Json::PRETTY));
	}

}