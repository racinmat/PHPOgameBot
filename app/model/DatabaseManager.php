<?php

namespace App\Model;

use App\Enum\PlanetProbingStatus;
use App\Enum\PlayerStatus;
use App\Enum\ProbingStatus;
use App\Model\Entity\Planet;
use App\Model\Entity\Player;
use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\ValueObject\Coordinates;
use App\Utils\Functions;
use App\Utils\ArrayCollection;
use Carbon\Carbon;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Monolog\Logger;
use Nette\Object;
use Nette\Utils\Json;
use Tracy\Debugger;


class DatabaseManager extends Object
{

	/** @var EntityManager */
	private $entityManager;

	/** @var EntityRepository */
	private $planetRepository;

	/** @var EntityRepository */
	private $playerRepository;

	/** @var Logger  */
	private $logger;

	public function __construct(EntityManager $entityManager, Logger $logger)
	{
		$this->entityManager = $entityManager;
		$this->logger = $logger;
		$this->planetRepository = $entityManager->getRepository(Planet::class);
		$this->playerRepository = $entityManager->getRepository(Player::class);
	}

	/**
	 * @param Coordinates $coordinates
	 * @return Planet|null
	 */
	public function getPlanet(Coordinates $coordinates)
	{
		return $this->planetRepository->createQueryBuilder('planet')
			->andWhere('planet.coordinates.galaxy = :galaxy')
			->andWhere('planet.coordinates.system = :system')
			->andWhere('planet.coordinates.planet = :planet')
			->setParameters($coordinates->toArray())
			->getQuery()->getOneOrNullResult();
	}

	/**
	 * @param string $name
	 * @return Player|null
	 */
	public function getPlayer(string $name)
	{
		return $this->playerRepository->findOneBy(['name' => $name]);
	}

	public function addPlanet(Coordinates $coordinates, Player $player) : Planet
	{
		$planet = new Planet('', $coordinates, $player);
		$this->entityManager->persist($planet);
		$this->entityManager->flush($planet);
		return $planet;
	}

	public function getAllMyPlanets() : array 
	{
		$planets = $this->planetRepository->findAssoc(['player.me' => true], 'id');
		return $planets;
	}

	/**
	 * @return Coordinates[]|ArrayCollection
	 */
	public function getAllMyPlanetsCoordinates() : ArrayCollection
	{
		$planets = new ArrayCollection($this->getAllMyPlanets());
		return $planets->map(Functions::planetToCoordinates());
	}
	
	public function getAllMyPlanetsIdsNamesAndCoordinates() : array
	{
		$planets = new ArrayCollection($this->getAllMyPlanets());
		return $planets->map(Functions::planetToNameAndTextCoordinates())->toArray();
	}
	
	public function getPlanetById($id) : Planet
	{
		return $this->planetRepository->find($id);
	}

	public function flush()
	{
		$this->entityManager->flush();
	}

	public function addPlayer(string $name, bool $me = false) : Player
	{
		$player = new Player($name, $me);
		$this->entityManager->persist($player);
		return $player;
	}

	public function getMe() : Player
	{
		return $this->playerRepository->findOneBy(['me' => true]);
	}

	/**
	 * @param ProbePlayersCommand $command
	 * @return Planet[]
	 */
	public function getPlanetsFromCommand(ProbePlayersCommand $command) : array
	{
		$filter = [
			'player.status' => $command->getStatuses()->toArray(),
			'player.probingStatus' => $command->getProbingStatuses()->toArray()
		];
		$orderBy = [];
		if ($command->isOrderActive()) {
			$orderBy[$command->getOrderBy()->getValue()] = $command->getOrderType()->getValue();
		}
		return $this->planetRepository->findBy($filter, $orderBy, $command->getLimit());
	}

	public function removePlanet(Coordinates $coordinates)
	{
		$planet = $this->getPlanet($coordinates);
		$this->entityManager->remove($planet);
		$this->entityManager->flush();
	}

