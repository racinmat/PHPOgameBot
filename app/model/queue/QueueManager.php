<?php

namespace App\Model\Queue;
 
use App\Model\Queue\Command\ICommand;
use App\Utils\ArrayCollection;
use App\Utils\Functions;
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

	public function addToRepetitiveCommands(ICommand $command)
	{
		$queue = $this->queueRepository->loadRepetitiveCommands();
		$queue->add($command);
		$this->queueRepository->saveRepetitiveCommands($queue);
	}

	public function removeCommand(Uuid $uuid)
	{
		if ($this->isCommandInQueue($uuid)) {
			$queue = $this->queueRepository->loadQueue();
			$queue = $queue->filter(function (ICommand $c) use ($uuid) {
				return ! $c->getUuid()->equals($uuid);
			});
			$this->queueRepository->saveQueue($queue);
			return;
		}
		if ($this->isCommandInRepetitive($uuid)) {
			$queue = $this->queueRepository->loadRepetitiveCommands();
			$queue = $queue->filter(function (ICommand $c) use ($uuid) {
				return ! $c->getUuid()->equals($uuid);
			});
			$this->queueRepository->saveRepetitiveCommands($queue);
			return;
		}
	}

	public function moveCommandUp(Uuid $uuid)
	{
		if ($this->isCommandInQueue($uuid)) {
			$queue = $this->queueRepository->loadQueue();
			$this->moveUp($queue, $uuid);
			$this->queueRepository->saveQueue($queue);
			return;
		}
		if ($this->isCommandInRepetitive($uuid)) {
			$queue = $this->queueRepository->loadRepetitiveCommands();
			$this->moveUp($queue, $uuid);
			$this->queueRepository->saveRepetitiveCommands($queue);
			return;
		}
	}

	public function moveCommandDown(Uuid $uuid)
	{
		if ($this->isCommandInQueue($uuid)) {
			$queue = $this->queueRepository->loadQueue();
			$this->moveDown($queue, $uuid);
			$this->queueRepository->saveQueue($queue);
			return;
		}
		if ($this->isCommandInRepetitive($uuid)) {
			$queue = $this->queueRepository->loadRepetitiveCommands();
			$this->moveDown($queue, $uuid);
			$this->queueRepository->saveRepetitiveCommands($queue);
			return;
		}
	}

	public function disableCommand(Uuid $uuid)
	{
		$command = $this->getCommand($uuid);
		$command->setDisabled(true);
		if ($this->isCommandInQueue($command->getUuid())) {
			$queue = $this->queueRepository->loadQueue();
			$this->updateCommand($queue, $command);
			$this->queueRepository->saveQueue($queue);
			return;
		}
		if ($this->isCommandInRepetitive($command->getUuid())) {
			$queue = $this->queueRepository->loadRepetitiveCommands();
			$this->updateCommand($queue, $command);
			$this->queueRepository->saveRepetitiveCommands($queue);
			return;
		}
	}

	public function enableCommand(Uuid $uuid)
	{
		$command = $this->getCommand($uuid);
		$command->setDisabled(false);
		if ($this->isCommandInQueue($command->getUuid())) {
			$queue = $this->queueRepository->loadQueue();
			$this->updateCommand($queue, $command);
			$this->queueRepository->saveQueue($queue);
			return;
		}
		if ($this->isCommandInRepetitive($command->getUuid())) {
			$queue = $this->queueRepository->loadRepetitiveCommands();
			$this->updateCommand($queue, $command);
			$this->queueRepository->saveRepetitiveCommands($queue);
			return;
		}
	}

	private function moveUp(ArrayCollection $queue, Uuid $uuid)
	{
		foreach ($queue as $key => $item) {
			if ($item->getUuid()->equals($uuid) && $key !== 0) {
				$temp = $queue[$key];
				$queue[$key] = $queue[$key - 1];
				$queue[$key - 1] = $temp;
				break;
			}
		}
	}

	private function moveDown(ArrayCollection $queue, Uuid $uuid)
	{
		foreach ($queue as $key => $item) {
			if ($item->getUuid()->equals($uuid) && $key !== count($queue) - 1) {
				$temp = $queue[$key];
				$queue[$key] = $queue[$key + 1];
				$queue[$key + 1] = $temp;
				break;
			}
		}
	}

	/**
	 * @return ICommand[]|ArrayCollection
	 */
	public function getQueue() : ArrayCollection
	{
		return $this->queueRepository->loadQueue();
	}

	/**
	 * @return ICommand[]|ArrayCollection
	 */
	public function getRepetitiveCommands() : ArrayCollection
	{
		return $this->queueRepository->loadRepetitiveCommands();
	}

	public function addBefore(ICommand $command, Uuid $uuid)
	{
		$queue = $this->queueRepository->loadQueue();
		$key = $queue->indexOf($queue->filter(Functions::hasCommandUuid($uuid))->first());
		$queue->addBefore($command, $key);
		$this->queueRepository->saveQueue($queue);
	}

	public function getCommand(Uuid $uuid) : ICommand
	{
		$commands = $this->queueRepository->loadQueue()->merge($this->queueRepository->loadRepetitiveCommands());
		return $commands->filter(Functions::hasCommandUuid($uuid))->first();
	}

	public function saveCommand(ICommand $command)
	{
		if ($this->isCommandInQueue($command->getUuid())) {
			$queue = $this->queueRepository->loadQueue();
			$this->updateCommand($queue, $command);
			$this->queueRepository->saveQueue($queue);
			return;
		}
		if ($this->isCommandInRepetitive($command->getUuid())) {
			$queue = $this->queueRepository->loadRepetitiveCommands();
			$this->updateCommand($queue, $command);
			$this->queueRepository->saveRepetitiveCommands($queue);
			return;
		}
	}

	private function isCommandInQueue(Uuid $uuid) : bool
	{
		return ! $this->queueRepository->loadQueue()->filter(Functions::hasCommandUuid($uuid))->isEmpty();
	}

	private function isCommandInRepetitive(Uuid $uuid) : bool
	{
		return ! $this->queueRepository->loadRepetitiveCommands()->filter(Functions::hasCommandUuid($uuid))->isEmpty();
	}

	private function updateCommand(ArrayCollection $commands, ICommand $newCommand)
	{
		$key = $commands->indexOf($commands->filter(Functions::hasCommandUuid($newCommand->getUuid()))->first());
		$commands->set($key, $newCommand);
	}
	
}