<?php

namespace App\Presenters;

use App\Components\IDisplayCommandFactory;


use App\Model\DatabaseManager;
use App\Model\PlanetCalculator;
use App\Model\Queue\QueueFileRepository;

use App\Model\ValueObject\Coordinates;
use App\Utils\ArrayCollection;
use Carbon\Carbon;

use Nette\Utils\Strings;


class FarmsPresenter extends BasePresenter
{

	/**
	 * @var PlanetCalculator
	 * @inject
	 */
	public $planetCalculator;

	public function renderDefault()
	{
		list($resources, $lastVisited) = $this->planetCalculator->getResourcesEstimateAndLastVisitedForInactivePlanets();
		$this->template->resources = $resources;
		$this->template->lastVisited = $lastVisited;
	}
	
}
