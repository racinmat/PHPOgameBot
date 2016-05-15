<?php

namespace App\Model\Queue;
 
use App\Model\Queue\Command\ICommand;
use Nette\Object;
use Ramsey\Uuid\Uuid;

class QueueManager extends Object
{

	/** @var QueueFileRepository */
	private $queueRepository;

	/**
	 * @param QueueFileRepository $queueRepository
	 */
	public function __construct(QueueFileRepository $queueRepository)
	{
		$this->queueRepository = $queueRepository;
	}

	public function addToQueue(ICommand $command)
	{
		$queue = $this->queueRepository->loadQueue();
		$queue->add($command);
		$this->queueRepository->saveQueue($queue);
	}

	public function removeFromQueue(Uuid $uuid)
	{
		$queue = $this->queueRepository->loadQueue();
		$queue = $queue->filter(function (ICommand $c) use ($uuid) {
			return ! $c->getUuid()->equals($uuid);
		});
		$this->queueRepository->saveQueue($queue);
	}

	public function moveCommandUp(Uuid $uuid)
	{
		$queue = $this->queueRepository->loadQueue();
		foreach ($queue as $key => $item) {
			if ($item->getUuid()->equals($uuid) && $key !== 0) {
				$temp = $queue[$key];
				$queue[$key] = $queue[$key - 1];
				$queue[$key - 1] = $temp;
				break;
			}
		}
		$this->queueRepository->saveQueue($queue);
	}

	public function moveCommandDown(Uuid $uuid)
	{
		$queue = $this->queueRepository->loadQueue();
		foreach ($queue as $key => $item) {
			if ($item->getUuid()->equals($uuid) && $key !== count($queue) - 1) {
				$temp = $queue[$key];
				$queue[$key] = $queue[$key + 1];
				$queue[$key + 1] = $temp;
				break;
			}
		}
		$this->queueRepository->saveQueue($queue);
	}

}