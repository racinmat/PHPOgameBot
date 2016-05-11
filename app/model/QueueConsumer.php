<?php

namespace App\Model;
 
use App\Enum\Building;
use App\Enum\Defense;
use App\Model\Entity\QueueItem;
use Carbon\Carbon;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette\Object;
use Nette\Utils\Json;

class QueueConsumer extends Object
{

	/** @var EntityManager */
	private $entityManager;

	/** @var EntityRepository */
	private $queueRepository;

	/** @var BuildingManager */
	private $buildingsManager;

	/** @var PlanetManager */
	private $planetManager;

	/** @var ResourcesCalculator */
	private $resourcesCalculator;

	/** @var CronManager */
	private $cronManager;

	/** @var DefenseManager */
	private $defenseManager;

	public function __construct(EntityManager $entityManager, BuildingManager $buildingsManager, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator, CronManager $cronManager, DefenseManager $defenseManager)
	{
		$this->entityManager = $entityManager;
		$this->queueRepository = $entityManager->getRepository(QueueItem::class);
		$this->buildingsManager = $buildingsManager;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->cronManager = $cronManager;
		$this->defenseManager = $defenseManager;
	}

	public function processQueue()
	{
		/** @var QueueItem[] $queue */
		$queue = $this->queueRepository->findAll();
		$success = true;    //aby se zastavilo procházení fronty, když se nepodaří postavit budovu a zpracování tak skončilo
		$lastItem = null;
		foreach ($queue as $item) {
			switch ($item->getAction()) {
				case QueueItem::ACTION_UPGRADE:
					$success = $this->upgrade($item);
					break;
				case QueueItem::ACTION_BUILD:
					$success = $this->build($item);
					break;
			}
			if ($success) {
				$this->entityManager->remove($item);
			}
			$lastItem = $item;
			if (!$success) {
				break;
			}
		}
		$this->entityManager->flush();
		if (!$success) {
			$datetime = Carbon::now();
			$this->planetManager->refreshResourceData();
			$planet = $this->planetManager->getMyHomePlanet();
			switch ($lastItem->getAction()) {
				case QueueItem::ACTION_UPGRADE:
					$datetime = $this->resourcesCalculator->getTimeToEnoughResourcesForBuilding($planet, Building::_($lastItem->getData()));
					break;
				case QueueItem::ACTION_BUILD:
					$data = Json::decode($lastItem->getData(), true);
					$amount = $data['amount'];
					$type = Defense::_($data['type']);
					$datetime = $this->resourcesCalculator->getTimeToEnoughResourcesFoDefense($planet, $type, $amount);
					break;
			}
			$this->cronManager->setNextStart($datetime);
		}
	}

	/**
	 * @param QueueItem $item
	 * @return bool returns true if building is built successfully
	 */
	private function upgrade(QueueItem $item) : bool
	{
		$building = Building::_($item->getData());
		return $this->buildingsManager->upgrade($building);
	}

	private function build(QueueItem $item)
	{
		//možná přehodit frontu do elasticsearche nebo jiného nestrukturovaného úložiště, abych měl různé druhy úkolů
		$data = Json::decode($item->getData(), true);
		$amount = $data['amount'];
		$defense = Defense::_($data['type']);
		return $this->defenseManager->build($defense, $amount);
	}

}