<?php

namespace App\Model;

use App\Enum\Building;
use App\Model\Game\PlanetManager;
use App\Model\Game\UpgradeManager;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\Queue\Command\UpgradeBuildingCommand;
use App\Model\Queue\ICommandPreProcessor;
use App\Model\Queue\QueueManager;
use App\Utils\Collection;
use App\Utils\Functions;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Object;

class UpgradeStoragesPreProcessor extends Object implements ICommandPreProcessor
{

	/** @var UpgradeManager */
	private $upgradeManager;

	/** @var ResourcesCalculator */
	private $resourcesCalculator;

	/** @var PlanetManager */
	private $planetManager;

	/** @var QueueManager */
	private $queueManager;

	public function __construct(UpgradeManager $upgradeManager, ResourcesCalculator $resourcesCalculator, PlanetManager $planetManager, QueueManager $queueManager)
	{
		$this->upgradeManager = $upgradeManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->planetManager = $planetManager;
		$this->queueManager = $queueManager;
	}

	public function canPreProcessCommand(ICommand $command) : bool
	{
		return $command instanceof IEnhanceCommand;
	}

	public function preProcessCommand(ICommand $command, Collection $queue) : bool
	{
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		/** @var IEnhanceCommand $command */
		if ( ! $command->buildStoragesIfNeeded()) {
			return false;
		}

		if ( ! $this->resourcesCalculator->isNeedToUpgradeStoragesToHaveResources($planet, $command->getPrice($planet))) {
			return false;
		}

		$neededStoragesLevels = $this->resourcesCalculator->getMinimalStorageLevelsForResources($command->getPrice($planet));
		$metalStorageLevel = $neededStoragesLevels->getMetal();
		$crystalStorageLevel = $neededStoragesLevels->getCrystal();
		$deuteriumTankLevel = $neededStoragesLevels->getDeuterium();

		$coordinates = $planet->getCoordinates()->toValueObject()->toArray();
		$commands = [];
		for ($i = $planet->getMetalStorageLevel(); $i < $metalStorageLevel; $i++) {
			$commands[] = UpgradeBuildingCommand::fromArray([
				'coordinates' => $coordinates,
				'data' => ['building' => Building::METAL_STORAGE]
			]);
		}
		for ($i = $planet->getCrystalStorageLevel(); $i < $crystalStorageLevel; $i++) {
			$commands[] = UpgradeBuildingCommand::fromArray([
				'coordinates' => $coordinates,
				'data' => ['building' => Building::CRYSTAL_STORAGE]
			]);
		}
		for ($i = $planet->getDeuteriumTankLevel(); $i < $deuteriumTankLevel; $i++) {
			$commands[] = UpgradeBuildingCommand::fromArray([
				'coordinates' => $coordinates,
				'data' => ['building' => Building::DEUTERIUM_TANK]
			]);
		}

		//I want to build storages uniformly
		usort($commands, Functions::compareEnhanceCommandsByPrice($planet));

		foreach ($commands as $newCommand) {
			$this->queueManager->addBefore($newCommand, $command->getUuid());
		}
		$queue->prepend($commands);
		return true;
	}

}