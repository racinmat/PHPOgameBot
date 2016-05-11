<?php

namespace App\Model;
 
use App\Enum\Building;
use App\Enum\Defense;
use App\Model\Command\BuildDefenseCommand;
use App\Model\Command\ICommand;
use App\Model\Command\UpgradeBuildingCommand;
use App\Model\Entity\QueueItem;
use App\Utils\Functions;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette\Object;
use Nette\Utils\Json;

class QueueProducer extends Object
{

	/** @var QueueRepository */
	private $queueRepository;

	/**
	 * @param QueueRepository $queueRepository
	 */
	public function __construct(QueueRepository $queueRepository)
	{
		$this->queueRepository = $queueRepository;
	}

	public function addToQueue(ICommand $command)
	{
		$queue = $this->queueRepository->loadQueue();
		$queue[] = $command;
		$this->queueRepository->saveQueue($queue);
	}

}