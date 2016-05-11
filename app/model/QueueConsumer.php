<?php

namespace App\Model;
 
use App\Model\Command\BuildDefenseCommand;
use App\Model\Command\ICommand;
use App\Model\Command\UpgradeBuildingCommand;
use Carbon\Carbon;
use Nette\Object;

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

	/** @var QueueRepository */
	private $queueRepository;

	public function __construct(QueueRepository $queueRepository, BuildingManager $buildingsManager, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator, CronManager $cronManager, DefenseManager $defenseManager)
	{
		$this->queueRepository = $queueRepository;
		$this->buildingsManager = $buildingsManager;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->cronManager = $cronManager;
		$this->defenseManager = $defenseManager;
	}

	public function processQueue()
	{
		$queue = $this->queueRepository->loadQueue();
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
		$this->queueRepository->saveQueue($queue);
		if (!$success) {
			/** @var Carbon $datetime */
			$datetime = Carbon::now();
			$this->planetManager->refreshResourceData();
			$planet = $this->planetManager->getMyHomePlanet();
			switch ($lastItem->getAction()) {
				case ICommand::ACTION_UPGRADE:
					/** @var UpgradeBuildingCommand $lastItem */
					$datetime = $this->buildingsManager->getTimeToUpgradeAvailable($planet, $lastItem->getBuilding());
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

}