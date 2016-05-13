<?php

namespace App\Model\Game;
 
use App\Enum\Building;
use App\Enum\Enhanceable;
use App\Enum\MenuItem;
use App\Enum\Research;
use App\Model\Entity\Planet;
use Carbon\Carbon;
use Carbon\CarbonInterval;
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

	/**
	 * @throws \Exception
	 */
	public function refreshData()
	{
		//zatím pouze na mou planetu, v budoucnu nude přijímat jako argument planetu a případně pošle sondy
		$I = $this->I;

		//resources
		$metal = $I->grabTextFrom('#resources_metal');
		$crystal = $I->grabTextFrom('#resources_crystal');
		$deuterium = $I->grabTextFrom('#resources_deuterium');

		$metal = Strings::replace($metal, '~\.~');
		$crystal = Strings::replace($crystal, '~\.~');
		$deuterium = Strings::replace($deuterium, '~\.~');

		$planet = $this->getMyHomePlanet();
		//v budoucnu předělat na nastavení jedním DTO, které bude mít suroviny a čas
		$planet->setMetal($metal);
		$planet->setCrystal($crystal);
		$planet->setDeuterium($deuterium);
		$planet->setLastVisited(Carbon::now());

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

	protected function parseOgameTimeInterval(string $interval) : CarbonInterval
	{
		$params = Strings::match($interval, '~((?<weeks>\d{1,2})t)? ?((?<days>\d{1,2})d)? ?((?<hours>\d{1,2})hod)? ?((?<minutes>\d{1,2})min)? ?((?<seconds>\d{1,2})s)?~');
		return new CarbonInterval(0, 0, $params['weeks'], $params['days'], $params['hours'], $params['minutes'], $params['seconds']);
	}

	public function getTimeToFinish(Enhanceable $enhanceable) : Carbon
	{
		$I = $this->I;
		$this->menu->goToPage(MenuItem::_(MenuItem::OVERVIEW));
		$I->wait(1);
		if ($I->seeElementExists("{$enhanceable->getEnhanceStatusSelector()} #Countdown")) {
			$interval = $I->grabTextFrom("{$enhanceable->getEnhanceStatusSelector()} #Countdown");
			return Carbon::now()->add($this->parseOgameTimeInterval($interval));
		}
		return Carbon::now();
	}

	public function currentlyProcessing(Enhanceable $enhanceable) : bool
	{
		$this->menu->goToPage(MenuItem::_(MenuItem::OVERVIEW));
		return ! $this->I->seeExists($enhanceable->getFreeToEnhanceText(), $enhanceable->getEnhanceStatusSelector());
	}

}