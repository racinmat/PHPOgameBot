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
		foreach ($dependencyTypes as $planetCoordinates => $queue) {
			/** @var ICommand $command */
			while(!$queue->isEmpty()) {
				$command = $queue->first();
				$this->commandDispatcher->preProcessCommand($command, $queue);
				$command = $queue->first();     //because preprocessor modifies the queue
				$success = $this->commandDispatcher->processCommand($command);

				if ($success) {
					$this->logger->addInfo("Command processed successfully. Removing command from queue.");
					$this->queueManager->removeCommand($command->getUuid());
					$queue->remove(0);
				} else {
					$this->logger->addInfo("Command failed to process.");
					$failedCommands->add($command);
					break;
				}
			}
		}

		//repetitive commands
		$repetitiveCommands = $this->queueManager->getRepetitiveCommands();
		foreach ($repetitiveCommands as $repetitiveCommand) {
			$this->commandDispatcher->processCommand($repetitiveCommand);
		}

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
