<?php

namespace App\Model;
 
use App\Enum\Building;
use App\Enum\Defense;
use App\Model\Command\BuildDefenseCommand;
use App\Model\Command\ICommand;
use App\Model\Command\UpgradeBuildingCommand;
use App\Model\Entity\QueueItem;
use App\Utils\Functions;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette\Object;
use Nette\Utils\Json;

class QueueConsumer extends Object
{

	/** @var BuildingManager */
	private $buildingsManager;

	/** @var PlanetManager */
	private $planetManager;

	/** @var ResourcesCalculator */
	private $resourcesCalculator;

	/** @var CronManager */
	private $cronManager;

	/** @var DefenseManager */
	private $defenseManager;

	/** @var string */
	private $queueFile;

	public function __construct(string $queueFile, BuildingManager $buildingsManager, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator, CronManager $cronManager, DefenseManager $defenseManager)
	{
		$this->queueFile = $queueFile;
		$this->buildingsManager = $buildingsManager;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->cronManager = $cronManager;
		$this->defenseManager = $defenseManager;
	}

	public function processQueue()
	{
		/** @var ICommand[] $queue */
		$queue = $this->loadQueue();
		$success = true;    //aby se zastavilo procházení fronty, když se nepodaří postavit budovu a zpracování tak skončilo
		$lastItem = null;
		foreach ($queue as $key => $command) {
			switch ($command->getAction()) {
				case ICommand::ACTION_UPGRADE:
					/** @var UpgradeBuildingCommand $command */
					$success = $this->upgrade($command);
					break;
				case ICommand::ACTION_BUILD_DEFENSE:
					/** @var BuildDefenseCommand $command */
					$success = $this->build($command);
					break;
			}
			if ($success) {
				unset($queue['key']);
			}
			$lastItem = $command;
			if (!$success) {
				break;
			}
		}
		$this->saveQueue($queue);
		if (!$success) {
			/** @var Carbon $datetime */
			$datetime = Carbon::now();
			$this->planetManager->refreshResourceData();
			$planet = $this->planetManager->getMyHomePlanet();
			switch ($lastItem->getAction()) {
				case ICommand::ACTION_UPGRADE:
					/** @var UpgradeBuildingCommand $lastItem */
					$datetime = $this->resourcesCalculator->getTimeToEnoughResourcesForBuilding($planet, $lastItem->getBuilding());
					break;
				case ICommand::ACTION_BUILD_DEFENSE:
					/** @var BuildDefenseCommand $lastItem */
					$datetime = $this->resourcesCalculator->getTimeToEnoughResourcesFoDefense($planet, $lastItem->getDefense(), $lastItem->getAmount());
					break;
			}
			$this->cronManager->setNextStart($datetime);
		}
	}

	/**
	 * @param UpgradeBuildingCommand $command
	 * @return bool returns true if building is built successfully
	 */
	private function upgrade(UpgradeBuildingCommand $command) : bool
	{
		return $this->buildingsManager->upgrade($command->getBuilding());
	}

	/**
	 * @param BuildDefenseCommand $command
	 * @return bool returns true if building is built successfully
	 */
	private function build(BuildDefenseCommand $command)
	{
		return $this->defenseManager->build($command->getDefense(), $command->getAmount());
	}

	/**
	 * @return ICommand[]
	 * @throws \Nette\Utils\JsonException
	 */
	private function loadQueue() : array
	{
		$queueText = file_get_contents($this->queueFile);
		$queueCollection = new ArrayCollection(Json::decode($queueText));
		return $queueCollection->map($this->arrayToCommandCallback());
	}

	private function arrayToCommand(array $data) : ICommand
	{
		foreach ($this->getCommandList() as $commandClass) {
			if ($commandClass::getAction() === $data['action']) {
				return $commandClass::fromArray($data);
			}
		}
	}

	private function arrayToCommandCallback()
	{
		return function (array $data) {
			return $this->arrayToCommand($data);
		};
	}

	private function getCommandList() : array
	{
		return [
			BuildDefenseCommand::class,
			UpgradeBuildingCommand::class
		];
	}

	private function saveQueue(array $queue)
	{
		$queueCollection = new ArrayCollection($queue);
		$arrays = $queueCollection->map(Functions::toArray());
		$text = Json::encode($arrays);
		file_put_contents($this->queueFile, $text);
	}
}