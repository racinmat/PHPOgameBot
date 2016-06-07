<?php

namespace App\Model;
 



use App\Model\Entity\Planet;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\ValueObject\Resources;

use Carbon\Carbon;

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

	public function isEnoughResources(Planet $planet, Resources $expected) : bool
	{
		$missing = $this->getMissingResources($planet, $expected);
		return $missing->isZero();
	}

	public function getTimeToEnoughResources(Planet $planet, Resources $expected) : Carbon
	{
		$missingResources = $this->getMissingResources($planet, $expected);
		return $this->getTimeToResources($planet, $missingResources);
	}

	public function getTimeToEnoughResourcesTotal(Planet $planet, int $expected) : Carbon
	{
		$missing = $this->getMissingResourcesTotal($planet, $expected);
		return $this->getTimeToResourcesTotal($planet, $missing);
	}

	public function isEnoughResourcesToEnhance(Planet $planet, IEnhanceCommand $command) : bool
	{
		$missing = $this->getMissingResources($planet, $command->getPrice($planet));
		$enough = $missing->isZero();
		$this->logger->addDebug("Checking resources to process command {$command->toString()} in planet {$planet->getCoordinates()->toString()} which needs {$command->getPrice($planet)->toString()} resources.");
		$this->logger->addDebug("Command information: {$command->getInfo($planet)}");
		if ($enough) {
			$this->logger->addDebug("Enough resources.");
		} else {
			$this->logger->addDebug("Not enough resources, missing resources: {$missing->toString()}.");
		}
		return $enough;
	}

	public function getTimeToEnoughResourcesToEnhance(Planet $planet, IEnhanceCommand $command) : Carbon
	{
		return $this->getTimeToEnoughResources($planet, $command->getPrice($planet));
	}

	private function getMissingResources(Planet $planet, Resources $expected) : Resources
	{
		return $expected->subtract($planet->getResources());
	}

	private function getMissingResourcesTotal(Planet $planet, int $expected) : int
	{
		$missing = $expected - $planet->getResources()->getTotal();
		return $missing > 0 ? $missing : 0;
	}

	private function getTimeToResources(Planet $planet, Resources $missing) : Carbon
	{
		$productionPerHour = $this->getProductionPerHour($planet);
		$hours = max($missing->divide($productionPerHour));
		return $this->addHours($planet->getLastVisited(), $hours);
	}

	private function getTimeToResourcesTotal(Planet $planet, int $missing) : Carbon
	{
		$productionPerHour = $this->getProductionPerHour($planet);
		$hours = $missing / $productionPerHour->getTotal();
		return $this->addHours($planet->getLastVisited(), $hours);
	}

	private function addHours(Carbon $time, float $hours) : Carbon
	{
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

	private function getMetalProductionPerHour(int $mineLevel, int $plasmaTechnologyLevel) : int
	{
		$base = $this->acceleration * 30;
		$mine = round($this->acceleration * 30 * $mineLevel * pow(1.1, $mineLevel));
		return $base + $mine * (1 + $plasmaTechnologyLevel * 0.01);
	}

	private function getCrystalProductionPerHour(int $mineLevel, int $plasmaTechnologyLevel) : int
	{
		$base = $this->acceleration * 15;
		$mine = round($this->acceleration * 20 * $mineLevel * pow(1.1, $mineLevel));
		return $base + $mine * (1 + $plasmaTechnologyLevel * 0.0066);
	}

	private function getDeuteriumProductionPerHour(int $mineLevel, int $plasmaTechnologyLevel, int $averageTemperature) : int
	{
		return round($this->acceleration * 10 * $mineLevel * pow(1.1, $mineLevel) * (1.36 - 0.004 * $averageTemperature)) * (1 + $plasmaTechnologyLevel * 0.0033);
	}

	public function getProductionPerHour(Planet $planet) : Resources
	{
		return new Resources(
			$this->getMetalProductionPerHour($planet->getMetalMineLevel(), $planet->getPlayer()->getPlasmaTechnologyLevel()),
			$this->getCrystalProductionPerHour($planet->getCrystalMineLevel(), $planet->getPlayer()->getPlasmaTechnologyLevel()),
			$this->getDeuteriumProductionPerHour($planet->getDeuteriumMineLevel(), $planet->getPlayer()->getPlasmaTechnologyLevel(), $planet->getAverageTemperature())
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

	public function getResourcesEstimateForTime(Planet $planet, Carbon $time) : Resources
	{
		$resources = $planet->getResources();
		$hours = $planet->getLastVisited()->diffInHours($time);
		return $this->getProductionPerHour($planet)->multiplyByScalar($hours)->add($resources)->min($this->getStoragesCapacity($planet));
	}
}
