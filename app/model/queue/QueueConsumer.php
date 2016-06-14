<?php

namespace App\Model\Queue;

use App\Model\CronManager;
use App\Model\Game\PlanetManager;
use App\Model\Queue\Command\ICommand;
use App\Utils\ArrayCollection;
use App\Utils\Functions;
use Carbon\Carbon;
use Kdyby\Monolog\Logger;
use Nette\Object;
use Nette\Utils\Arrays;
use Nette\Utils\Json;

class QueueConsumer extends Object
{

	/** @var PlanetManager */
	private $planetManager;

	/** @var CronManager */
	private $cronManager;

	/** @var QueueManager */
	private $queueManager;

	/** @var Logger */
	private $logger;

	/** @var CommandDispatcher */
	private $commandDispatcher;

	public function __construct(QueueManager $queueManager, PlanetManager $planetManager, CronManager $cronManager, Logger $logger, CommandDispatcher $commandDispatcher)
	{
		$this->queueManager = $queueManager;
		$this->planetManager = $planetManager;
		$this->cronManager = $cronManager;
		$this->logger = $logger;
		$this->commandDispatcher = $commandDispatcher;
	}

	public function processQueue()
	{
		$this->planetManager->refreshAllData();
		$queue = $this->queueManager->getQueue();
		/** @var ArrayCollection[] $dependencyTypes */
		$dependencyTypes = [];
		foreach ($queue as $command) {
			$dependencyType = $command->getDependencyType();
			if (!isset($dependencyTypes[$dependencyType])) {
				$dependencyTypes[$dependencyType] = new ArrayCollection();
			}
			$dependencyTypes[$dependencyType]->add($command);
		}
		$failedCommands = new ArrayCollection();

		$this->logger->addInfo("Commands categorized to types.");
		foreach ($dependencyTypes as $dependencyType => $queue) {
			$this->logger->addDebug("Uuids in queue for dependency type $dependencyType: " . Json::encode($queue->map(Functions::commandToUuidString())->toArray()));
		}

		$this->logger->addInfo("Starting to process queue.");
		foreach ($dependencyTypes as $dependencyType => $queue) {
			/** @var ICommand $command */
			while(!$queue->isEmpty()) {
				$command = $queue->first();
				$this->commandDispatcher->preProcessCommand($command, $queue);
				$command = $queue->first();     //because preprocessor modifies the queue
				$this->logger->addInfo("Going to process the command {$command->getUuid()}. After its preprocessing there are {$queue->count()} commands remaining in queue for dependency type $dependencyType.");
				$success = $this->commandDispatcher->processCommand($command);

				if ($success) {
					$this->logger->addInfo("Command processed successfully. Removing command from queue.");
					$this->queueManager->removeCommand($command->getUuid());
					$queue->removeFirst();
				} else {
					$this->logger->addInfo("Command failed to process.");
					$failedCommands->add($command);
					break;
				}
				$this->logger->addInfo("{$queue->count()} commands remaining in queue for dependency type $dependencyType.");
				$this->logger->addInfo("Uuids of these commands are: " . Json::encode($queue->map(Functions::commandToUuidString())->toArray()));
			}
		}

		$this->logger->addInfo("Queue processed. Starting to process repetitive commands.");
		//repetitive commands
		$repetitiveCommands = $this->queueManager->getRepetitiveCommands();
		foreach ($repetitiveCommands as $repetitiveCommand) {
			$this->commandDispatcher->processCommand($repetitiveCommand);
		}

		$this->logger->addInfo("Repetitive commands processed.");
		$this->resolveTimeOfNextRun($failedCommands->merge($repetitiveCommands));
	}

	private function resolveTimeOfNextRun(ArrayCollection $commands)
	{
		if ($commands->isEmpty()) {
			return;
		}
		/** @var Carbon[] $nextStarts */
		$nextStarts = [];
		/** @var ICommand $command */
		foreach ($commands as $command) {
			$nextStarts[] = $this->commandDispatcher->getTimeToProcessingAvailable($command);
		}
		usort($nextStarts, Functions::compareCarbonDateTimes());
		$nextStart = $nextStarts[0];
		$this->logger->addDebug("Nearest next run is {$nextStart->__toString()}.");
		$this->cronManager->setNextStart($nextStart);
	}

}
