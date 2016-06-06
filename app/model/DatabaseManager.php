<?php

namespace App\Model;

use App\Model\Entity\Planet;
use App\Model\Entity\Player;
use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\ValueObject\Coordinates;
use App\Utils\Functions;
use App\Utils\ArrayCollection;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette\Object;


class DatabaseManager extends Object
{

	/** @var EntityManager */
	private $entityManager;

	/** @var EntityRepository */
	private $planetRepository;

	/** @var EntityRepository */
	private $playerRepository;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
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
		$statuses = $command->getStatuses();
		$orderBy = [];
		if ($command->isOrderActive()) {
			$orderBy[$command->getOrderBy()->getValue()] = $command->getOrderType()->getValue();
		}
		return $this->planetRepository->findBy(['player.status' => $statuses->toArray()], $orderBy, $command->getLimit());
	}

	public function removePlanet(Coordinates $coordinates)
	{
		$planet = $this->getPlanet($coordinates);
		$this->entityManager->remove($planet);
		$this->entityManager->flush();
	}
}