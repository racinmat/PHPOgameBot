<?php

namespace App\Model;
 
use App\Enum\Buildable;
use App\Enum\Building;
use App\Enum\Defense;
use App\Enum\Upgradable;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Resources;
use App\Utils\Functions;
use Carbon\Carbon;
use Nette;
 
class ResourcesCalculator extends Nette\Object
{

	/** @var int */
	private $acceleration;

	public function __construct(int $acceleration)
	{
		$this->acceleration = $acceleration;
	}

	public function isEnoughResourcesForUpgrade(Planet $planet, Upgradable $upgradable)
	{
		$currentLevel = $upgradable->getCurrentLevel($planet);
		$missing = $this->getMissingResources($planet, $upgradable->getPriceToNextLevel($currentLevel));
		return $missing->forAll(Functions::isZero());
	}

	public function isEnoughResourcesForBuild(Planet $planet, Buildable $buildable, int $amount)
	{
		$missing = $this->getMissingResources($planet, $buildable->getPrice()->multiplyScalar($amount));
		return $missing->forAll(Functions::isZero());
	}

	public function getTimeToEnoughResourcesForUpgrade(Planet $planet, Upgradable $upgradable) : Carbon
	{
		$currentLevel = $upgradable->getCurrentLevel($planet);
		$missingResources = $this->getMissingResources($planet, $upgradable->getPriceToNextLevel($currentLevel));
		return $this->getTimeToResources($planet, $missingResources);
	}

	public function getTimeToEnoughResourcesForBuild(Planet $planet, Buildable $buildable, int $amount) : Carbon
	{
		$missingResources = $this->getMissingResources($planet, $buildable->getPrice()->multiplyScalar($amount));
		return $this->getTimeToResources($planet, $missingResources);
	}

	private function getMissingResources(Planet $planet, Resources $expected) : Resources
	{
		return $expected->subtract($planet->getResources());
	}

	private function getTimeToResources(Planet $planet, Resources $missing) : Carbon
	{
		$time = $planet->getLastVisited();

		$productionPerHour = $this->getProductionPerHour($planet->getMetalMineLevel(), $planet->getCrystalMineLevel(), $planet->getDeuteriumMineLevel(), $planet->getAverageTemperature());

		$metalHours = $missing->getMetal() / $productionPerHour->getMetal();
		$crystalHours = $missing->getCrystal() / $productionPerHour->getCrystal();
		$deuteriumHours = $missing->getDeuterium() / $productionPerHour->getDeuterium();

		$maxHours = max($metalHours, $crystalHours, $deuteriumHours);
		if ($maxHours <= 0) {
			return Carbon::now();
		}

		$minutes = ($maxHours - (int) $maxHours) * 60;
		return $time->addHours((int) $maxHours)->addMinutes((int) $minutes);
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

	private function getProductionPerHour(int $metalMineLevel, int $crystalMineLevel, int $deuteriumMineLevel, $averageTemperature) : Resources
	{
		return new Resources(
			$this->getMetalProductionPerHour($metalMineLevel),
			$this->getCrystalProductionPerHour($crystalMineLevel),
			$this->getDeuteriumProductionPerHour($deuteriumMineLevel, $averageTemperature)
		);
	}

}
