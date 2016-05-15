<?php

namespace App\Model;
 
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

class DatabasePlanetManager extends Object
{

	/** @var EntityManager */
	private $entityManager;

	/** @var EntityRepository */
	private $planetRepository;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->planetRepository = $entityManager->getRepository(Planet::class);
	}

	/**
	 * @param Coordinates $coordinates
	 * @return Planet|null
	 */
	public function getPlanet(Coordinates $coordinates)
	{
		return $this->planetRepository->findOneBy([
			'coordinates.galaxy' => $coordinates->getGalaxy(),
			'coordinates.system' => $coordinates->getSystem(),
			'coordinates.planet' => $coordinates->getPlanet()
		]);
	}

	public function addPlanet(Coordinates $coordinates, bool $my)
	{
		$planet = new Planet('', $coordinates, $my);
		$this->entityManager->persist($planet);
		$this->entityManager->flush($planet);
	}

	public function getAllMyPlanets()
	{
		return $this->planetRepository->findAssoc(['my' => true], 'id');
	}

	public function getAllMyPlanetIdsAndCoordinates() : array
	{
		$planets = new ArrayCollection($this->getAllMyPlanets());
		return $planets->map(Functions::planetToNameAndTextCoordinates())->toArray();
	}
	
	public function getPlanetById($id) : Planet
	{
		return $this->planetRepository->find($id);
	}

	public function flush(Planet $planet)
	{
		$this->entityManager->flush($planet);
	}
}