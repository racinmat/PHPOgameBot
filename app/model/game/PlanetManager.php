<?php

namespace App\Model\Game;
 
use App\Enum\Building;
use App\Enum\Enhanceable;
use App\Enum\MenuItem;
use App\Enum\Research;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Coordinates;
use App\Utils\Functions;
use App\Utils\OgameParser;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette\Object;
use Nette\Utils\Strings;

class PlanetManager extends Object
{

	/** @var EntityManager */
	private $entityManager;

	/** @var EntityRepository */
	private $planetRepository;

	/** @var \AcceptanceTester */
	private $I;

	/** @var Menu */
	private $menu;

	public function __construct(EntityManager $entityManager, \AcceptanceTester $acceptanceTester, Menu $menu)
	{
		$this->entityManager = $entityManager;
		$this->planetRepository = $entityManager->getRepository(Planet::class);
		$this->I = $acceptanceTester;
		$this->menu = $menu;
	}

	/**
	 * @return Planet
	 */
	public function getMyHomePlanet()
	{
		return $this->planetRepository->findOneBy(['my' => true]);
	}

	public function getPlanet(Coordinates $coordinates) : Planet
	{
		return $this->planetRepository->findOneBy([
			'coordinates.galaxy' => $coordinates->getGalaxy(),
			'coordinates.system' => $coordinates->getSystem(),
			'coordinates.planet' => $coordinates->getPlanet()
		]);
	}

	public function refreshResourcesData(Planet $planet)
	{
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

		$this->entityManager->flush($planet);
	}

	public function refreshAllResourcesData()
	{
		$this->getAllMyPlanets()->forAll(function ($key, Planet $planet) {
			$this->refreshResourcesData($planet);
		});
	}

	/**
	 * @throws \Exception
	 */
	public function refreshAllData()
	{
		$this->getAllMyPlanets()->forAll(function ($key, Planet $planet) {
			$this->refreshPlanetData($planet);
		});
	}

	public function refreshPlanetData(Planet $planet)
	{
		$I = $this->I;

		$this->refreshResourcesData($planet);
		//buildings level
		foreach (Building::getEnums() as $building) {
			$this->menu->goToPage($building->getMenuLocation());
			$level = $I->grabTextFrom($building->getClassSelector() . ' .level');
			usleep(random_int(500000, 1000000));
			$building->setCurrentLevel($planet, $level);
		}

		//research level
		foreach (Research::getEnums() as $research) {
			$this->menu->goToPage($research->getMenuLocation());
			$level = $I->grabTextFrom($research->getClassSelector() . ' .level');
			usleep(random_int(500000, 1000000));
			$research->setCurrentLevel($planet, $level);
		}

		$this->entityManager->flush($planet);

	}

	public function getTimeToFinish(Enhanceable $enhanceable) : Carbon
	{
		$I = $this->I;
		$this->menu->goToPage(MenuItem::_(MenuItem::OVERVIEW));
		if ($I->seeElementExists($enhanceable->getEnhanceCountdownSelector())) {
			$interval = $I->grabTextFrom($enhanceable->getEnhanceCountdownSelector());
			return Carbon::now()->add(OgameParser::parseOgameTimeInterval($interval));
		}
		echo 'Countdown text not found. Can not find when to run queue next time.' . PHP_EOL;
		return Carbon::now();
	}

	public function currentlyProcessing(Enhanceable $enhanceable) : bool
	{
		$this->menu->goToPage(MenuItem::_(MenuItem::OVERVIEW));
		return ! $this->I->seeExists($enhanceable->getFreeToEnhanceText(), $enhanceable->getEnhanceStatusSelector());
	}

	/**
	 * @return Coordinates[]|ArrayCollection
	 */
	public function getAllMyPlanetsCoordinates() : ArrayCollection
	{
		return (new ArrayCollection($this->I->grabMultiple('.planetlink span.planet-koords')))->map(Functions::coordinatesToValueObject());
	}

	/**
	 * @return Planet[]|ArrayCollection
	 */
	public function getAllMyPlanets() : ArrayCollection
	{
		return $this->getAllMyPlanetsCoordinates()->map(function (Coordinates $coordinates) {
			$planet = $this->getPlanet($coordinates);
			if ($planet === null) {
				$this->addPlanet($coordinates, true);
			}
			return $planet;
		});
	}

	public function addPlanet(Coordinates $coordinates, bool $my)
	{
		$planet = new Planet('', $coordinates, $my);
		$this->entityManager->persist($planet);
		$this->entityManager->flush($planet);
	}

	public function getAllMyPlanetsFromDatabase()
	{
		return $this->planetRepository->findAssoc(['my' => true], 'id');
	}

	public function getPlanetById($id) : Planet
	{
		return $this->planetRepository->find($id);
	}
}