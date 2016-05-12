<?php

namespace App\Model\Game;
 
use App\Model\Entity\Planet;
use Carbon\Carbon;
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

	public function __construct(EntityManager $entityManager, \AcceptanceTester $acceptanceTester)
	{
		$this->entityManager = $entityManager;
		$this->planetRepository = $entityManager->getRepository(Planet::class);
		$this->I = $acceptanceTester;
	}

	/**
	 * @return Planet
	 */
	public function getMyHomePlanet()
	{
		return $this->planetRepository->findOneBy(['my' => true]);
	}

	public function refreshResourceData()
	{
		//zatím pouze na mou planetu, v budoucnu nude přijímat jako argument planetu a případně pošle sondy
		$I = $this->I;
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
		$this->entityManager->flush($planet);
	}
}