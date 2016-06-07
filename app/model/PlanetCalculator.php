<?php

namespace App\Model;

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

	/**
	 * @return Resources[]
	 */
	public function getResourcesEstimateForInactivePlanets() : array
	{
		$planets = $this->databaseManager->getInactivePlanets();
		$resources = [];
		foreach ($planets as $planet) {
			$resources[$planet->getCoordinates()->toString()] = $this->resourcesCalculator->getResourcesEstimateForTime($planet, Carbon::now());
		}
		uasort($resources, function(Resources $a, Resources $b) {return $b->getTotal() - $a->getTotal();});
		return $resources;
	}
}