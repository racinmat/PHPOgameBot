<?php

namespace App\Model\Queue;
 
use App\Model\UpgradeStoragesPreProcessor;
use App\Model\CronManager;
use App\Model\Game\BuildManager;
use App\Model\Game\GalaxyBrowser;
use App\Model\Game\UpgradeManager;
use App\Model\Game\PlanetManager;
use App\Model\Queue\Command\ICommand;
use App\Model\ResourcesCalculator;
use App\Utils\ArrayCollection;
use App\Utils\ChangesAwareCollection;
use App\Utils\Functions;
use Kdyby\Monolog\Logger;
use Nette\Object;

class QueueConsumer extends Object
{

	/** @var PlanetManager */
	private $planetManager;

	/** @var ResourcesCalculator */
	private $resourcesCalculator;

	/** @var CronManager */
	private $cronManager;

	/** @var ICommandProcessor[] */
	private $processors;

	/** @var ICommandPreProcessor[] */
	private $preprocessors;

	/** @var QueueManager */
	private $queueManager;

	/** @var Logger */
	private $logger;

	public function __construct(QueueManager $queueManager, UpgradeManager $upgradeManager, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator, CronManager $cronManager, BuildManager $buildManager, Logger $logger, GalaxyBrowser $galaxyBrowser, UpgradeStoragesPreProcessor $buildStoragesPreProcessor)
	{
		$this->queueManager = $queueManager;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->cronManager = $cronManager;
		$this->processors = [
			$upgradeManager,
			$buildManager,
			$galaxyBrowser
		];
		$this->logger = $logger;
		$this->preprocessors = [
			$buildStoragesPreProcessor
		];
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
		$failedCommands = [];
		foreach ($dependencyTypes as $planetCoordinates => $queue) {
			$success = true;    //aby se zastavilo procházení fronty, když se nepodaří vykonat příkaz a zpracování tak skončilo
			/** @var ICommand $command */
			while(!$queue->isEmpty()) {
				$command = $queue->first();
				foreach ($this->preprocessors as $preprocessor) {
					if ($preprocessor->canPreProcessCommand($command)) {
						$this->logger->addInfo("Going to preProcess the command {$command->__toString()}.");
						$preprocessor->preProcessCommand($command, $queue);
						$command = $queue->first();
						break;
					}
				}

				foreach ($this->processors as $processor) {
					if ($processor->canProcessCommand($command)) {
						$this->logger->addInfo("Going to process the command {$command->__toString()}.");
						$success = $processor->processCommand($command);
						$this->planetManager->refreshAllResourcesData();
						break;
					}
				}

				if ($success) {
					$this->logger->addInfo("Command processed successfully.");
					$this->queueManager->removeFromQueue($command->getUuid());
					$queue->remove(0);
				} else {
					$this->logger->addInfo("Command failed to process.");
					$failedCommands[] = $command;
					break;
				}
			}
		}

		if (count($failedCommands) > 0) {
			$nextStarts = [];
			/** @var ICommand $failedCommand */
			foreach ($failedCommands as $failedCommand) {
				foreach ($this->processors as $processor) {
					if ($processor->canProcessCommand($failedCommand)) {
						$this->logger->addInfo("Going to find the next run of command {$failedCommand->__toString()}.");
						$datetime = $processor->getTimeToProcessingAvailable($failedCommand);
						$this->logger->addInfo("Next run of command {$failedCommand->__toString()} is {$datetime->__toString()}.");
						$nextStarts[] = $datetime;
						break;
					}
				}
			}

			usort($nextStarts, Functions::compareCarbonDateTimes());
			$this->logger->addDebug("Nearest next run is {$nextStarts[0]->__toString()}.");
			$this->cronManager->setNextStart($nextStarts[0]);
		}
	}

}
