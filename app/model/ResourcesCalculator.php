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
	 * @param int $level
	 * @return Carbon
	 */
	public function getTimeToEnoughResourcesForBuilding(Planet $planet, Building $building, int $level)
	{
		return $this->getTimeToResources($planet, $building->getPriceToNextLevel($level));
	}

	/**
	 * @param Planet $planet
	 * @param Resources $resources
	 * @return Carbon
	 */
	public function getTimeToResources(Planet $planet, Resources $resources)
	{
		$metalExpected = $resources->getMetal();
		$crystalExpected = $resources->getCrystal();
		$deuteriumExpected = $resources->getDeuterium();

		$metal = $planet->getMetal();
		$crystal = $planet->getCrystal();
		$deuterium = $planet->getDeuterium();
		$time = $planet->getLastVisited();

		$metalDiff = $metalExpected - $metal;
		$crystalDiff = $crystalExpected - $crystal;
		$deuteriumDiff = $deuteriumExpected - $deuterium;

		$metalHours = $metalDiff / $this->getMetalProductionPerHour($planet->getMetalMineLevel());
		$crystalHours = $crystalDiff / $this->getCrystalProductionPerHour($planet->getCrystalMineLevel());
		$deuteriumHours = $deuteriumDiff / $this->getDeuteriumProductionPerHour($planet->getDeuteriumMineLevel(), $planet->getAverageTemperature());

		$maxTime = max($metalHours, $crystalHours, $deuteriumHours);
		if ($maxTime <= 0) {
			return Carbon::now();
		}

		$minutes = ($maxTime - (int) $maxTime) * 60;
		return $time->addHours($maxTime)->addMinutes($minutes);
	}

	/**
	 * @param int $mineLevel
	 * @return int
	 */
	private function getMetalProductionPerHour(int $mineLevel)
	{
		return $this->acceleration * 30 * $mineLevel * pow(1.1, $mineLevel);
	}

	/**
	 * @param int $mineLevel
	 * @return int
	 */
	private function getCrystalProductionPerHour(int $mineLevel)
	{
		return $this->acceleration * 20 * $mineLevel * pow(1.1, $mineLevel);
	}

	/**
	 * @param int $mineLevel
	 * @param int $averageTemperature
	 * @return int
	 */
	private function getDeuteriumProductionPerHour(int $mineLevel, int $averageTemperature)
	{
		return (int) 10 * $mineLevel * pow(1.1, $mineLevel) * (1.36 - 0.004 * $averageTemperature);
	}
}