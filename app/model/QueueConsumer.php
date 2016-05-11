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

	public function __construct(EntityManager $entityManager, BuildingsManager $buildingsManager)
	{
		$this->entityManager = $entityManager;
		$this->queueRepository = $entityManager->getRepository(QueueItem::class);
		$this->buildingsManager = $buildingsManager;
	}

	public function processQueue()
	{
		/** @var QueueItem[] $queue */
		$queue = $this->queueRepository->findAll();
		foreach ($queue as $item) {
			switch ($item->getAction()) {
				case QueueItem::ACTION_BUILD: $this->build($item); break;
			}
		}
	}

	private function build(QueueItem $item)
	{
		$building = Building::_($item->getData());
		if ($this->buildingsManager->isEnoughResources($building)) {
			$this->buildingsManager->build($building);
			$this->entityManager->remove($item);
		}
		$this->entityManager->flush();
	}
}