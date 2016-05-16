<?php

namespace App\Model\Queue;
 
use App\Model\CronManager;
use App\Model\Game\BuildManager;
use App\Model\Game\UpgradeManager;
use App\Model\Game\PlanetManager;
use App\Model\Queue\Command\ICommand;
use App\Model\ResourcesCalculator;
use App\Utils\Functions;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
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

	/** @var QueueManager */
	private $queueManager;

	/** @var Logger */
	private $logger;

	public function __construct(QueueManager $queueManager, UpgradeManager $upgradeManager, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator, CronManager $cronManager, BuildManager $buildManager, Logger $logger)
	{
		$this->queueManager = $queueManager;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->cronManager = $cronManager;
		$this->processors = [
			$upgradeManager,
			$buildManager
		];
		$this->logger = $logger;
	}

	public function processQueue()
	{
		$this->planetManager->refreshAllData();
		$queue = $this->queueManager->getQueue();
		$planetsQueue = [];
		foreach ($queue as $command) {
			$coordinatesString = $command->getCoordinates()->__toString();
			if (!array_key_exists($coordinatesString, $planetsQueue)) {
				$planetsQueue[$coordinatesString] = [];
			}
			$planetsQueue[$coordinatesString][] = $command;
		}
		$failedCommands = [];
		foreach ($planetsQueue as $planetCoordinates => $queue) {
			$success = true;    //aby se zastavilo procházení fronty, když se nepodaří postavit budovu a zpracování tak skončilo
			/** @var ICommand $command */
			foreach ($queue as $key => $command) {
				foreach ($this->processors as $processor) {
					if ($processor->canProcessCommand($command)) {
						$this->logger->addDebug("Going to process the command {$command->__toString()}.");
						$success = $processor->processCommand($command);
						$this->planetManager->refreshAllResourcesData();
						break;
					}
				}
				if ($success) {
					$this->logger->addDebug("Command processed successfully.");
					$this->queueManager->removeFromQueue($command->getUuid());
				} else {
					$this->logger->addDebug("Command failed to process.");
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
						$this->logger->addDebug("Going to find the next run of command {$failedCommand->__toString()}.");
						$datetime = $processor->getTimeToProcessingAvailable($failedCommand);
						$this->logger->addDebug("Next run of command {$failedCommand->__toString()} is {$datetime->__toString()}.");
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
