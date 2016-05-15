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
use Doctrine\Common\Collections\ArrayCollection;
use Nette;
 
class ResourcesCalculator extends Nette\Object
{

	/** @var int */
	private $acceleration;

	public function __construct(int $acceleration)
	{
		$this->acceleration = $acceleration;
	}

	public function isEnoughResourcesForUpgrade(Planet $planet, Upgradable $upgradable) : bool
	{
		$currentLevel = $upgradable->getCurrentLevel($planet);
		$missing = $this->getMissingResources($planet, $upgradable->getPriceToNextLevel($currentLevel));
		$enough = $missing->forAll(Functions::isZero());
		echo $enough ? 'Enough resources to upgrade.' . PHP_EOL : 'Not enough resources to upgrade.' . PHP_EOL;
		return $enough;
	}

	public function isEnoughResourcesForBuild(Planet $planet, Buildable $buildable, int $amount) : bool
	{
		$missing = $this->getMissingResources($planet, $buildable->getPrice()->multiplyByScalar($amount));
		$enough = $missing->forAll(Functions::isZero());
		echo $enough ? 'Enough resources to build.' . PHP_EOL : 'Not enough resources to build.' . PHP_EOL;
		return $enough;
	}

	public function getTimeToEnoughResourcesForUpgrade(Planet $planet, Upgradable $upgradable) : Carbon
	{
		$currentLevel = $upgradable->getCurrentLevel($planet);
		$missingResources = $this->getMissingResources($planet, $upgradable->getPriceToNextLevel($currentLevel));
		return $this->getTimeToResources($planet, $missingResources);
	}

	public function getTimeToEnoughResourcesForBuild(Planet $planet, Buildable $buildable, int $amount) : Carbon
	{
		$missingResources = $this->getMissingResources($planet, $buildable->getPrice()->multiplyByScalar($amount));
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

		$maxHours = max($missing->divide($productionPerHour));
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

	public function getProductionPerHour(Planet $planet) : Resources
	{
		return new Resources(
			$this->getMetalProductionPerHour($planet->getMetalMineLevel()),
			$this->getCrystalProductionPerHour($planet->getCrystalMineLevel()),
			$this->getDeuteriumProductionPerHour($planet->getDeuteriumMineLevel(), $planet->getAverageTemperature())
		);
	}

}
