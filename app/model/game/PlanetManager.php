<?php

namespace App\Model\Game;
 
use App\Enum\Building;
use App\Enum\Enhanceable;
use App\Enum\MenuItem;
use App\Enum\Research;
use App\Model\DatabasePlanetManager;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Coordinates;
use App\Utils\Functions;
use App\Utils\OgameParser;
use App\Utils\Random;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Monolog\Logger;
use Nette\Object;
use Nette\Utils\Strings;

class PlanetManager extends Object
{

	/** @var \AcceptanceTester */
	private $I;

	/** @var Menu */
	private $menu;
	
	/** @var DatabasePlanetManager */
	private $databasePlanetManager;

	/** @var Logger */
	private $logger;

	public function __construct(DatabasePlanetManager $databasePlanetManager, \AcceptanceTester $acceptanceTester, Menu $menu, Logger $logger)
	{
		$this->databasePlanetManager = $databasePlanetManager;
		$this->I = $acceptanceTester;
		$this->menu = $menu;
		$this->logger = $logger;
	}

	/**
	 * @param Coordinates $coordinates
	 * @return Planet|null
	 */
	public function getPlanet(Coordinates $coordinates)
	{
		return $this->databasePlanetManager->getPlanet($coordinates);
	}

	protected function refreshResourcesData(Planet $planet)
	{
		$this->menu->goToPlanet($planet);
		//předělat na více planet a nebo dát do argumentu planetu
		$I = $this->I;

		//resources
		$metal = $I->grabTextFrom('#resources_metal');
		$crystal = $I->grabTextFrom('#resources_crystal');
		$deuterium = $I->grabTextFrom('#resources_deuterium');

		$metal = Strings::replace($metal, '~\.~');
		$crystal = Strings::replace($crystal, '~\.~');
		$deuterium = Strings::replace($deuterium, '~\.~');

		//v budoucnu předělat na nastavení jedním DTO, které bude mít suroviny a čas
		$planet->setMetal($metal);
		$planet->setCrystal($crystal);
		$planet->setDeuterium($deuterium);
		$planet->setLastVisited(Carbon::now());

		$this->databasePlanetManager->flush($planet);
	}

	public function refreshAllResourcesData()
	{
		$this->getAllMyPlanets()->forAll(function ($key, Planet $planet) {
			$this->refreshResourcesData($planet);
			return true;
		});
	}

	/**
	 * @throws \Exception
	 */
	public function refreshAllData()
	{
		$this->getAllMyPlanets()->forAll(function ($key, Planet $planet) {
			$this->refreshPlanetData($planet);
			return true;
		});
	}

	public function refreshPlanetData(Planet $planet)
	{
		$I = $this->I;

		$this->menu->goToPlanet($planet);

		//planet name
		$planetName = $I->grabTextFrom('.planetlink.active span.planet-name');
		$planet->setName($planetName);
		
		$this->refreshResourcesData($planet);
		//buildings level
		foreach (Building::getEnums() as $building) {
			$this->menu->goToPage($building->getMenuLocation());
			$level = $I->grabTextFrom($building->getClassSelector() . ' .level');
			usleep(Random::microseconds(0.5, 1));
			$building->setCurrentLevel($planet, $level);
		}

		//research level
		foreach (Research::getEnums() as $research) {
			$this->menu->goToPage($research->getMenuLocation());
			$level = $I->grabTextFrom($research->getClassSelector() . ' .level');
			usleep(Random::microseconds(0.5, 1));
			$research->setCurrentLevel($planet, $level);
		}

		$this->databasePlanetManager->flush($planet);

	}

	public function getTimeToFinish(Enhanceable $enhanceable) : Carbon
	{
		$I = $this->I;
		$this->menu->goToPage(MenuItem::_(MenuItem::OVERVIEW));
		if ($I->seeElementExists($enhanceable->getEnhanceCountdownSelector())) {
			$interval = $I->grabTextFrom($enhanceable->getEnhanceCountdownSelector());
			return Carbon::now()->add(OgameParser::parseOgameTimeInterval($interval));
		}
		$this->logger->addCritical('Countdown text not found. Can not find when to run queue next time.');
		return Carbon::now();
	}

	public function currentlyProcessing(Enhanceable $enhanceable) : bool
	{
		$this->menu->goToPage(MenuItem::_(MenuItem::OVERVIEW));
		$currentlyProcessing = ! $this->I->seeExists($enhanceable->getFreeToEnhanceText(), $enhanceable->getEnhanceStatusSelector());
		if ($currentlyProcessing) {
			$this->logger->addDebug("Currently processing {$enhanceable->getValue()}.");
		} else {
			$class = get_class($enhanceable);
			$this->logger->addDebug("Not processing {$class}. {$enhanceable->getValue()} can be processed.");
		}
		return $currentlyProcessing;
	}

	/**
	 * @return Coordinates[]|ArrayCollection
	 */
	public function getAllMyPlanetsCoordinates() : ArrayCollection
	{
		return (new ArrayCollection($this->I->grabMultiple('.planetlink span.planet-koords')))->map(Functions::textCoordinatesToValueObject());
	}

	/**
	 * @return Planet[]|ArrayCollection
	 */
	public function getAllMyPlanets() : ArrayCollection
	{
		return $this->getAllMyPlanetsCoordinates()->map(function (Coordinates $coordinates) {
			$planet = $this->getPlanet($coordinates);
			if ($planet === null) {
				$this->databasePlanetManager->addPlanet($coordinates, true);
			}
			return $planet;
		});
	}

}