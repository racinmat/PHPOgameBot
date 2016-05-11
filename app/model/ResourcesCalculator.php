<?php

namespace App\Model;
 
use App\Enum\Building;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Resources;
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

	/**
	 * @param Planet $planet
	 * @param Building $building
	 * @param int $currentLevel
	 * @return Carbon
	 */
	public function getTimeToEnoughResourcesForBuilding(Planet $planet, Building $building, int $currentLevel)
	{
		return $this->getTimeToResources($planet, $building->getPriceToNextLevel($currentLevel));
	}

	/**
	 * @param Planet $planet
	 * @param Resources $expected
	 * @return Carbon
	 */
	public function getTimeToResources(Planet $planet, Resources $expected)
	{
		$resources = $planet->getResources();
		$time = $planet->getLastVisited();

		$difference = $expected->subtract($resources);

		$productionPerHour = $this->getProductionPerHour($planet->getMetalMineLevel(), $planet->getCrystalMineLevel(), $planet->getDeuteriumMineLevel(), $planet->getAverageTemperature());

		$hoursToProduce = $difference->divide($productionPerHour);
		$metalHours = $difference->getMetal() / $productionPerHour->getMetal();
		$crystalHours = $difference->getCrystal() / $productionPerHour->getCrystal();
		$deuteriumHours = $difference->getDeuterium() / $productionPerHour->getDeuterium();

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