	public function removePlanetsInSystemExceptOf(Coordinates $coordinates, ArrayCollection $planetsInSystem)
	{
		$qb = $this->planetRepository->createQueryBuilder('planet')
			->andWhere('planet.coordinates.galaxy = :galaxy')
			->andWhere('planet.coordinates.system = :system')
			->setParameters([
				'galaxy' => $coordinates->getGalaxy(),
				'system' => $coordinates->getSystem()
			]);
		if ( ! $planetsInSystem->isEmpty()) {
			$qb->andWhere('planet.coordinates.planet NOT IN (:planets)')
				->setParameter('planets', $planetsInSystem->toArray());
		}
		$planets = $qb->getQuery()->getResult();
		$coords = (new ArrayCollection($planets))->map(Functions::planetToCoordinates());
		$this->logger->addInfo("Removing non existing planets from coordinates " . Json::encode($coords->toArray()));
		$this->entityManager->remove($planets);
		$this->entityManager->flush();
	}

	public function getAllPlanetsCount() : int
	{
		return $this->planetRepository->countBy([]);
	}

	public function getAllPlayersCount() : int
	{
		return $this->playerRepository->countBy([]);
	}

	public function getPlanetsWithAllInformationCount() : int
	{
		return $this->planetRepository->countBy(['probingStatus' => PlanetProbingStatus::GOT_ALL_INFORMATION]);
	}

	public function getPlanetsWithoutFleetAndDefenseCount() : int
	{
		return $this->planetRepository->countBy($this->getNoFleetAndNoDefenseFilter());
	}

	public function getInactivePlayersCount()
	{
		return $this->playerRepository->countBy(['status' => [PlayerStatus::STATUS_INACTIVE, PlayerStatus::STATUS_LONG_INACTIVE]]);
	}

	public function getInactivePlanetsCount()
	{
		return $this->planetRepository->countBy(['player.status' => [PlayerStatus::STATUS_INACTIVE, PlayerStatus::STATUS_LONG_INACTIVE]]);
	}

	/**
	 * @param Carbon $lastVisitedFrom
	 * @param Carbon $lastVisitedTo
	 * @return Entity\Planet[]|array
	 */
	public function getInactiveDefenselessPlanets(Carbon $lastVisitedFrom = null, Carbon $lastVisitedTo = null) : array
	{
		$filters = array_merge(
			['player.status' => [PlayerStatus::STATUS_INACTIVE, PlayerStatus::STATUS_LONG_INACTIVE]],
			$this->getNoFleetAndNoDefenseFilter()
		);
		if ($lastVisitedFrom !== null) {
			$filters['lastVisited >'] = $lastVisitedFrom;
		}
		if ($lastVisitedTo !== null) {
			$filters['lastVisited <'] = $lastVisitedTo;
		}
		return $this->planetRepository->findBy($filters);
	}

	private function getNoFleetAndNoDefenseFilter() : array
	{
		return [
			'probingStatus' => PlanetProbingStatus::GOT_ALL_INFORMATION,
			'rocketLauncherAmount' => 0,
			'lightLaserAmount' => 0,
			'heavyLaserAmount' => 0,
			'ionCannonAmount' => 0,
			'gaussCannonAmount' => 0,
			'plasmaTurretAmount' => 0,
			'smallShieldDomeAmount' => 0,
			'largeShieldDomeAmount' => 0,
			'smallCargoShipAmount' => 0,
			'largeCargoShipAmount' => 0,
			'lightFighterAmount' => 0,
			'heavyFighterAmount' => 0,
			'cruiserAmount' => 0,
			'battleshipAmount' => 0,
			'battlecruiserAmount' => 0,
			'destroyerAmount' => 0,
			'deathstarAmount' => 0,
			'bomberAmount' => 0,
			'recyclerAmount' => 0,
			'espionageProbeAmount' => 0,
			'solarSatelliteAmount' => 0,
			'colonyShipAmount' => 0
		];
	}

}