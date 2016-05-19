<?php

namespace App\Model;
 
use App\Enum\Buildable;
use App\Enum\Building;
use App\Enum\Defense;
use App\Enum\Upgradable;
use App\Model\Entity\Planet;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\ValueObject\Resources;
use App\Utils\Functions;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Monolog\Logger;
use Nette;
 
class ResourcesCalculator extends Nette\Object
{

	/** @var int */
	private $acceleration;

	/** @var Logger */
	private $logger;

	public function __construct(int $acceleration, Logger $logger)
	{
		$this->acceleration = $acceleration;
		$this->logger = $logger;
	}

	public function isEnoughResourcesToEnhance(Planet $planet, IEnhanceCommand $command) : bool
	{
		$missing = $this->getMissingResources($planet, $command->getPrice($planet));
		$enough = $missing->isZero();
		$this->logger->addDebug("Checking resources to process command {$command->toString()} in planet {$planet->getCoordinates()->toValueObject()->toString()} which needs {$command->getPrice($planet)->toString()} resources.");
		if ($enough) {
			$this->logger->addDebug("Enough resources.");
		} else {
			$this->logger->addDebug("Not enough resources, missing resources: {$missing->toString()}.");
		}
		return $enough;
	}

	public function getTimeToEnoughResourcesToEnhance(Planet $planet, IEnhanceCommand $command) : Carbon
	{
		$missingResources = $this->getMissingResources($planet, $command->getPrice($planet));
		return $this->getTimeToResources($planet, $missingResources);
	}

	private function getMissingResources(Planet $planet, Resources $expected) : Resources
	{
		return $expected->subtract($planet->getResources());
	}

	private function getTimeToResources(Planet $planet, Resources $missing) : Carbon
	{
		$time = $planet->getLastVisited();

		$productionPerHour = $this->getProductionPerHour($planet);

		$hours = max($missing->divide($productionPerHour));
		if ($hours <= 0) {
			$this->logger->addDebug("No missing resources, now is enough resources.");
			return Carbon::now();
		}

		$minutes = ($hours - (int) $hours) * 60;
		$seconds = ($minutes - (int) $minutes) * 60;
		$resourcesAvailable = $time->addHours((int) $hours)->addMinutes((int) $minutes)->addSeconds((int) $seconds);
		$this->logger->addDebug("Missing resources, enough will be in {$resourcesAvailable->__toString()}.");
		return $resourcesAvailable;
	}

	private function getMetalProductionPerHour(int $mineLevel) : int
	{
		return $this->acceleration * 30 + round($this->acceleration * 30 * $mineLevel * pow(1.1, $mineLevel));
	}

	private function getCrystalProductionPerHour(int $mineLevel) : int
	{
		return $this->acceleration * 15 + round($this->acceleration * 20 * $mineLevel * pow(1.1, $mineLevel));
	}

	private function getDeuteriumProductionPerHour(int $mineLevel, int $averageTemperature) : int
	{
		return round($this->acceleration * 10 * $mineLevel * pow(1.1, $mineLevel) * (1.36 - 0.004 * $averageTemperature));
	}

	public function getProductionPerHour(Planet $planet) : Resources
	{
		return new Resources(
			$this->getMetalProductionPerHour($planet->getMetalMineLevel()),
			$this->getCrystalProductionPerHour($planet->getCrystalMineLevel()),
			$this->getDeuteriumProductionPerHour($planet->getDeuteriumMineLevel(), $planet->getAverageTemperature())
		);
	}

	public function isNeedToUpgradeStoragesToHaveResources(Planet $planet, Resources $resources) : bool
	{
		return ! $resources->subtract($this->getStoragesCapacity($planet))->isZero();
	}

	private function getStorageCapacity(int $storageLevel) : int
	{
		return 5000 * (int) (2.5 * pow(M_E, (20 * $storageLevel)/33));
	}

	private function getStoragesCapacity(Planet $planet) : Resources
	{
		return new Resources(
			$this->getStorageCapacity($planet->getMetalStorageLevel()),
			$this->getStorageCapacity($planet->getCrystalStorageLevel()),
			$this->getStorageCapacity($planet->getDeuteriumTankLevel())
		);
	}

	public function getMinimalStorageLevelsForResources(Resources $resources) : Resources
	{
		$metalLevel = 0;
		$crystalLevel = 0;
		$deuteriumLevel = 0;
		while ($this->getStorageCapacity($metalLevel) < $resources->getMetal()) {
			$metalLevel++;
		}
		while ($this->getStorageCapacity($crystalLevel) < $resources->getCrystal()) {
			$crystalLevel++;
		}
		while ($this->getStorageCapacity($deuteriumLevel) < $resources->getDeuterium()) {
			$deuteriumLevel++;
		}
		return new Resources($metalLevel, $crystalLevel, $deuteriumLevel);
	}

}
