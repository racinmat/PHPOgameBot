<?php

namespace App\Model;
 
use App\Enum\Building;
use App\Model\Entity\QueueItem;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette;
 
class QueueConsumer extends Nette\Object
{

	/** @var EntityManager */
	private $entityManager;

	/** @var EntityRepository */
	private $queueRepository;

	/** @var BuildingsManager */
	private $buildingsManager;

	/** @var PlanetManager */
	private $planetManager;

	/** @var ResourcesCalculator */
	private $resourcesCalculator;

	public function __construct(EntityManager $entityManager, BuildingsManager $buildingsManager, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator)
	{
		$this->entityManager = $entityManager;
		$this->queueRepository = $entityManager->getRepository(QueueItem::class);
		$this->buildingsManager = $buildingsManager;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
	}

	public function processQueue()
	{
		/** @var QueueItem[] $queue */
		$queue = $this->queueRepository->findAll();
		$success = true;    //aby se zastavilo procházení fronty, když se nepodaří postavit budovu a zpracování tak skončilo
		foreach ($queue as $item) {
			if (!$success) {
				break;
			}
			switch ($item->getAction()) {
				case QueueItem::ACTION_BUILD:
					$success = $this->build($item);
					break;
			}
		}
		$this->entityManager->flush();
		if (!$success) {
			//calculate when will be possible to build next building and set the cron time
		}
	}

	/**
	 * @param QueueItem $item
	 * @return bool returns true if building is built successfully
	 */
	private function build(QueueItem $item) : bool
	{
		$building = Building::_($item->getData());
		$success = $this->buildingsManager->build($building);
		if ($success) {
			$this->entityManager->remove($item);
		}
		return $success;
	}
}