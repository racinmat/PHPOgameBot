<?php

namespace App\Model;

use App\Enum\Ships;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Resources;
use App\Utils\ArrayCollection;
use Carbon\Carbon;
use Nette\Object;

class PlanetCalculator extends Object
{

	/** @var DatabaseManager */
	private $databaseManager;

	/** @var ResourcesCalculator */
	private $resourcesCalculator;

	public function __construct(DatabaseManager $databaseManager, ResourcesCalculator $resourcesCalculator)
	{
		$this->databaseManager = $databaseManager;
		$this->resourcesCalculator = $resourcesCalculator;
	}

	public function getResourcesEstimateAndLastVisitedForInactivePlanets()
	{
		$planets = $this->databaseManager->getInactiveDefenselessPlanets(null, null, true);
		$resources = $this->getResourcesEstimateForPlanets($planets);
		$lastVisited = [];
		foreach ($planets as $planet) {
			$lastVisited[$planet->getCoordinates()->toString()] = $planet->getLastVisited();
		}
		return [$resources, $lastVisited];
	}

	private function getResourcesEstimateForPlanets(array $planets) : array
	{
		$resources = [];
		foreach ($planets as $planet) {
			$resources[$planet->getCoordinates()->toString()] = $this->resourcesCalculator->getResourcesEstimateForTime($planet, Carbon::now());
		}
		uasort($resources, function(Resources $a, Resources $b) {return $b->getTotal() - $a->getTotal();});
		return $resources;
	}

	public function getFarms(int $limit, Carbon $lastVisitedFrom = null, Carbon $lastVisitedTo = null) : array
	{
		$planets = $this->databaseManager->getInactiveDefenselessPlanets($lastVisitedFrom, $lastVisitedTo);
		$resources = $this->getResourcesEstimateForPlanets($planets);
		$topResources = array_slice($resources, 0, $limit, true);
		foreach ($planets as $key => $planet) {
			if ( ! array_key_exists($planet->getCoordinates()->toString(), $topResources)) {
				unset($planets[$key]);
			}
		}
		return $planets;
	}

	public function countShipsNeededToFarmResources(Planet $planet, Ships $ship) : int
	{
		$resourcesNow = $this->resourcesCalculator->getResourcesEstimateForTime($planet, Carbon::now());
		return ceil($resourcesNow->getTotal() / 2 / $ship->getCapacity());  //Every attack takes only one half of resources
	}

	public function saveResourcesEstimateAfterAttack(Planet $planet)
	{
		$resources = $this->resourcesCalculator->getResourcesEstimateForTime($planet, Carbon::now());
		$resources = $resources->divideByScalar(2);
		$planet->setResources($resources);
		$this->databaseManager->flush();
	}

	/**
	 * @param Planet[] $planets
	 */
	public function saveResourcesEstimateAfterAttackForPlanets(array $planets)
	{
		foreach ($planets as $planet) {
			$this->saveResourcesEstimateAfterAttack($planet);
		}
	}

}
