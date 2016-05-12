<?php

namespace App\Model\Queue;
 
use App\Model\Queue\Command\ICommand;
use Nette\Object;

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